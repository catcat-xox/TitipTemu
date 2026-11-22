<?php

namespace App\Http\Controllers;

use App\Models\LostReport;
use App\Models\MissingReport;
use App\Support\ReportPhoto;
use App\Support\WhatsAppLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicReportController extends Controller
{
    private const TYPES = ['lost', 'found'];

    private const STATES = ['belum', 'sudah'];

    private const LABELS = [
        'lost' => [
            'title' => 'Barang Hilang',
            'belum' => 'Barang Belum Ditemukan',
            'sudah' => 'Barang Sudah Ditemukan',
            'contact' => 'Hubungi pemilik via WhatsApp',
        ],
        'found' => [
            'title' => 'Barang Ditemukan',
            'belum' => 'Barang Belum Kembali',
            'sudah' => 'Barang Sudah Kembali',
            'contact' => 'Hubungi penemu via WhatsApp',
        ],
    ];

    public function home(): View
    {
        $latestMissing = MissingReport::query()
            ->publicUnfound()
            ->latest()
            ->get()
            ->map(fn (MissingReport $report): array => [
                'kind' => 'lost',
                'kind_label' => 'Barang Hilang',
                'status_label' => 'Barang Belum Ditemukan',
                'item_name' => $report->item_name,
                'category' => $report->category,
                'location' => $report->location,
                'lost_at' => $report->lost_at,
                'created_at' => $report->created_at,
                'photo_url' => route('missing-reports.photo', $report),
                'show_url' => route('reports.show', ['lost', $report]),
            ]);

        $latestFound = LostReport::query()
            ->publicUnreturned()
            ->latest()
            ->get()
            ->map(fn (LostReport $report): array => [
                'kind' => 'found',
                'kind_label' => 'Barang Ditemukan',
                'status_label' => 'Barang Belum Kembali',
                'item_name' => $report->item_name,
                'category' => $report->category,
                'location' => $report->location,
                'lost_at' => $report->lost_at,
                'created_at' => $report->created_at,
                'photo_url' => route('reports.photo', $report),
                'show_url' => route('reports.show', ['found', $report]),
            ]);

        $latestReports = $latestMissing
            ->concat($latestFound)
            ->sortByDesc('created_at')
            ->take(4)
            ->values();

        return view('pages.home', compact('latestReports'));
    }

    public function index(Request $request, string $type): View
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        $state = $request->string('status')->toString();
        $state = in_array($state, self::STATES, true) ? $state : 'belum';

        $query = $type === 'lost'
            ? MissingReport::query()->when(
                $state === 'belum',
                fn ($query) => $query->publicUnfound(),
                fn ($query) => $query->publicFound(),
            )
            : LostReport::query()->when(
                $state === 'belum',
                fn ($query) => $query->publicUnreturned(),
                fn ($query) => $query->publicReturned(),
            );

        $query->when($request->filled('q'), function ($query) use ($request) {
            $keyword = $request->string('q')->toString();
            $query->where(function ($query) use ($keyword) {
                $query->where('item_name', 'like', "%{$keyword}%")
                    ->orWhere('category', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%");
            });
        })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')->toString()))
            ->when($request->filled('lost_date'), fn ($query) => $query->whereDate('lost_at', $request->string('lost_date')->toString()))
            ->latest();

        $reports = $query->paginate(12)->withQueryString();

        $baseQuery = $type === 'lost'
            ? MissingReport::query()->when(
                $state === 'belum',
                fn ($query) => $query->publicUnfound(),
                fn ($query) => $query->publicFound(),
            )
            : LostReport::query()->when(
                $state === 'belum',
                fn ($query) => $query->publicUnreturned(),
                fn ($query) => $query->publicReturned(),
            );

        $categories = (clone $baseQuery)->distinct()->orderBy('category')->pluck('category');
        $counts = [
            'belum' => (clone $baseQuery)->count(),
            'sudah' => $type === 'lost'
                ? MissingReport::query()->publicFound()->count()
                : LostReport::query()->publicReturned()->count(),
        ];

        return view('pages.reports.index', [
            'reports' => $reports,
            'categories' => $categories,
            'type' => $type,
            'state' => $state,
            'counts' => $counts,
            'labels' => self::LABELS[$type],
        ]);
    }

    public function show(string $type, string $report): View
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        $model = $type === 'lost'
            ? MissingReport::query()->findOrFail($report)
            : LostReport::query()->findOrFail($report);

        abort_unless($model->isPublic(), 404);

        $state = in_array($model->status, ['found', 'returned'], true) ? 'sudah' : 'belum';

        return view('pages.reports.show', [
            'report' => $model,
            'type' => $type,
            'state' => $state,
            'labels' => self::LABELS[$type],
        ]);
    }

    public function contact(LostReport $report): RedirectResponse
    {
        abort_unless($report->isPublic() && filled($report->contact_phone), 404);

        $url = WhatsAppLink::forReport($report);
        abort_unless($url !== null, 404);

        return redirect()->away($url);
    }

    public function contactMissing(MissingReport $report): RedirectResponse
    {
        abort_unless($report->isPublic() && filled($report->contact_phone), 404);

        $url = WhatsAppLink::forReport($report);
        abort_unless($url !== null, 404);

        return redirect()->away($url);
    }

    public function photo(LostReport $report)
    {
        abort_unless($report->isPublic(), 404);

        return ReportPhoto::response($report);
    }

    public function photoMissing(MissingReport $report)
    {
        abort_unless($report->isPublic(), 404);

        return ReportPhoto::response($report);
    }
}
