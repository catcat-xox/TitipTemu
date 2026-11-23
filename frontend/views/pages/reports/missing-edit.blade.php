@extends('layouts.public')

@section('title', 'Ubah Laporan Kehilangan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Ubah laporan kehilangan</h1>
            <p class="page-intro">Perbarui data laporan sebelum Admin meninjau kembali pengajuanmu.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            @include('partials.missing-report-form', [
                'action' => route('missing-reports.update', $report),
                'report' => $report,
                'submitLabel' => 'Simpan perubahan',
            ])
        </div>
    </section>
@endsection
