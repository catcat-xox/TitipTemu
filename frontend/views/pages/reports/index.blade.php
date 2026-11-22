@extends('layouts.public')

@php
    $isLost = $type === 'lost';
    $title = $labels['title'];
@endphp

@section('title', $title . ' — TitipTemu')

@section('content')
    <section class="section reports-listing">
        <div class="container">
            <h1 class="visually-hidden">{{ $title }}</h1>

            <nav class="row g-3 mb-4" aria-label="Pilih status {{ $title }}">
                @foreach (['belum', 'sudah'] as $option)
                    <div class="col-md-6">
                        <a class="state-card{{ $state === $option ? ' is-active' : '' }}"
                            href="{{ route('reports.index', [$type, 'status' => $option]) }}"
                            @if ($state === $option) aria-current="page" @endif>
                            <span class="state-card-label">{{ $labels[$option] }}</span>
                            <span class="state-card-count">{{ $counts[$option] }} laporan</span>
                            <span class="state-card-arrow" aria-hidden="true">→</span>
                        </a>
                    </div>
                @endforeach
            </nav>

            <form class="filter-panel reports-filter-panel mb-4" method="GET" action="{{ route('reports.index', $type) }}">
                <input type="hidden" name="status" value="{{ $state }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label" for="q">Cari {{ $title }}</label>
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
                    <p>Coba gunakan kata kunci atau filter lain, atau pilih card status lainnya.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($reports as $report)
                        <div class="col-md-6 col-xl-4">
                            <article class="report-card">
                                <div class="report-image">
                                    <img src="{{ route($isLost ? 'missing-reports.photo' : 'reports.photo', $report) }}"
                                        alt="Foto {{ $report->item_name }}" loading="lazy">
                                </div>
                                <div class="report-card-body">
                                    <div class="d-flex justify-content-between gap-2 align-items-start">
                                        <span class="status-badge">{{ $labels[$state] }}</span>
                                        <span class="report-date">{{ $report->lost_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <h2>{{ $report->item_name }}</h2>
                                    <p class="report-meta">{{ $report->category }} · {{ $report->location }}</p>
                                    <p class="report-excerpt">{{ Str::limit($report->description, 120) }}</p>
                                    <div class="mt-auto pt-2">
                                        <a class="btn btn-sm btn-outline-primary"
                                            href="{{ route('reports.show', [$type, $report]) }}">
                                            Lihat detail <span aria-hidden="true">→</span>
                                        </a>
                                    </div>
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
