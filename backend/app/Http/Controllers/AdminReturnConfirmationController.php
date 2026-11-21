<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectReturnConfirmationRequest;
use App\Http\Requests\ResolveReturnRequest;
use App\Models\LostReport;
use App\Models\ReturnConfirmation;
use App\Services\ReturnConfirmationService;
use App\Support\ReturnProof;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminReturnConfirmationController extends Controller
{
    public function proof(Request $request, ReturnConfirmation $confirmation)
    {
        return ReturnProof::response($confirmation, max(0, (int) $request->query('index', 0)));
    }

    public function approve(
        ReturnConfirmation $confirmation,
        ReturnConfirmationService $service,
    ): RedirectResponse {
        $service->approve($confirmation, auth()->user());

        return redirect()->route('admin.dashboard', ['status' => 'return_pending'])
            ->with('success', 'Pengembalian disetujui. Laporan tampil di Barang Ditemukan.');
    }

    public function reject(
        RejectReturnConfirmationRequest $request,
        ReturnConfirmationService $service,
        ReturnConfirmation $confirmation,
    ): RedirectResponse {
        $service->reject($confirmation, $request->user(), $request->validated('reason'));

        return redirect()->route('admin.dashboard', ['status' => 'return_pending'])
            ->with('success', 'Konfirmasi pengembalian ditolak. Pemilik dapat memperbaiki dan mengirim ulang.');
    }

    public function resolve(
        ResolveReturnRequest $request,
        ReturnConfirmationService $service,
        LostReport $report,
    ): RedirectResponse {
        $service->resolveByAdmin($report, $request->user(), $request->validated('note'));

        return redirect()->route('admin.dashboard', ['status' => 'returned'])
            ->with('success', 'Pengembalian diselesaikan oleh Admin dan tampil di Barang Ditemukan.');
    }
}
