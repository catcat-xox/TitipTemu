<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\MissingReportStatusHistory;
use App\Models\ReportStatusHistory;
use App\Models\ReturnConfirmation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminReportEditDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_user_cannot_edit_or_delete_reports(): void
    {
        $report = LostReport::factory()->create();
        $user = User::factory()->create();

        $this->get(route('admin.reports.edit', $report))->assertRedirect(route('admin.login'));
        $this->put(route('admin.reports.update', $report), [])->assertRedirect(route('admin.login'));
        $this->delete(route('admin.reports.destroy', $report))->assertRedirect(route('admin.login'));

        $this->actingAs($user)->get(route('admin.reports.edit', $report))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.reports.update', $report), [])->assertRedirect(route('admin.login'));
        $this->actingAs($user)->delete(route('admin.reports.destroy', $report))->assertRedirect(route('admin.login'));

        $this->assertSame('pending', $report->fresh()->status);
    }

    public function test_admin_can_edit_pending_report_and_change_is_audited(): void
    {
        $admin = Admin::factory()->create();
        $report = LostReport::factory()->create([
            'item_name' => 'Nama Lama',
            'contact_phone' => '081200000000',
        ]);

        $this->actingAs($admin, 'admin')->get(route('admin.reports.edit', $report))
            ->assertOk()
            ->assertSee('Edit laporan yang menunggu pemeriksaan');

        $this->actingAs($admin, 'admin')->put(route('admin.reports.update', $report), $this->payload([
            'item_name' => 'Nama Baru',
            'contact_phone' => '081299988877',
        ]))->assertRedirect(route('admin.reports.show', $report));

        $report->refresh();
        $this->assertSame('Nama Baru', $report->item_name);
        $this->assertSame('081299988877', $report->contact_phone);
        $this->assertSame('pending', $report->status);
        $this->assertDatabaseHas('report_status_histories', [
            'lost_report_id' => $report->id,
            'actor_id' => null,
            'admin_id' => $admin->id,
            'action' => 'admin_edited',
            'from_status' => 'pending',
            'to_status' => 'pending',
        ]);
    }

    public function test_admin_cannot_directly_edit_non_pending_report(): void
    {
        $admin = Admin::factory()->create();
        $report = LostReport::factory()->approved()->create(['item_name' => 'Sudah Publik']);

        $this->actingAs($admin, 'admin')->get(route('admin.reports.edit', $report))->assertForbidden();
        $this->actingAs($admin, 'admin')->put(route('admin.reports.update', $report), $this->payload())
            ->assertForbidden();

        $this->assertSame('Sudah Publik', $report->fresh()->item_name);
    }

    public function test_admin_cannot_delete_pending_or_approved_report(): void
    {
        $admin = Admin::factory()->create();
        $pending = LostReport::factory()->create();
        $approved = LostReport::factory()->approved()->create();

        $this->actingAs($admin, 'admin')->delete(route('admin.reports.destroy', $pending))->assertForbidden();
        $this->actingAs($admin, 'admin')->delete(route('admin.reports.destroy', $approved))->assertForbidden();

        $this->assertDatabaseHas('lost_reports', ['id' => $pending->id]);
        $this->assertDatabaseHas('lost_reports', ['id' => $approved->id]);
    }

    public function test_admin_can_permanently_delete_returned_report_and_its_private_proof(): void
    {
        Storage::fake('local');
        $admin = Admin::factory()->create();
        $report = LostReport::factory()->returnedAt(now())->create(['item_name' => 'Barang Selesai']);
        $confirmation = ReturnConfirmation::factory()->create([
            'lost_report_id' => $report->id,
            'proof_paths' => ['return-proofs/bukti.jpg'],
        ]);
        Storage::disk('local')->put('return-proofs/bukti.jpg', 'bukti-privat');

        $this->actingAs($admin, 'admin')->delete(route('admin.reports.destroy', $report))
            ->assertRedirect(route('admin.dashboard', ['jenis' => 'found', 'status' => 'returned']))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('lost_reports', ['id' => $report->id]);
        $this->assertDatabaseMissing('return_confirmations', ['id' => $confirmation->id]);
        $this->assertSame(0, ReportStatusHistory::where('lost_report_id', $report->id)->count());
        Storage::disk('local')->assertMissing('return-proofs/bukti.jpg');
    }

    public function test_guest_and_user_cannot_edit_or_delete_missing_reports(): void
    {
        $report = MissingReport::factory()->create();
        $user = User::factory()->create();

        $this->get(route('admin.reports.missing.edit', $report))->assertRedirect(route('admin.login'));
        $this->put(route('admin.reports.missing.update', $report), [])->assertRedirect(route('admin.login'));
        $this->delete(route('admin.reports.missing.destroy', $report))->assertRedirect(route('admin.login'));

        $this->actingAs($user)->get(route('admin.reports.missing.edit', $report))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.reports.missing.update', $report), [])->assertRedirect(route('admin.login'));
        $this->actingAs($user)->delete(route('admin.reports.missing.destroy', $report))->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('missing_reports', ['id' => $report->id]);
    }

    public function test_admin_can_edit_missing_report_in_every_status_and_audit_the_original_status(): void
    {
        $admin = Admin::factory()->create();

        foreach ($this->missingStatuses() as $status) {
            $report = MissingReport::factory()->create([
                'status' => $status,
                'item_name' => 'Nama lama '.$status,
            ]);

            $this->actingAs($admin, 'admin')->get(route('admin.reports.missing.edit', $report))
                ->assertOk()
                ->assertSee('Edit laporan kehilangan');

            $this->actingAs($admin, 'admin')->put(route('admin.reports.missing.update', $report), $this->payload([
                'item_name' => 'Nama baru '.$status,
            ]))->assertRedirect(route('admin.reports.missing.show', $report));

            $this->assertSame('Nama baru '.$status, $report->fresh()->item_name);
            $this->assertSame($status, $report->fresh()->status);
            $this->assertDatabaseHas('missing_report_status_histories', [
                'missing_report_id' => $report->id,
                'actor_id' => null,
                'admin_id' => $admin->id,
                'action' => 'admin_edited',
                'from_status' => $status,
                'to_status' => $status,
            ]);
        }
    }

    public function test_admin_can_delete_missing_report_in_every_status_with_photo_and_history(): void
    {
        Storage::fake('local');
        $admin = Admin::factory()->create();

        foreach ($this->missingStatuses() as $status) {
            $photoPath = 'missing-reports/hapus-'.$status.'.jpg';
            $report = MissingReport::factory()->create([
                'status' => $status,
                'photo_path' => $photoPath,
            ]);
            Storage::disk('local')->put($photoPath, 'foto-privat-'.$status);
            MissingReportStatusHistory::create([
                'missing_report_id' => $report->id,
                'actor_id' => null,
                'admin_id' => $admin->id,
                'action' => 'admin_edited',
                'from_status' => $status,
                'to_status' => $status,
            ]);

            $this->actingAs($admin, 'admin')->delete(route('admin.reports.missing.destroy', $report))
                ->assertRedirect(route('admin.dashboard', ['jenis' => 'lost', 'status' => 'all']))
                ->assertSessionHas('success');

            $this->assertDatabaseMissing('missing_reports', ['id' => $report->id]);
            $this->assertSame(0, MissingReportStatusHistory::where('missing_report_id', $report->id)->count());
            Storage::disk('local')->assertMissing($photoPath);
        }
    }

    public function test_admin_review_page_always_offers_edit_and_delete_for_missing_reports(): void
    {
        $admin = Admin::factory()->create();

        foreach ($this->missingStatuses() as $status) {
            $report = MissingReport::factory()->create(['status' => $status]);

            $html = $this->actingAs($admin, 'admin')->get(route('admin.reports.missing.show', $report))
                ->assertOk()
                ->getContent();

            $this->assertStringContainsString(route('admin.reports.missing.edit', $report), $html);
            $this->assertStringContainsString(route('admin.reports.missing.destroy', $report), $html);
            $this->assertStringContainsString('Hapus laporan', $html);
            $this->assertStringContainsString('Edit laporan', $html);

            if ($status === 'pending') {
                $this->assertStringContainsString('Keputusan verifikasi', $html);
            } else {
                $this->assertStringContainsString(
                    'Penemuan barang dikonfirmasi langsung oleh pemilik tanpa verifikasi tambahan.',
                    $html
                );
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function missingStatuses(): array
    {
        return [
            MissingReport::STATUS_PENDING,
            MissingReport::STATUS_APPROVED,
            MissingReport::STATUS_REJECTED,
            MissingReport::STATUS_FOUND_PENDING,
            MissingReport::STATUS_FOUND,
            MissingReport::STATUS_ARCHIVED,
            MissingReport::STATUS_CANCELLED,
        ];
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'item_name' => 'Kartu Mahasiswa',
            'category' => 'Dokumen',
            'description' => 'Kartu mahasiswa ditemukan di sekitar gedung kuliah.',
            'lost_at' => now()->subDay()->toDateString(),
            'location' => 'Gedung Kuliah',
            'contact_phone' => '081234567890',
        ], $overrides);
    }
}
