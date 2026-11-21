@extends('layouts.app')

@section('title', 'Ubah Laporan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <span class="eyebrow">Area User</span>
            <h1 class="mt-3">Ubah laporan kehilangan</h1>
            <p class="page-intro">Perbaiki informasi laporan sebelum dikirim kembali untuk diperiksa Admin.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            @include('partials.lost-report-form', [
                'action' => route('reports.update', $report),
                'report' => $report,
                'submitLabel' => $report->status === 'rejected' ? 'Simpan dan kirim ulang' : 'Simpan perubahan',
            ])
        </div>
    </section>
@endsection
