<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\ReportStatusHistory;
use App\Models\ReturnConfirmation;
use App\Models\User;
use App\Services\ReportArchiveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ReportArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_archive_due_at_uses_wita_and_thirty_day_boundary(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-12-21 00:00:00', 'Asia/Makassar'));

        $this->assertSame('Asia/Makassar', config('app.timezone'));

        $onBoundary = LostReport::factory()
            ->returnedAt(Carbon::parse('2026-11-21 00:00:00', 'Asia/Makassar'))
            ->create();
        $beforeBoundary = LostReport::factory()
            ->returnedAt(Carbon::parse('2026-11-21 00:00:01', 'Asia/Makassar'))
            ->create();

        $this->assertTrue($onBoundary->isArchiveDue());
        $this->assertFalse($beforeBoundary->isArchiveDue());
        $this->assertSame('2026-12-21 00:00:00', $onBoundary->archiveDueAt()->toDateTimeString());
    }

    public function test_command_archives_due_reports_once_and_records_system_history(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-12-21 12:00:00', 'Asia/Makassar'));

        $due = LostReport::factory()->returnedAt(now()->subDays(30))->create(['item_name' => 'Arsip Satu']);
        $safe = LostReport::factory()->returnedAt(now()->subDays(29))->create(['item_name' => 'Arsip Dua']);

        $this->artisan('reports:archive-returned')
            ->assertSuccessful()
            ->expectsOutput('Diarsipkan: 1 laporan.');

        $this->assertSame('archived', $due->fresh()->status);
        $this->assertNotNull($due->fresh()->archived_at);
        $this->assertSame('returned', $safe->fresh()->status);

        $history = ReportStatusHistory::where('lost_report_id', $due->id)
            ->where('action', 'archive')
            ->firstOrFail();
        $this->assertNull($history->actor_id);
        $this->assertSame('returned', $history->from_status);
        $this->assertSame('archived', $history->to_status);

        $this->artisan('reports:archive-returned')
            ->assertSuccessful()
            ->expectsOutput('Diarsipkan: 0 laporan.');

        $this->assertSame(1, ReportStatusHistory::where('lost_report_id', $due->id)->where('action', 'archive')->count());
    }

    public function test_archived_report_is_hidden_from_public_but_data_is_retained(): void
    {
        Storage::fake('local');
        Carbon::setTestNow(Carbon::parse('2026-12-21 12:00:00', 'Asia/Makassar'));

        $owner = User::factory()->create();
        $report = LostReport::factory()
            ->for($owner)
            ->returnedAt(now()->subDays(31))
            ->create(['item_name' => 'Barang Arsip']);
        $confirmation = ReturnConfirmation::factory()->create([
            'lost_report_id' => $report->id,
            'user_id' => $owner->id,
        ]);

        $this->artisan('reports:archive-returned')->assertSuccessful();

        $this->get(route('reports.index', ['ditemukan', 'status' => 'sudah']))->assertDontSee('Barang Arsip');
        $this->get(route('reports.show', ['ditemukan', $report]))->assertNotFound();
        $this->get(route('reports.photo', $report))->assertNotFound();

        $this->assertDatabaseHas('lost_reports', ['id' => $report->id, 'status' => 'archived']);
        $this->assertNotNull($report->fresh()->archived_at);
        $this->assertNotNull($report->fresh()->returned_at);
        $this->assertDatabaseHas('return_confirmations', ['id' => $confirmation->id]);

        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertSee('Diarsipkan otomatis');
    }

    public function test_scheduler_registers_daily_archive_command(): void
    {
        $commands = collect(Schedule::events())
            ->map(fn ($event) => $event->command)
            ->implode(' ');

        $this->assertStringContainsString('reports:archive-returned', $commands);
    }

    public function test_command_logs_failure_without_changing_report_status(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-12-21 12:00:00', 'Asia/Makassar'));

        $report = LostReport::factory()->returnedAt(now()->subDays(31))->create();

        $service = Mockery::mock(ReportArchiveService::class);
        $service->shouldReceive('archiveDue')->once()->andThrow(new RuntimeException('gagal uji'));
        $this->app->instance(ReportArchiveService::class, $service);
        Log::spy();

        $this->artisan('reports:archive-returned')->assertExitCode(1);

        Log::shouldHaveReceived('error')->once();
        $this->assertSame('returned', $report->fresh()->status);
        $this->assertNull($report->fresh()->archived_at);
    }
}
