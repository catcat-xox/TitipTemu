<?php

namespace App\Services;

use App\Models\LostReport;
use App\Models\ReportStatusHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReportArchiveService
{
    public function archiveDue(?Carbon $now = null): int
    {
        $now = $now ?? now();
        $cutoff = $now->copy()->subDays(LostReport::FOUND_DISPLAY_DAYS);

        $reports = LostReport::query()
            ->where('status', 'returned')
            ->whereNotNull('returned_at')
            ->where('returned_at', '<=', $cutoff)
            ->get();

        $archived = 0;

        foreach ($reports as $report) {
            try {
                $archived += DB::transaction(function () use ($report, $now): int {
                    $fresh = LostReport::query()->whereKey($report->id)->lockForUpdate()->first();

                    if (! $fresh || ! $fresh->isArchiveDue($now)) {
                        return 0;
                    }

                    $fresh->update([
                        'status' => 'archived',
                        'archived_at' => $now,
                    ]);

                    ReportStatusHistory::create([
                        'lost_report_id' => $fresh->id,
                        'actor_id' => null,
                        'action' => 'archive',
                        'from_status' => 'returned',
                        'to_status' => 'archived',
                        'note' => 'Diarisipkan otomatis setelah 30 hari.',
                    ]);

                    return 1;
                });
            } catch (Throwable $exception) {
                Log::error('Gagal mengarsipkan laporan.', [
                    'lost_report_id' => $report->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $archived;
    }
}
