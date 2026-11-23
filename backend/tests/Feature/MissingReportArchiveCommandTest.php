<?php

namespace Tests\Feature;

use App\Models\MissingReport;
use App\Services\MissingReportArchiveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use RuntimeException;
use Tests\TestCase;

class MissingReportArchiveCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_command_archives_missing_reports_older_than_thirty_days(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-12-21 12:00:00', 'Asia/Makassar'));

        $due = MissingReport::factory()->foundAt(now()->subDays(30))->create();
        $safe = MissingReport::factory()->foundAt(now()->subDays(29))->create();

        $this->artisan('missing-reports:archive-found')
            ->expectsOutput('Diarsipkan: 1 laporan kehilangan.')
            ->assertSuccessful();

        $this->assertSame(MissingReport::STATUS_ARCHIVED, $due->fresh()->status);
        $this->assertSame(MissingReport::STATUS_FOUND, $safe->fresh()->status);
        $this->assertDatabaseHas('missing_report_status_histories', [
            'missing_report_id' => $due->id,
            'action' => 'archive',
        ]);
    }

    public function test_scheduler_registers_daily_missing_report_archive_command(): void
    {
        $commands = collect(Schedule::events())
            ->map(fn ($event) => $event->command)
            ->implode(' ');

        $this->assertStringContainsString('missing-reports:archive-found', $commands);
        $this->assertStringContainsString('reports:archive-returned', $commands);
    }

    public function test_command_logs_failure_without_changing_report_status(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-12-21 12:00:00', 'Asia/Makassar'));

        $report = MissingReport::factory()->foundAt(now()->subDays(31))->create();

        $service = $this->createMock(MissingReportArchiveService::class);
        $service->method('archiveDue')->willThrowException(new RuntimeException('gagal uji'));
        $this->app->instance(MissingReportArchiveService::class, $service);
        Log::spy();

        $this->artisan('missing-reports:archive-found')->assertExitCode(1);

        Log::shouldHaveReceived('error')->once();
        $this->assertSame(MissingReport::STATUS_FOUND, $report->fresh()->status);
        $this->assertNull($report->fresh()->archived_at);
    }
}
