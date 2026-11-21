@extends('layouts.app')

@section('title', 'Buat Laporan Kehilangan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <span class="eyebrow">Untuk User</span>
            <h1 class="mt-3">Buat laporan kehilangan</h1>
            <p class="page-intro">Lengkapi informasi agar Admin dapat memeriksa laporanmu sebelum dipublikasikan.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            @include('partials.lost-report-form', [
                'action' => route('reports.store'),
                'report' => null,
                'submitLabel' => 'Kirim untuk ditinjau',
            ])
        </div>
    </section>
@endsection
