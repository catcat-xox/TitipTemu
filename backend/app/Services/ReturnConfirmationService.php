<?php

namespace App\Services;

use App\Models\LostReport;
use App\Models\ReportStatusHistory;
use App\Models\ReturnConfirmation;
use App\Models\User;
use App\Support\ReturnProof;
use Illuminate\Support\Facades\DB;

final class ReturnConfirmationService
{
    private const MAX_ATTEMPTS = 3;

    public function submit(LostReport $report, User $owner, array $files, ?string $note): ReturnConfirmation
    {
        abort_unless(
            $report->user_id === $owner->id
                && $report->status === 'approved'
                && $owner->hasVerifiedEmail(),
            403
        );

        $attempt = (int) $report->returnConfirmations()->max('attempt') + 1;
        abort_if($attempt > self::MAX_ATTEMPTS, 403);

        return DB::transaction(function () use ($report, $owner, $files, $note, $attempt): ReturnConfirmation {
            $confirmation = ReturnConfirmation::create([
                'lost_report_id' => $report->id,
                'user_id' => $owner->id,
                'status' => 'pending',
                'attempt' => $attempt,
                'reported_returned_at' => now(),
                'proof_paths' => ReturnProof::store($files),
                'note' => $note,
            ]);

            $report->update(['status' => 'return_pending']);

            ReportStatusHistory::create([
                'lost_report_id' => $report->id,
                'actor_id' => $owner->id,
                'action' => 'return_submitted',
                'from_status' => 'approved',
                'to_status' => 'return_pending',
                'note' => $note,
            ]);

            return $confirmation;
        });
    }

    public function approve(ReturnConfirmation $confirmation, User $admin, ?string $note = null): void
    {
        abort_unless(
            $confirmation->status === 'pending'
                && $confirmation->report->status === 'return_pending',
            403
        );

        DB::transaction(function () use ($confirmation, $admin, $note): void {
            $now = now();
            $report = $confirmation->report;

            $confirmation->update([
                'status' => 'approved',
                'admin_id' => $admin->id,
                'admin_note' => $note,
                'reviewed_at' => $now,
            ]);

            $report->update([
                'status' => 'returned',
                'returned_at' => $now,
            ]);

            ReportStatusHistory::create([
                'lost_report_id' => $report->id,
                'actor_id' => $admin->id,
                'action' => 'return_approved',
                'from_status' => 'return_pending',
                'to_status' => 'returned',
                'note' => $note,
            ]);
        });
    }

    public function reject(ReturnConfirmation $confirmation, User $admin, string $reason): void
    {
        abort_unless(
            $confirmation->status === 'pending'
                && $confirmation->report->status === 'return_pending',
            403
        );
        abort_if(blank($reason), 422);

        DB::transaction(function () use ($confirmation, $admin, $reason): void {
            $report = $confirmation->report;

            $confirmation->update([
                'status' => 'rejected',
                'admin_id' => $admin->id,
                'rejection_reason' => $reason,
                'reviewed_at' => now(),
            ]);

            $report->update(['status' => 'approved']);

            ReportStatusHistory::create([
                'lost_report_id' => $report->id,
                'actor_id' => $admin->id,
                'action' => 'return_rejected',
                'from_status' => 'return_pending',
                'to_status' => 'approved',
                'reason' => $reason,
            ]);
        });
    }

    public function resolveByAdmin(LostReport $report, User $admin, string $note): ReturnConfirmation
    {
        $latest = $report->returnConfirmations()->latest('id')->first();

        abort_unless(
            $report->status === 'approved'
                && $latest?->status === 'rejected'
                && $latest->attempt >= self::MAX_ATTEMPTS,
            403
        );
        abort_if(blank($note), 422);

        return DB::transaction(function () use ($report, $admin, $note, $latest): ReturnConfirmation {
            $now = now();

            $confirmation = ReturnConfirmation::create([
                'lost_report_id' => $report->id,
                'user_id' => $report->user_id,
                'admin_id' => $admin->id,
                'status' => 'approved',
                'attempt' => $latest->attempt,
                'reported_returned_at' => $now,
                'proof_paths' => $latest->proof_paths,
                'admin_note' => $note,
                'is_admin_resolution' => true,
                'reviewed_at' => $now,
            ]);

            $report->update([
                'status' => 'returned',
                'returned_at' => $now,
            ]);

            ReportStatusHistory::create([
                'lost_report_id' => $report->id,
                'actor_id' => $admin->id,
                'action' => 'return_resolved_by_admin',
                'from_status' => 'approved',
                'to_status' => 'returned',
                'note' => $note,
            ]);

            return $confirmation;
        });
    }
}
