<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUpdateLostReportRequest;
use App\Http\Requests\AdminUpdateMissingReportRequest;
use App\Http\Requests\ModerateLostReportRequest;
use App\Models\LostReport;
use App\Models\MissingReport;
use App\Services\MissingReportModerationService;
use App\Services\ReportModerationService;
use App\Support\MissingReportPhoto;
use App\Support\ReportPhoto;
use App\Support\ReturnProof;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
        'cancel_by_owner' => 'Dibatalkan pemilik',
        'found_by_owner' => 'Barang ditemukan pemilik',
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

    private const KIND_LABELS = [
        'all' => 'Semua jenis',
        'lost' => 'Barang Hilang',
        'found' => 'Barang Ditemukan',
    ];

    private const STATUSES_LOST = [
        'pending', 'approved', 'rejected', 'found', 'archived', 'cancelled',
    ];

    private const STATUS_LABELS_EXTENDED = [
        'pending' => 'Menunggu pemeriksaan',
        'approved' => 'Masih dicari',
        'return_pending' => 'Pengembalian diperiksa',
        'found_pending' => 'Penemuan diperiksa',
        'rejected' => 'Ditolak',
        'returned' => 'Sudah kembali',
        'found' => 'Sudah ditemukan',
        'archived' => 'Diarsipkan',
        'cancelled' => 'Dibatalkan',
        'verification' => 'Menunggu tindak lanjut',
        'done' => 'Selesai',
    ];

    private const CARD_GROUPS = [
        'pending' => ['pending'],
        'approved' => ['approved'],
        'verification' => ['return_pending', 'found_pending'],
        'done' => ['returned', 'found'],
    ];

    private const CARD_LABELS = [
        'pending' => 'MENUNGGU',
        'approved' => 'DITERIMA',
        'verification' => 'TINDAK LANJUT',
        'done' => 'SELESAI',
    ];

    public function index(Request $request): View
    {
        $jenis = $request->string('jenis')->toString();
        if (! array_key_exists($jenis, self::KIND_LABELS)) {
            $jenis = 'all';
        }

        $statuses = $this->statusesFor($jenis);
        $status = $request->string('status')->toString();
        if (! in_array($status, ['all', 'verification', 'done', ...$statuses], true)) {
            $status = 'pending';
        }

        $rows = $this->reportRows($jenis, $status);
        $counts = $this->statusCounts($jenis);

        $cards = collect(self::CARD_GROUPS)->map(function (array $group, string $key) use ($counts, $jenis): array {
            $label = self::CARD_LABELS[$key];
            if ($key === 'verification' && $jenis !== 'all') {
                $label = $jenis === 'lost' ? 'PENEMUAN' : 'PENGEMBALIAN';
            }
            if ($key === 'done' && $jenis !== 'all') {
                $label = $jenis === 'lost' ? 'SUDAH DITEMUKAN' : 'SUDAH KEMBALI';
            }

            return [
                'filter' => $key,
                'label' => $label,
                'total' => collect($group)->sum(fn (string $item) => $counts->get($item, 0)),
            ];
        });

        return view('pages.admin.dashboard', [
            'reports' => $this->paginateRows($rows),
            'cards' => $cards,
            'counts' => $counts,
            'currentJenis' => $jenis,
            'jenisLabels' => self::KIND_LABELS,
            'currentStatus' => $status,
            'currentStatusLabel' => self::STATUS_LABELS_EXTENDED[$status] ?? 'Semua',
            'statusOptions' => $this->statusOptions($jenis, $rows->count()),
            'statusLabels' => self::STATUS_LABELS_EXTENDED,
        ]);
    }

    public function search(Request $request): View
    {
        $keyword = $request->string('q')->trim()->toString();

        return view('pages.admin.reports.search', [
            'reports' => $this->paginateRows($this->reportRows('all', 'all', $keyword)),
            'keyword' => $keyword,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function kindsFor(string $jenis): array
    {
        return match ($jenis) {
            'lost' => ['lost'],
            'found' => ['found'],
            default => ['lost', 'found'],
        };
    }

    /**
     * @return array<int, string>
     */
    private function statusesFor(string $jenis): array
    {
        return match ($jenis) {
            'lost' => self::STATUSES_LOST,
            'found' => self::STATUSES,
            default => array_values(array_unique([...self::STATUSES, ...self::STATUSES_LOST])),
        };
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(string $jenis, int $total): array
    {
        $options = ['all' => 'Semua ('.$total.')'];
        foreach ($this->statusesFor($jenis) as $status) {
            $options[$status] = self::STATUS_LABELS_EXTENDED[$status] ?? $status;
        }
        if ($jenis === 'all') {
            $options['verification'] = self::STATUS_LABELS_EXTENDED['verification'];
            $options['done'] = self::STATUS_LABELS_EXTENDED['done'];
        }

        return $options;
    }

    private function reportRows(string $jenis, string $status, string $keyword = ''): Collection
    {
        $like = '%'.addcslashes($keyword, '%_\\').'%';

        return collect($this->kindsFor($jenis))
            ->flatMap(function (string $kind) use ($status, $keyword, $like): Collection {
                $model = $kind === 'lost' ? MissingReport::class : LostReport::class;

                return $model::query()
                    ->with('user')
                    ->when($status === 'verification', fn ($query) => $query->whereIn('status', ['return_pending', 'found_pending']))
                    ->when($status === 'done', fn ($query) => $query->whereIn('status', ['returned', 'found']))
                    ->when(! in_array($status, ['all', 'verification', 'done'], true), fn ($query) => $query->where('status', $status))
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
                    ->get()
                    ->map(fn ($report): array => [
                        'id' => $report->id,
                        'kind' => $kind,
                        'kind_label' => self::KIND_LABELS[$kind],
                        'item_name' => $report->item_name,
                        'category' => $report->category,
                        'location' => $report->location,
                        'lost_at' => $report->lost_at,
                        'user_name' => $report->user?->name ?? '-',
                        'status' => $report->status,
                        'status_label' => self::STATUS_LABELS_EXTENDED[$report->status] ?? $report->status,
                        'created_at' => $report->created_at,
                        'show_url' => $kind === 'lost'
                            ? route('admin.reports.missing.show', $report)
                            : route('admin.reports.show', $report),
                        'edit_url' => $kind === 'lost'
                            ? route('admin.reports.missing.edit', $report)
                            : ($report->status === 'pending' ? route('admin.reports.edit', $report) : null),
                        'delete_url' => match (true) {
                            $kind === 'lost' => route('admin.reports.missing.destroy', $report),
                            in_array($report->status, ['returned', 'archived'], true) => route('admin.reports.destroy', $report),
                            default => null,
                        },
                    ]);
            })
            ->sortByDesc('created_at')
            ->values();
    }

    private function statusCounts(string $jenis): Collection
    {
        $counts = collect(array_fill_keys(array_keys(self::STATUS_LABELS_EXTENDED), 0));

        foreach ($this->kindsFor($jenis) as $kind) {
            $model = $kind === 'lost' ? MissingReport::class : LostReport::class;
            $model::query()
                ->groupBy('status')
                ->selectRaw('status, COUNT(*) as total')
                ->pluck('total', 'status')
                ->each(function ($total, string $status) use ($counts): void {
                    $counts->put($status, $counts->get($status, 0) + $total);
                });
        }

        return $counts;
    }

    private function paginateRows(Collection $rows, int $perPage = 10): LengthAwarePaginator
    {
        $page = max(1, (int) request('page', 1));

        return new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()],
        );
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

    public function showMissing(MissingReport $report): View
    {
        $report->load([
            'user',
            'statusHistories' => fn ($query) => $query->latest(),
            'statusHistories.admin',
        ]);

        return view('pages.admin.missing-reports.show', [
            'report' => $report,
            'statusLabels' => self::STATUS_LABELS_EXTENDED,
            'actionLabels' => self::ACTION_LABELS,
        ]);
    }

    public function editMissing(MissingReport $report): View
    {
        return view('pages.admin.missing-reports.edit', [
            'report' => $report,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function updateMissing(
        AdminUpdateMissingReportRequest $request,
        MissingReportModerationService $service,
        MissingReport $report,
    ): RedirectResponse {
        $data = $request->safe()->except(['photo']);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = MissingReportPhoto::store($request->file('photo'));
        }

        DB::transaction(function () use ($report, $data, $service): void {
            $report->update($data);
            $service->recordAdminEdit($report, Auth::guard('admin')->user());
        });

        return redirect()->route('admin.reports.missing.show', $report)
            ->with('success', 'Perubahan Admin tersimpan dan tercatat pada riwayat.');
    }

    public function photoMissing(MissingReport $report)
    {
        return ReportPhoto::response($report);
    }

    public function verifyMissing(
        ModerateLostReportRequest $request,
        MissingReportModerationService $service,
        MissingReport $report,
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
            'jenis' => 'lost',
            'status' => $data['decision'] === 'approve' ? 'approved' : 'rejected',
        ])->with(
            'success',
            $data['decision'] === 'approve'
                ? 'Laporan disetujui dan tampil di Barang Hilang.'
                : 'Laporan ditolak. Pelapor dapat memperbaiki dan mengirim ulang.',
        );
    }

    public function destroyMissing(MissingReport $report): RedirectResponse
    {
        DB::transaction(function () use ($report): void {
            if (is_string($report->photo_path)) {
                $this->deleteStoredFile($report->photo_path, MissingReportPhoto::DIRECTORY);
            }

            $report->statusHistories()->delete();
            $report->delete();
        });

        return redirect()->route('admin.dashboard', ['jenis' => 'lost', 'status' => 'all'])
            ->with('success', 'Laporan kehilangan dihapus permanen beserta foto dan riwayatnya.');
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
            'jenis' => 'found',
            'status' => $data['decision'] === 'approve' ? 'approved' : 'rejected',
        ])->with(
            'success',
            $data['decision'] === 'approve'
                ? 'Laporan disetujui dan tampil di Barang Ditemukan.'
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

        return redirect()->route('admin.dashboard', ['jenis' => 'found', 'status' => 'returned'])
            ->with('success', 'Laporan yang telah kembali dihapus permanen.');
    }

    private function deleteStoredFile(string $path, string $directory): void
    {
        if (! str_starts_with($path, $directory.'/') || str_contains($path, '..') || str_contains($path, '\\')) {
            return;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }
}
