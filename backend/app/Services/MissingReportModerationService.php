<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\MissingReport;
use App\Models\MissingReportStatusHistory;
use Illuminate\Support\Facades\DB;

final class MissingReportModerationService
{
    public function recordAdminEdit(MissingReport $report, Admin $admin, ?string $note = null): void
    {
        $status = $report->status;

        MissingReportStatusHistory::create([
            'missing_report_id' => $report->id,
            'actor_id' => null,
            'admin_id' => $admin->id,
            'action' => 'admin_edited',
            'from_status' => $status,
            'to_status' => $status,
            'note' => blank($note) ? 'Admin memperbarui isi laporan.' : $note,
        ]);
    }

    public function decide(
        MissingReport $report,
        Admin $admin,
        string $decision,
        ?string $reason = null,
        ?string $note = null,
    ): void {
        abort_unless($report->status === MissingReport::STATUS_PENDING, 403);

        $toStatus = match ($decision) {
            'approve' => MissingReport::STATUS_APPROVED,
            'reject' => MissingReport::STATUS_REJECTED,
            default => abort(422),
        };

        abort_if($decision === 'reject' && blank($reason), 422);

        $fromStatus = $report->status;

        DB::transaction(function () use ($report, $admin, $decision, $toStatus, $fromStatus, $reason, $note): void {
            $report->update(['status' => $toStatus]);

            MissingReportStatusHistory::create([
                'missing_report_id' => $report->id,
                'actor_id' => null,
                'admin_id' => $admin->id,
                'action' => $decision,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'reason' => $decision === 'reject' ? $reason : null,
                'note' => blank($note) ? null : $note,
            ]);
        });
    }
}
