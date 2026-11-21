<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModerateLostReportRequest;
use App\Models\LostReport;
use App\Services\ReportModerationService;
use App\Support\ReportPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'return_pending', 'rejected', 'returned', 'archived', 'cancelled'];

    private const STATUS_LABELS = [
        'pending' => 'Menunggu pemeriksaan',
        'approved' => 'Masih dicari',
        'return_pending' => 'Pengembalian diperiksa',
        'rejected' => 'Ditolak',
        'returned' => 'Sudah kembali',
        'archived' => 'Diarsipkan',
        'cancelled' => 'Dibatalkan',
    ];

    private const ACTION_LABELS = [
        'approve' => 'Laporan disetujui',
        'reject' => 'Laporan ditolak',
        'return_submitted' => 'Konfirmasi dikirim',
        'return_approved' => 'Pengembalian disetujui',
        'return_rejected' => 'Pengembalian ditolak',
        'return_resolved_by_admin' => 'Diselesaikan Admin',
        'archive' => 'Diarsipkan otomatis',
    ];

    private const RETURN_STATUS_LABELS = [
        'pending' => 'Menunggu pemeriksaan',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ];

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        if (! in_array($status, self::STATUSES, true)) {
            $status = 'pending';
        }

        $reports = LostReport::query()
            ->with('user')
            ->where('status', $status)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = LostReport::query()
            ->whereIn('status', self::STATUSES)
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as total')
            ->pluck('total', 'status');

        return view('pages.admin.dashboard', [
            'reports' => $reports,
            'counts' => $counts,
            'currentStatus' => $status,
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }

    public function show(LostReport $report): View
    {
        $report->load([
            'user',
            'statusHistories' => fn ($query) => $query->latest(),
            'returnConfirmations' => fn ($query) => $query->latest('id'),
            'returnConfirmations.user',
        ]);

        return view('pages.admin.reports.show', [
            'report' => $report,
            'statusLabels' => self::STATUS_LABELS,
            'actionLabels' => self::ACTION_LABELS,
            'returnStatusLabels' => self::RETURN_STATUS_LABELS,
        ]);
    }

    public function photo(LostReport $report)
    {
        return ReportPhoto::response($report);
    }

    public function moderate(
        ModerateLostReportRequest $request,
        ReportModerationService $service,
        LostReport $report,
    ): RedirectResponse {
        $data = $request->safe()->only(['decision', 'reason', 'note']);

        $service->decide(
            $report,
            $request->user(),
            $data['decision'],
            $data['reason'] ?? null,
            $data['note'] ?? null,
        );

        return redirect()->route('admin.dashboard', [
            'status' => $data['decision'] === 'approve' ? 'approved' : 'rejected',
        ])->with(
            'success',
            $data['decision'] === 'approve'
                ? 'Laporan disetujui dan tampil di Barang Hilang.'
                : 'Laporan ditolak. Pemilik dapat memperbaiki dan mengirim ulang.',
        );
    }
}
