@extends('layouts.public')

@php
    $isLost = $type === 'hilang';
@endphp

@section('title', $report->item_name . ' — TitipTemu')

@section('content')
    <section class="section">
        <div class="container">
            <a class="back-link" href="{{ route('reports.index', [$type, 'status' => $state]) }}">← Kembali ke {{ $labels['title'] }}</a>
            <div class="row g-5 mt-2 align-items-start">
                <div class="col-lg-6">
                    <div class="detail-image">
                        <img src="{{ route($isLost ? 'missing-reports.photo' : 'reports.photo', $report) }}"
                            alt="Foto {{ $report->item_name }}">
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="status-badge">{{ $labels[$state] }}</span>
                    <h1 class="mt-3">{{ $report->item_name }}</h1>
                    <p class="page-intro">{{ $report->description }}</p>
                    <dl class="detail-list">
                        <div><dt>Kategori</dt><dd>{{ $report->category }}</dd></div>
                        <div><dt>Tanggal {{ $isLost ? 'hilang' : 'ditemukan' }}</dt><dd>{{ $report->lost_at->translatedFormat('d F Y') }}</dd></div>
                        <div><dt>Lokasi {{ $isLost ? 'hilang' : 'ditemukan' }}</dt><dd>{{ $report->location }}</dd></div>
                    </dl>
                    @if (filled($report->contact_phone))
                        <a class="btn btn-primary btn-lg" href="{{ route($isLost ? 'missing-reports.contact' : 'reports.contact', $report) }}">
                            {{ $labels['contact'] }}
                        </a>
                    @endif
                    <div class="privacy-note mt-4">
                        <strong>Informasi aman untuk publik</strong>
                        <p class="mb-0">Nomor HP pemilik maupun penemu tidak ditampilkan. Tombol WhatsApp hanya membuka chat, tanpa menampilkan nomor di halaman ini. Nama pemilik dan foto bersama juga tetap privat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
