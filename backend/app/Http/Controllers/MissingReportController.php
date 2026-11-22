<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMissingReportRequest;
use App\Http\Requests\UpdateMissingReportRequest;
use App\Models\MissingReport;
use App\Support\MissingReportPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MissingReportController extends Controller
{
    private const CATEGORIES = ['Elektronik', 'Dokumen', 'Aksesori', 'Pakaian', 'Lainnya'];

    public function create(): View
    {
        return view('pages.reports.missing-create', [
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(StoreMissingReportRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'terms_accepted']);
        $data['user_id'] = $request->user()->id;
        $data['photo_path'] = MissingReportPhoto::store($request->file('photo'));
        $data['terms_accepted_at'] = now();
        $data['status'] = MissingReport::STATUS_PENDING;

        MissingReport::create($data);

        return redirect()->route('missing-reports.mine')
            ->with('success', 'Laporan kehilangan berhasil dikirim dan menunggu pemeriksaan Admin.');
    }

    public function mine(): View
    {
        return view('pages.reports.missing-mine', [
            'reports' => auth()->user()->missingReports()
                ->with('latestRejection')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function edit(MissingReport $report): View
    {
        $this->assertOwnedReport($report);
        abort_unless(
            in_array($report->status, [MissingReport::STATUS_PENDING, MissingReport::STATUS_REJECTED], true),
            403,
        );

        return view('pages.reports.missing-edit', [
            'report' => $report,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(UpdateMissingReportRequest $request, MissingReport $report): RedirectResponse
    {
        $data = $request->safe()->except(['photo']);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = MissingReportPhoto::store($request->file('photo'));
        }
        if ($report->status === MissingReport::STATUS_REJECTED) {
            $data['status'] = MissingReport::STATUS_PENDING;
        }

        $report->update($data);

        return redirect()->route('missing-reports.mine')->with(
            'success',
            $report->wasChanged('status')
                ? 'Perubahan disimpan dan laporan dikirim ulang untuk diperiksa Admin.'
                : 'Perubahan laporan kehilangan berhasil disimpan.',
        );
    }

    public function cancel(MissingReport $report): RedirectResponse
    {
        $this->assertOwnedReport($report);
        abort_unless($report->status === MissingReport::STATUS_PENDING, 403);

        $report->update(['status' => MissingReport::STATUS_CANCELLED]);

        return redirect()->route('missing-reports.mine')
            ->with('success', 'Laporan kehilangan dibatalkan dan tidak lagi ditampilkan.');
    }

    public function resubmit(MissingReport $report): RedirectResponse
    {
        $this->assertOwnedReport($report);
        abort_unless($report->status === MissingReport::STATUS_REJECTED, 403);

        $report->update(['status' => MissingReport::STATUS_PENDING]);

        return redirect()->route('missing-reports.mine')
            ->with('success', 'Laporan kehilangan dikirim ulang dan menunggu pemeriksaan Admin.');
    }

    private function assertOwnedReport(MissingReport $report): void
    {
        abort_unless($report->user_id === auth()->id(), 403);
    }
}
