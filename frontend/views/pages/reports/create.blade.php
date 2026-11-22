@extends('layouts.public')

@section('title', 'Laporkan Barang Temuan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Laporkan barang temuan</h1>
            <p class="page-intro">Lengkapi data penemu dan informasi barang agar Admin dapat memeriksa laporanmu sebelum dipublikasikan.</p>
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
