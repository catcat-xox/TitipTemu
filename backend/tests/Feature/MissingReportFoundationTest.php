<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\MissingReport;
use App\Models\MissingReportStatusHistory;
use App\Models\ReportStatusHistory;
use App\Services\MissingReportArchiveService;
use App\Services\MissingReportModerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MissingReportFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_missing_reports_have_a_separate_table_without_link_columns(): void
    {
        $missing = MissingReport::factory()->create(['item_name' => 'Kunci hilang']);
        $found = LostReport::factory()->create(['item_name' => 'Dompet ditemukan']);

        $this->assertSame('missing_reports', $missing->getTable());
        $this->assertSame('lost_reports', $found->getTable());
        $this->assertDatabaseHas('missing_reports', [
            'id' => $missing->id,
            'status' => MissingReport::STATUS_PENDING,
            'contact_phone' => $missing->contact_phone,
        ]);
        $this->assertDatabaseHas('lost_reports', ['id' => $found->id]);
        $this->assertTrue(Schema::hasColumns('missing_reports', [
            'user_id', 'contact_phone', 'photo_path', 'lost_at', 'location', 'found_at', 'archived_at',
        ]));
        $this->assertFalse(Schema::hasColumn('missing_reports', 'lost_report_id'));
        $this->assertFalse(Schema::hasColumn('lost_reports', 'missing_report_id'));
        $this->assertSame(0, MissingReportStatusHistory::where('missing_report_id', $missing->id)->count());
    }

    public function test_public_scopes_follow_the_a18_status_labels(): void
    {
        $unfound = MissingReport::factory()->approved()->create(['item_name' => 'Belum Ditemukan']);
        $confirming = MissingReport::factory()->foundPending()->create(['item_name' => 'Menunggu Konfirmasi']);
        $found = MissingReport::factory()->foundAt(now()->subDays(29))->create(['item_name' => 'Sudah Ditemukan']);
        $expired = MissingReport::factory()->foundAt(now()->subDays(31))->create();
        $pending = MissingReport::factory()->create();
        $archived = MissingReport::factory()->archived()->create();

        $this->assertEqualsCanonicalizing(
            [$unfound->id, $confirming->id],
            MissingReport::query()->publicUnfound()->pluck('id')->all(),
        );
        $this->assertEqualsCanonicalizing(
            [$found->id],
            MissingReport::query()->publicFound()->pluck('id')->all(),
        );
        $this->assertTrue($unfound->isPublic());
        $this->assertTrue($confirming->isPublic());
        $this->assertTrue($found->isPublic());
        $this->assertFalse($expired->isPublic());
        $this->assertFalse($pending->isPublic());
        $this->assertFalse($archived->isPublic());
    }

    public function test_archive_service_uses_thirty_day_boundary_and_separate_history(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-12-21 12:00:00', 'Asia/Makassar'));

        $due = MissingReport::factory()->foundAt(now()->subDays(30))->create(['item_name' => 'Arsip Satu']);
        $safe = MissingReport::factory()->foundAt(now()->subDays(29))->create(['item_name' => 'Arsip Dua']);

        $this->assertSame('2026-12-21 12:00:00', $due->archiveDueAt()->toDateTimeString());
        $this->assertTrue($due->isArchiveDue());
        $this->assertFalse($safe->isArchiveDue());

        $service = app(MissingReportArchiveService::class);

        $this->assertSame(1, $service->archiveDue());
        $this->assertSame(MissingReport::STATUS_ARCHIVED, $due->fresh()->status);
        $this->assertNotNull($due->fresh()->archived_at);
        $this->assertSame(MissingReport::STATUS_FOUND, $safe->fresh()->status);

        $history = MissingReportStatusHistory::where('missing_report_id', $due->id)
            ->where('action', 'archive')
            ->firstOrFail();
        $this->assertNull($history->actor_id);
        $this->assertSame(MissingReport::STATUS_FOUND, $history->from_status);
        $this->assertSame(MissingReport::STATUS_ARCHIVED, $history->to_status);
        $this->assertSame(0, ReportStatusHistory::count());

        $this->assertSame(0, $service->archiveDue());
    }

    public function test_moderation_service_records_decisions_in_separate_history(): void
    {
        $admin = Admin::factory()->create();
        $service = app(MissingReportModerationService::class);

        $report = MissingReport::factory()->create();
        $service->recordAdminEdit($report, $admin, 'Perbaikan Admin');

        $edit = MissingReportStatusHistory::where('missing_report_id', $report->id)
            ->where('action', 'admin_edited')
            ->firstOrFail();
        $this->assertSame($admin->id, $edit->admin_id);
        $this->assertSame('Perbaikan Admin', $edit->note);

        $service->decide($report, $admin, 'approve', null, 'Disetujui');

        $this->assertSame(MissingReport::STATUS_APPROVED, $report->fresh()->status);
        $approval = MissingReportStatusHistory::where('missing_report_id', $report->id)
            ->where('action', 'approve')
            ->firstOrFail();
        $this->assertSame($admin->id, $approval->admin_id);
        $this->assertSame(MissingReport::STATUS_PENDING, $approval->from_status);
        $this->assertSame(MissingReport::STATUS_APPROVED, $approval->to_status);
        $this->assertSame('Disetujui', $approval->note);

        $rejected = MissingReport::factory()->create();

        try {
            $service->decide($rejected, $admin, 'reject');
            $this->fail('Penolakan tanpa alasan seharusnya ditolak.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }
        $this->assertSame(MissingReport::STATUS_PENDING, $rejected->fresh()->status);

        $service->decide($rejected, $admin, 'reject', 'Data kurang lengkap');

        $this->assertSame(MissingReport::STATUS_REJECTED, $rejected->fresh()->status);
        $rejection = MissingReportStatusHistory::where('missing_report_id', $rejected->id)
            ->where('action', 'reject')
            ->firstOrFail();
        $this->assertSame('Data kurang lengkap', $rejection->reason);

        try {
            $service->decide($rejected, $admin, 'approve');
            $this->fail('Laporan yang sudah diputuskan tidak boleh diproses ulang.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $this->assertSame(0, ReportStatusHistory::count());
    }
}
