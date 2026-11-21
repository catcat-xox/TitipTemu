<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLostReportRequest;
use App\Models\LostReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LostReportController extends Controller
{
    public function create(): View
    {
        return view('pages.reports.create', [
            'categories' => ['Elektronik', 'Dokumen', 'Aksesori', 'Pakaian', 'Lainnya'],
        ]);
    }

    public function store(StoreLostReportRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'terms_accepted']);
        $data['user_id'] = $request->user()->id;
        $data['photo_path'] = $request->file('photo')->store('lost-reports', 'public');
        $data['terms_accepted_at'] = now();
        $data['status'] = 'pending';

        LostReport::create($data);

        return redirect()->route('reports.mine')
            ->with('success', 'Laporan berhasil dikirim dan menunggu pemeriksaan Admin.');
    }

    public function mine(): View
    {
        return view('pages.reports.mine', [
            'reports' => auth()->user()->lostReports()->latest()->paginate(10),
        ]);
    }
}
