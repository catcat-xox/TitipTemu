<?php

namespace Tests\Feature;

use App\Models\LostReport;
use App\Models\ReportStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminReportModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_regular_user_cannot_access_admin_moderation(): void
    {
        $report = LostReport::factory()->create();

        $this->get(route('admin.reports.show', $report))->assertRedirect(route('login'));
        $this->get(route('admin.reports.photo', $report))->assertRedirect(route('login'));
        $this->post(route('admin.reports.moderate', $report), ['decision' => 'approve'])->assertRedirect(route('login'));

        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.reports.show', $report))->assertForbidden();
        $this->actingAs($user)->get(route('admin.reports.photo', $report))->assertForbidden();
        $this->actingAs($user)->post(route('admin.reports.moderate', $report), ['decision' => 'approve'])->assertForbidden();
    }

    public function test_admin_can_open_detail_and_history_without_private_owner_fields(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create([
            'name' => 'Pemilik Detail',
            'email' => 'privat-detail@example.test',
            'nim_nip' => 'NIM-DETAIL',
        ]);
        $report = LostReport::factory()->for($owner)->create(['item_name' => 'Kunci Detail']);

        $this->actingAs($admin)->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertSee('Kunci Detail')
            ->assertSee('Pemilik Detail')
            ->assertDontSee('privat-detail@example.test')
            ->assertDontSee('NIM-DETAIL');
    }

    public function test_admin_can_preview_private_photo_and_guest_cannot(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();
        $path = Storage::disk('local')->putFile('lost-reports', UploadedFile::fake()->image('foto.jpg'));
        $report = LostReport::factory()->create(['photo_path' => $path]);

        $this->get(route('admin.reports.photo', $report))->assertRedirect(route('login'));
        $this->actingAs($admin)->get(route('admin.reports.photo', $report))->assertOk();
    }

    public function test_public_photo_is_only_available_for_public_reports(): void
    {
        Storage::fake('local');
        $path = Storage::disk('local')->putFile('lost-reports', UploadedFile::fake()->image('foto.jpg'));
        $pending = LostReport::factory()->create(['photo_path' => $path]);
        $approved = LostReport::factory()->approved()->create(['photo_path' => $path]);

        $this->get(route('reports.photo', $pending))->assertNotFound();
        $this->get(route('reports.photo', $approved))->assertOk();
    }

    public function test_admin_can_approve_pending_report_and_history_is_recorded(): void
    {
        $admin = User::factory()->admin()->create();
        $report = LostReport::factory()->create();

        $this->actingAs($admin)->post(route('admin.reports.moderate', $report), ['decision' => 'approve'])
            ->assertRedirect(route('admin.dashboard', ['status' => 'approved']));

        $this->assertSame('approved', $report->fresh()->status);
        $this->assertDatabaseHas('report_status_histories', [
            'lost_report_id' => $report->id,
            'actor_id' => $admin->id,
            'action' => 'approve',
            'from_status' => 'pending',
            'to_status' => 'approved',
        ]);
    }

    public function test_admin_can_reject_pending_report_with_required_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $report = LostReport::factory()->create(['item_name' => 'Kunci Ditolak']);

        $this->actingAs($admin)->post(route('admin.reports.moderate', $report), ['decision' => 'reject'])
            ->assertSessionHasErrors('reason');
        $this->assertSame('pending', $report->fresh()->status);
        $this->assertSame(0, ReportStatusHistory::count());

        $this->actingAs($admin)->post(route('admin.reports.moderate', $report), [
            'decision' => 'reject',
            'reason' => 'Foto barang kurang jelas.',
            'note' => 'Catatan internal',
        ])->assertRedirect(route('admin.dashboard', ['status' => 'rejected']));

        $this->assertSame('rejected', $report->fresh()->status);
        $this->assertDatabaseHas('report_status_histories', [
            'lost_report_id' => $report->id,
            'actor_id' => $admin->id,
            'action' => 'reject',
            'from_status' => 'pending',
            'to_status' => 'rejected',
            'reason' => 'Foto barang kurang jelas.',
            'note' => 'Catatan internal',
        ]);

        $this->actingAs($admin)->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertSee('Foto barang kurang jelas.')
            ->assertSee('Catatan internal');
    }

    public function test_non_pending_report_cannot_be_moderated_again(): void
    {
        $admin = User::factory()->admin()->create();
        $report = LostReport::factory()->approved()->create();

        $this->actingAs($admin)->post(route('admin.reports.moderate', $report), [
            'decision' => 'reject',
            'reason' => 'Alasan pengujian.',
        ])->assertForbidden();

        $this->assertSame('approved', $report->fresh()->status);
        $this->assertSame(0, ReportStatusHistory::count());
    }

    public function test_owner_sees_rejection_reason_and_can_resubmit(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $report = LostReport::factory()->for($owner)->create();

        $this->actingAs($admin)->post(route('admin.reports.moderate', $report), [
            'decision' => 'reject',
            'reason' => 'Deskripsi perlu diperjelas.',
        ])->assertRedirect(route('admin.dashboard', ['status' => 'rejected']));

        $this->actingAs($owner)->get(route('reports.mine'))
            ->assertOk()
            ->assertSee('Alasan Admin: Deskripsi perlu diperjelas.');

        $this->actingAs($owner)->post(route('reports.resubmit', $report))
            ->assertRedirect(route('reports.mine'));

        $this->assertSame('pending', $report->fresh()->status);
    }
}
