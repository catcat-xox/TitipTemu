<?php

namespace App\Services;

use App\Models\LostReport;
use App\Models\ReportStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ReportModerationService
{
    public function decide(LostReport $report, User $admin, string $decision, ?string $reason = null, ?string $note = null): void
    {
        abort_unless($report->status === 'pending', 403);

        $toStatus = match ($decision) {
            'approve' => 'approved',
            'reject' => 'rejected',
            default => abort(422),
        };

        abort_if($decision === 'reject' && blank($reason), 422);

        $fromStatus = $report->status;

        DB::transaction(function () use ($report, $admin, $decision, $toStatus, $fromStatus, $reason, $note): void {
            $report->update(['status' => $toStatus]);

            ReportStatusHistory::create([
                'lost_report_id' => $report->id,
                'actor_id' => $admin->id,
                'action' => $decision,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'reason' => $decision === 'reject' ? $reason : null,
                'note' => blank($note) ? null : $note,
            ]);
        });
    }
}
