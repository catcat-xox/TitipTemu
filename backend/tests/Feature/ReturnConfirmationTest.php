<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\ReturnConfirmation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturnConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_submit_confirmation_and_report_becomes_return_pending(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->approved()->create(['item_name' => 'Dompet Kembali']);

        $this->actingAs($owner)->get(route('reports.return.create', $report))
            ->assertOk()
            ->assertSee('Konfirmasi barang telah kembali');

        $this->actingAs($owner)->post(route('reports.return.store', $report), [
            'proof' => [
                UploadedFile::fake()->image('bukti-satu.jpg'),
                UploadedFile::fake()->image('bukti-dua.png'),
            ],
            'note' => 'Diterima di gedung A.',
            'return_statement' => '1',
        ])->assertRedirect(route('reports.mine'));

        $confirmation = ReturnConfirmation::firstOrFail();
        $this->assertSame('pending', $confirmation->status);
        $this->assertSame(1, $confirmation->attempt);
        $this->assertCount(2, $confirmation->proof_paths);
        foreach ($confirmation->proof_paths as $path) {
            Storage::disk('local')->assertExists($path);
        }

        $this->assertSame('return_pending', $report->fresh()->status);
        $this->assertDatabaseHas('report_status_histories', [
            'lost_report_id' => $report->id,
            'actor_id' => $owner->id,
            'action' => 'return_submitted',
            'from_status' => 'approved',
            'to_status' => 'return_pending',
        ]);

        $this->get(route('reports.index', 'lost'))
            ->assertOk()
            ->assertSee('Dompet Kembali');
        $this->get(route('reports.index', 'found'))
            ->assertOk()
            ->assertDontSee('Dompet Kembali');
    }

    public function test_confirmation_access_and_upload_rules_are_enforced(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $unverifiedOwner = User::factory()->unverified()->create();
        $report = LostReport::factory()->for($owner)->approved()->create();
        $unverifiedReport = LostReport::factory()->for($unverifiedOwner)->approved()->create();

        $this->get(route('reports.return.create', $report))->assertRedirect(route('login'));
        $this->actingAs($other)->get(route('reports.return.create', $report))->assertForbidden();
        $this->actingAs($admin)->get(route('reports.return.create', $report))->assertForbidden();
        $this->actingAs($unverifiedOwner)->get(route('reports.return.create', $unverifiedReport))->assertForbidden();

        $this->actingAs($owner)->post(route('reports.return.store', $report), [])
            ->assertSessionHasErrors(['proof', 'return_statement']);

        $tooManyProofs = [];
        for ($index = 0; $index < 4; $index++) {
            $tooManyProofs[] = UploadedFile::fake()->image("bukti-{$index}.jpg");
        }
        $this->actingAs($owner)->post(route('reports.return.store', $report), [
            'proof' => $tooManyProofs,
            'return_statement' => '1',
        ])->assertSessionHasErrors('proof');

        $this->actingAs($owner)->post(route('reports.return.store', $report), [
            'proof' => [UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf')],
            'return_statement' => '1',
        ])->assertSessionHasErrors('proof.0');

        $this->assertSame(0, ReturnConfirmation::count());
        $this->assertSame('approved', $report->fresh()->status);
    }

    public function test_return_proof_is_private_to_admin(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('return-proofs/bukti.jpg', 'konten-bukti');
        $confirmation = ReturnConfirmation::factory()->create([
            'proof_paths' => ['return-proofs/bukti.jpg'],
        ]);
        $admin = User::factory()->admin()->create();

        $this->get(route('admin.returns.proof', $confirmation))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('admin.returns.proof', $confirmation))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.returns.proof', $confirmation))->assertOk();
        $this->actingAs($admin)->get(route('admin.returns.proof', [$confirmation, 'index' => 9]))->assertNotFound();
    }

    public function test_admin_can_approve_confirmation_and_report_appears_on_found_list(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $report = LostReport::factory()->for($owner)->approved()->create(['item_name' => 'Sepatu Kembali']);
        $report->update(['status' => 'return_pending']);
        $confirmation = ReturnConfirmation::factory()->create([
            'lost_report_id' => $report->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($admin)->post(route('admin.returns.approve', $confirmation))
            ->assertRedirect(route('admin.dashboard', ['status' => 'return_pending']));

        $this->assertSame('approved', $confirmation->fresh()->status);
        $this->assertSame($admin->id, $confirmation->fresh()->admin_id);
        $this->assertNotNull($confirmation->fresh()->reviewed_at);
        $this->assertSame('returned', $report->fresh()->status);
        $this->assertNotNull($report->fresh()->returned_at);
        $this->assertDatabaseHas('report_status_histories', [
            'lost_report_id' => $report->id,
            'actor_id' => $admin->id,
            'action' => 'return_approved',
            'from_status' => 'return_pending',
            'to_status' => 'returned',
        ]);

        $this->get(route('reports.index', 'found'))
            ->assertOk()
            ->assertSee('Sepatu Kembali');
        $this->get(route('reports.index', 'lost'))
            ->assertOk()
            ->assertDontSee('Sepatu Kembali');
    }

    public function test_admin_can_reject_confirmation_and_owner_can_resubmit(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $report = LostReport::factory()->for($owner)->approved()->create(['item_name' => 'Tas Kembali']);
        $report->update(['status' => 'return_pending']);
        $confirmation = ReturnConfirmation::factory()->create([
            'lost_report_id' => $report->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($admin)->post(route('admin.returns.reject', $confirmation), [])
            ->assertSessionHasErrors('reason');
        $this->assertSame('pending', $confirmation->fresh()->status);

        $this->actingAs($admin)->post(route('admin.returns.reject', $confirmation), [
            'reason' => 'Foto bukti belum jelas.',
        ])->assertRedirect(route('admin.dashboard', ['status' => 'return_pending']));

        $this->assertSame('rejected', $confirmation->fresh()->status);
        $this->assertSame('Foto bukti belum jelas.', $confirmation->fresh()->rejection_reason);
        $this->assertSame('approved', $report->fresh()->status);
        $this->assertDatabaseHas('report_status_histories', [
            'lost_report_id' => $report->id,
            'actor_id' => $admin->id,
            'action' => 'return_rejected',
            'from_status' => 'return_pending',
            'to_status' => 'approved',
            'reason' => 'Foto bukti belum jelas.',
        ]);

        $this->actingAs($owner)->get(route('reports.mine'))
            ->assertOk()
            ->assertSee('Bukti ditolak: Foto bukti belum jelas.');

        $this->actingAs($owner)->post(route('reports.return.store', $report), [
            'proof' => [UploadedFile::fake()->image('bukti-baru.jpg')],
            'note' => 'Sudah diambil pemilik.',
            'return_statement' => '1',
        ])->assertRedirect(route('reports.mine'));

        $this->assertSame(2, (int) ReturnConfirmation::max('attempt'));
        $this->assertSame('return_pending', $report->fresh()->status);
    }

    public function test_three_rejections_limit_resubmission_and_allow_admin_resolution(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $report = LostReport::factory()->for($owner)->approved()->create(['item_name' => 'Dompet Tiga Kali']);

        foreach ([1, 2, 3] as $attempt) {
            ReturnConfirmation::factory()->rejected()->create([
                'lost_report_id' => $report->id,
                'user_id' => $owner->id,
                'attempt' => $attempt,
                'proof_paths' => ["return-proofs/bukti-{$attempt}.jpg"],
            ]);
        }

        $this->actingAs($owner)->get(route('reports.return.create', $report))
            ->assertOk()
            ->assertSee('Batas pengiriman tercapai');
        $this->actingAs($owner)->post(route('reports.return.store', $report), [
            'proof' => [UploadedFile::fake()->image('bukti-empat.jpg')],
            'return_statement' => '1',
        ])->assertForbidden();
        $this->assertSame(3, ReturnConfirmation::count());

        $this->actingAs($admin)->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertSee('Selesaikan melalui Admin');

        $this->actingAs($admin)->post(route('admin.reports.resolve-return', $report), ['note' => 'ok'])
            ->assertSessionHasErrors('note');
        $this->actingAs($admin)->post(route('admin.reports.resolve-return', $report), [
            'note' => 'Admin menyetujui pengembalian.',
        ])->assertRedirect(route('admin.dashboard', ['status' => 'returned']));

        $this->assertSame('returned', $report->fresh()->status);
        $resolution = ReturnConfirmation::latest('id')->firstOrFail();
        $this->assertTrue($resolution->is_admin_resolution);
        $this->assertSame('approved', $resolution->status);
        $this->assertSame($admin->id, $resolution->admin_id);
        $this->assertSame('Admin menyetujui pengembalian.', $resolution->admin_note);
        $this->assertDatabaseHas('report_status_histories', [
            'lost_report_id' => $report->id,
            'actor_id' => $admin->id,
            'action' => 'return_resolved_by_admin',
            'from_status' => 'approved',
            'to_status' => 'returned',
        ]);
    }

    public function test_regular_user_cannot_manage_return_confirmations(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->approved()->create();
        $report->update(['status' => 'return_pending']);
        $confirmation = ReturnConfirmation::factory()->create([
            'lost_report_id' => $report->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($user)->post(route('admin.returns.approve', $confirmation))->assertForbidden();
        $this->actingAs($user)->post(route('admin.returns.reject', $confirmation), [
            'reason' => 'Alasan pengujian.',
        ])->assertForbidden();
        $this->actingAs($user)->post(route('admin.reports.resolve-return', $report), [
            'note' => 'Catatan pengujian.',
        ])->assertForbidden();

        $this->assertSame('pending', $confirmation->fresh()->status);
        $this->assertSame('return_pending', $report->fresh()->status);
    }
}
