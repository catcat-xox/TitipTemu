@extends('layouts.public')

@section('title', 'Laporkan Kehilangan Barang — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Laporkan kehilangan barang</h1>
            <p class="page-intro">Lengkapi data pemilik dan informasi barang agar Admin dapat memeriksa laporanmu sebelum dipublikasikan.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            @include('partials.missing-report-form', [
                'action' => route('missing-reports.store'),
                'report' => null,
                'submitLabel' => 'Kirim untuk ditinjau',
            ])
        </div>
    </section>
@endsection
