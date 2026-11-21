<?php

namespace App\Http\Controllers;

use App\Models\LostReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicReportController extends Controller
{
    public function index(Request $request, string $type): View
    {
        abort_unless(in_array($type, ['lost', 'found'], true), 404);

        $query = LostReport::query()
            ->when(
                $type === 'lost',
                fn ($query) => $query->publicLost(),
                fn ($query) => $query->publicFound(),
            )
            ->when($request->filled('q'), function ($query) use ($request) {
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
        $categories = LostReport::query()
            ->whereIn('status', $type === 'lost' ? ['approved'] : ['returned'])
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('pages.reports.index', compact('reports', 'categories', 'type'));
    }

    public function show(string $type, LostReport $report): View
    {
        abort_unless(
            in_array($type, ['lost', 'found'], true)
            && (($type === 'lost' && $report->status === 'approved')
                || ($type === 'found' && $report->isPublic() && $report->status === 'returned')),
            404,
        );

        return view('pages.reports.show', compact('report', 'type'));
    }
}
