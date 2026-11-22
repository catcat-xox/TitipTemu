@extends('layouts.admin')

@section('title', 'Cari Laporan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <div class="admin-page-head">
                <div>
                    <h1 class="admin-page-title">Cari laporan</h1>
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
            <form class="filter-panel mb-4" method="GET" action="{{ route('admin.reports.search') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-8">
                        <label class="form-label" for="q">Kata kunci</label>
                        <input class="form-control" id="q" name="q" value="{{ $keyword }}"
                            placeholder="Nama barang, kategori, lokasi, atau pelapor">
                    </div>
                    <div class="col-12 col-lg-4 d-flex flex-wrap justify-content-lg-end gap-2">
                        <button class="btn btn-primary ms-lg-auto" type="submit">Cari</button>
                        @if ($keyword !== '')
                            <a class="btn btn-outline-secondary" href="{{ route('admin.reports.search') }}">Atur ulang</a>
                        @endif
                    </div>
                </div>
            </form>

            <p class="text-muted small mb-3">
                {{ $keyword === ''
                    ? 'Menampilkan semua laporan dari kedua jenis.'
                    : 'Ditemukan '.$reports->total().' laporan untuk “'.$keyword.'”.' }}
            </p>

            @if ($reports->isEmpty())
                <div class="empty-state">
                    <span class="icon-box" aria-hidden="true">⌕</span>
                    <h2>Belum ada laporan yang sesuai</h2>
                    <p>Coba kata kunci lain atau kosongkan pencarian untuk melihat semua laporan.</p>
                </div>
            @else
                <div class="table-responsive surface-card">
                    <table class="table align-middle mb-0">
                        <caption class="visually-hidden">Hasil pencarian laporan lintas jenis dan status</caption>
                        <thead>
                            <tr>
                                <th scope="col">Barang</th>
                                <th scope="col">Jenis</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Pelapor</th>
                                <th scope="col">Status</th>
                                <th scope="col">Dikirim</th>
                                <th scope="col"><span class="visually-hidden">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td>
                                        <strong>{{ $report['item_name'] }}</strong>
                                    </td>
                                    <td>{{ $report['kind_label'] }}</td>
                                    <td>{{ $report['category'] }}</td>
                                    <td>{{ $report['location'] }}</td>
                                    <td>{{ $report['user_name'] }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $report['status'] }}">{{ $report['status_label'] }}</span>
                                    </td>
                                    <td>{{ $report['created_at']->translatedFormat('d M Y H:i') }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ $report['show_url'] }}">Tinjau</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $reports->links() }}</div>
            @endif
        </div>
    </section>
@endsection
