<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLostReportRequest;
use App\Http\Requests\UpdateLostReportRequest;
use App\Models\LostReport;
use App\Support\ReportPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LostReportController extends Controller
{
    private const CATEGORIES = ['Elektronik', 'Dokumen', 'Aksesori', 'Pakaian', 'Lainnya'];

    public function create(): View
    {
        return view('pages.reports.create', [
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(StoreLostReportRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'terms_accepted']);
        $data['user_id'] = $request->user()->id;
        $data['photo_path'] = ReportPhoto::store($request->file('photo'));
        $data['terms_accepted_at'] = now();
        $data['status'] = 'pending';

        LostReport::create($data);

        return redirect()->route('reports.mine')
            ->with('success', 'Laporan berhasil dikirim dan menunggu pemeriksaan Admin.');
    }

    public function mine(): View
    {
        return view('pages.reports.mine', [
            'reports' => auth()->user()->lostReports()
                ->with(['latestRejection', 'latestReturnRejection'])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function edit(LostReport $report): View
    {
        $this->assertOwnedReport($report);
        abort_unless(in_array($report->status, ['pending', 'rejected'], true), 403);

        return view('pages.reports.edit', [
            'report' => $report,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(UpdateLostReportRequest $request, LostReport $report): RedirectResponse
    {
        $this->assertOwnedReport($report);

        $data = $request->safe()->except(['photo']);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = ReportPhoto::store($request->file('photo'));
        }
        if ($report->status === 'rejected') {
            $data['status'] = 'pending';
        }

        $report->update($data);

        return redirect()->route('reports.mine')->with(
            'success',
            $report->wasChanged('status')
                ? 'Perubahan disimpan dan laporan dikirim ulang untuk diperiksa Admin.'
                : 'Perubahan laporan berhasil disimpan.',
        );
    }

    public function cancel(LostReport $report): RedirectResponse
    {
        $this->assertOwnedReport($report);
        abort_unless($report->status === 'pending', 403);

        $report->update(['status' => 'cancelled']);

        return redirect()->route('reports.mine')
            ->with('success', 'Laporan dibatalkan dan tidak lagi ditampilkan.');
    }

    public function resubmit(LostReport $report): RedirectResponse
    {
        $this->assertOwnedReport($report);
        abort_unless($report->status === 'rejected', 403);

        $report->update(['status' => 'pending']);

        return redirect()->route('reports.mine')
            ->with('success', 'Laporan dikirim ulang dan menunggu pemeriksaan Admin.');
    }

    private function assertOwnedReport(LostReport $report): void
    {
        abort_unless($report->user_id === auth()->id(), 403);
    }
}
