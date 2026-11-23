<?php

namespace App\Services;

use App\Models\MissingReport;
use App\Models\MissingReportStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class MissingReportOwnerService
{
    public function cancel(MissingReport $report, User $user): void
    {
        abort_unless($report->user_id === $user->id, 403);
        abort_unless(
            in_array($report->status, [MissingReport::STATUS_PENDING, MissingReport::STATUS_APPROVED], true),
            403
        );

        DB::transaction(function () use ($report, $user): void {
            $fromStatus = $report->status;

            $report->update(['status' => MissingReport::STATUS_CANCELLED]);

            MissingReportStatusHistory::create([
                'missing_report_id' => $report->id,
                'actor_id' => $user->id,
                'admin_id' => null,
                'action' => 'cancel_by_owner',
                'from_status' => $fromStatus,
                'to_status' => MissingReport::STATUS_CANCELLED,
            ]);
        });
    }

    public function markFound(MissingReport $report, User $user): void
    {
        abort_unless($report->user_id === $user->id, 403);
        abort_unless($report->status === MissingReport::STATUS_APPROVED, 403);

        DB::transaction(function () use ($report, $user): void {
            $report->update([
                'status' => MissingReport::STATUS_FOUND,
                'found_at' => now(),
            ]);

            MissingReportStatusHistory::create([
                'missing_report_id' => $report->id,
                'actor_id' => $user->id,
                'admin_id' => null,
                'action' => 'found_by_owner',
                'from_status' => MissingReport::STATUS_APPROVED,
                'to_status' => MissingReport::STATUS_FOUND,
            ]);
        });
    }
}
