@extends('layouts.app')

@section('title', $report->item_name . ' — TitipTemu')

@section('content')
    <section class="section">
        <div class="container">
            <a class="back-link" href="{{ route('reports.index', $type) }}">← Kembali ke {{ $type === 'lost' ? 'Barang Hilang' : 'Barang Ditemukan' }}</a>
            <div class="row g-5 mt-2 align-items-start">
                <div class="col-lg-6">
                    <div class="detail-image">
                        <img src="{{ asset('storage/' . $report->photo_path) }}" alt="Foto {{ $report->item_name }}">
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="status-badge">{{ $type === 'lost' ? 'Masih dicari' : 'Sudah kembali' }}</span>
                    <h1 class="mt-3">{{ $report->item_name }}</h1>
                    <p class="page-intro">{{ $report->description }}</p>
                    <dl class="detail-list">
                        <div><dt>Kategori</dt><dd>{{ $report->category }}</dd></div>
                        <div><dt>Tanggal kehilangan</dt><dd>{{ $report->lost_at->translatedFormat('d F Y') }}</dd></div>
                        <div><dt>Lokasi</dt><dd>{{ $report->location }}</dd></div>
                    </dl>
                    <div class="privacy-note">
                        <strong>Informasi aman untuk publik</strong>
                        <p class="mb-0">Kontak pemilik dan bukti pengembalian tidak ditampilkan di halaman ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
