<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUpdateLostReportRequest;
use App\Http\Requests\ModerateLostReportRequest;
use App\Models\LostReport;
use App\Services\ReportModerationService;
use App\Support\ReportPhoto;
use App\Support\ReturnProof;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    private const CATEGORIES = ['Elektronik', 'Dokumen', 'Aksesori', 'Pakaian', 'Lainnya'];

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

    private const STATUS_CARD_LABELS = [
        'pending' => 'MENUNGGU',
        'return_pending' => 'PENGEMBALIAN',
    ];

    private const ACTION_LABELS = [
        'approve' => 'Laporan disetujui',
        'reject' => 'Laporan ditolak',
        'admin_edited' => 'Laporan diedit Admin',
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
        if (! in_array($status, ['all', ...self::STATUSES], true)) {
            $status = 'pending';
        }

        $reports = LostReport::query()
            ->with('user')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
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
            'currentStatusLabel' => $status === 'all' ? 'Semua' : self::STATUS_LABELS[$status],
            'statusLabels' => self::STATUS_LABELS,
            'statusCardLabels' => array_replace(self::STATUS_LABELS, self::STATUS_CARD_LABELS),
        ]);
    }

    public function search(Request $request): View
    {
        $keyword = $request->string('q')->trim()->toString();
        $like = '%'.addcslashes($keyword, '%_\\').'%';

        $reports = LostReport::query()
            ->with('user')
            ->when($keyword !== '', function ($query) use ($like): void {
                $query->where(function ($query) use ($like): void {
                    $query->where('item_name', 'like', $like)
                        ->orWhere('category', 'like', $like)
                        ->orWhere('location', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('contact_phone', 'like', $like)
                        ->orWhereHas('user', fn ($query) => $query->where('name', 'like', $like));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.reports.search', [
            'reports' => $reports,
            'keyword' => $keyword,
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }

    public function show(LostReport $report): View
    {
        $report->load([
            'user',
            'statusHistories' => fn ($query) => $query->latest(),
            'statusHistories.admin',
            'returnConfirmations' => fn ($query) => $query->latest('id'),
            'returnConfirmations.user',
            'returnConfirmations.reviewerAdmin',
        ]);

        return view('pages.admin.reports.show', [
            'report' => $report,
            'statusLabels' => self::STATUS_LABELS,
            'actionLabels' => self::ACTION_LABELS,
            'returnStatusLabels' => self::RETURN_STATUS_LABELS,
        ]);
    }

    public function edit(LostReport $report): View
    {
        abort_unless($report->status === 'pending', 403);

        return view('pages.admin.reports.edit', [
            'report' => $report,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(
        AdminUpdateLostReportRequest $request,
        ReportModerationService $service,
        LostReport $report,
    ): RedirectResponse {
        $data = $request->safe()->except(['photo']);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = ReportPhoto::store($request->file('photo'));
        }

        DB::transaction(function () use ($report, $data, $service): void {
            $report->update($data);
            $service->recordAdminEdit($report, Auth::guard('admin')->user());
        });

        return redirect()->route('admin.reports.show', $report)
            ->with('success', 'Perubahan Admin tersimpan dan tercatat pada riwayat.');
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
            $request->user('admin'),
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
                : 'Laporan ditolak. Pelapor dapat memperbaiki dan mengirim ulang.',
        );
    }

    public function destroy(LostReport $report): RedirectResponse
    {
        abort_unless(in_array($report->status, ['returned', 'archived'], true), 403);

        $report->load(['returnConfirmations']);

        DB::transaction(function () use ($report): void {
            if (is_string($report->photo_path)) {
                $this->deleteStoredFile($report->photo_path, ReportPhoto::DIRECTORY);
            }

            foreach ($report->returnConfirmations as $confirmation) {
                $paths = $confirmation->proof_paths;
                if (is_array($paths)) {
                    foreach ($paths as $path) {
                        if (is_string($path)) {
                            $this->deleteStoredFile($path, ReturnProof::DIRECTORY);
                        }
                    }
                }
                $confirmation->delete();
            }

            $report->statusHistories()->delete();
            $report->delete();
        });

        return redirect()->route('admin.dashboard', ['status' => 'returned'])
            ->with('success', 'Laporan yang telah kembali dihapus permanen.');
    }

    private function deleteStoredFile(string $path, string $directory): void
    {
        if (! str_starts_with($path, $directory.'/') || str_contains($path, '..')) {
            return;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }
}
