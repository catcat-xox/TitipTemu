@extends('layouts.app')

@php
    $isLost = $type === 'lost';
    $title = $isLost ? 'Barang Hilang' : 'Barang Ditemukan';
@endphp

@section('title', $title . ' — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <span class="eyebrow">{{ $isLost ? 'Daftar aktif' : 'Riwayat pengembalian' }}</span>
            <h1 class="mt-3">{{ $title }}</h1>
            <p class="page-intro">
                {{ $isLost
                    ? 'Cari laporan kehilangan yang telah disetujui Admin.'
                    : 'Lihat laporan yang pengembaliannya telah disetujui Admin dalam 30 hari terakhir.' }}
            </p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            <form class="filter-panel mb-4" method="GET" action="{{ route('reports.index', $type) }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label" for="q">Cari barang</label>
                        <input class="form-control" id="q" name="q" value="{{ request('q') }}"
                            placeholder="Nama, kategori, atau lokasi">
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label" for="category">Kategori</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label" for="lost_date">Tanggal</label>
                        <input class="form-control" type="date" id="lost_date" name="lost_date"
                            value="{{ request('lost_date') }}">
                    </div>
                    <div class="col-md-4 col-lg-2 d-grid">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </div>
            </form>

            @if ($reports->isEmpty())
                <div class="empty-state">
                    <span class="icon-box" aria-hidden="true">⌕</span>
                    <h2>Belum ada laporan yang sesuai</h2>
                    <p>Coba gunakan kata kunci atau filter lain.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($reports as $report)
                        <div class="col-md-6 col-xl-4">
                            <article class="report-card">
                                <div class="report-image">
                                    <img src="{{ asset('storage/' . $report->photo_path) }}"
                                        alt="Foto {{ $report->item_name }}" loading="lazy">
                                </div>
                                <div class="report-card-body">
                                    <div class="d-flex justify-content-between gap-2 align-items-start">
                                        <span class="status-badge">{{ $isLost ? 'Masih dicari' : 'Sudah kembali' }}</span>
                                        <span class="report-date">{{ $report->lost_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <h2>{{ $report->item_name }}</h2>
                                    <p class="report-meta">{{ $report->category }} · {{ $report->location }}</p>
                                    <p class="report-excerpt">{{ Str::limit($report->description, 120) }}</p>
                                    <a class="stretched-link" href="{{ route('reports.show', [$type, $report]) }}">
                                        Lihat detail <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5">{{ $reports->links() }}</div>
            @endif
        </div>
    </section>
@endsection
