<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\ReportStatusHistory;
use Illuminate\Support\Facades\DB;

final class ReportModerationService
{
    public function recordAdminEdit(LostReport $report, Admin $admin, ?string $note = null): void
    {
        abort_unless($report->status === 'pending', 403);

        ReportStatusHistory::create([
            'lost_report_id' => $report->id,
            'actor_id' => null,
            'admin_id' => $admin->id,
            'action' => 'admin_edited',
            'from_status' => 'pending',
            'to_status' => 'pending',
            'note' => blank($note) ? 'Admin memperbarui isi laporan.' : $note,
        ]);
    }

    public function decide(LostReport $report, Admin $admin, string $decision, ?string $reason = null, ?string $note = null): void
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
