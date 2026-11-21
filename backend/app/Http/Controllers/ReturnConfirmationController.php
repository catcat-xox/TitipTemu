<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnConfirmationRequest;
use App\Models\LostReport;
use App\Services\ReturnConfirmationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReturnConfirmationController extends Controller
{
    public function create(LostReport $report): View
    {
        abort_unless(
            $report->user_id === auth()->id()
                && $report->status === 'approved'
                && auth()->user()?->hasVerifiedEmail(),
            403
        );

        $attempt = (int) $report->returnConfirmations()->max('attempt') + 1;
        $latestRejection = $report->returnConfirmations()
            ->where('status', 'rejected')
            ->latest('id')
            ->first();

        return view('pages.reports.return.create', compact('report', 'attempt', 'latestRejection'));
    }

    public function store(
        StoreReturnConfirmationRequest $request,
        ReturnConfirmationService $service,
        LostReport $report,
    ): RedirectResponse {
        $data = $request->safe()->only(['proof', 'note']);

        $service->submit(
            $report,
            $request->user(),
            $data['proof'],
            $data['note'] ?? null,
        );

        return redirect()->route('reports.mine')
            ->with('success', 'Konfirmasi pengembalian dikirim dan menunggu pemeriksaan Admin.');
    }
}
