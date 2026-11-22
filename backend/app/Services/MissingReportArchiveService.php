<?php

namespace App\Services;

use App\Models\MissingReport;
use App\Models\MissingReportStatusHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MissingReportArchiveService
{
    public function archiveDue(?Carbon $now = null): int
    {
        $now = $now ?? now();
        $cutoff = $now->copy()->subDays(MissingReport::FOUND_DISPLAY_DAYS);

        $reports = MissingReport::query()
            ->where('status', MissingReport::STATUS_FOUND)
            ->whereNotNull('found_at')
            ->where('found_at', '<=', $cutoff)
            ->get();

        $archived = 0;

        foreach ($reports as $report) {
            try {
                $archived += DB::transaction(function () use ($report, $now): int {
                    $fresh = MissingReport::query()->whereKey($report->id)->lockForUpdate()->first();

                    if (! $fresh || ! $fresh->isArchiveDue($now)) {
                        return 0;
                    }

                    $fresh->update([
                        'status' => MissingReport::STATUS_ARCHIVED,
                        'archived_at' => $now,
                    ]);

                    MissingReportStatusHistory::create([
                        'missing_report_id' => $fresh->id,
                        'actor_id' => null,
                        'action' => 'archive',
                        'from_status' => MissingReport::STATUS_FOUND,
                        'to_status' => MissingReport::STATUS_ARCHIVED,
                        'note' => 'Diarisipkan otomatis setelah 30 hari.',
                    ]);

                    return 1;
                });
            } catch (Throwable $exception) {
                Log::error('Gagal mengarsipkan laporan kehilangan.', [
                    'missing_report_id' => $report->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $archived;
    }
}
