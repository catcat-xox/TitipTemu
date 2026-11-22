@extends('layouts.admin')

@section('title', 'Edit Laporan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <a class="back-link" href="{{ route('admin.reports.show', $report) }}">← Kembali ke tinjauan</a>
            <h1 class="mt-3">Edit laporan Pending</h1>
            <p class="page-intro">Perubahan yang Anda simpan langsung diterapkan dan tercatat pada riwayat verifikasi.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            @include('partials.lost-report-form', [
                'action' => route('admin.reports.update', $report),
                'report' => $report,
                'submitLabel' => 'Simpan perubahan Admin',
            ])
        </div>
    </section>
@endsection
