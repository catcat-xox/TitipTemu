@extends('layouts.admin')

@section('title', 'Edit Laporan Kehilangan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <div class="admin-page-head">
                <div>
                    <h1 class="admin-page-title">Edit laporan kehilangan Pending</h1>
                </div>
                <button type="button" class="btn btn-outline-secondary admin-back-button"
                    onclick="history.back()" aria-label="Kembali ke halaman sebelumnya" title="Kembali">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 18 9 12l6-6" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            @include('partials.missing-report-form', [
                'action' => route('admin.reports.missing.update', $report),
                'report' => $report,
                'submitLabel' => 'Simpan perubahan Admin',
                'backUrl' => route('admin.reports.missing.show', $report),
            ])
        </div>
    </section>
@endsection
