@extends('layouts.admin')

@section('title', 'Dashboard Admin — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <div class="admin-page-head">
                <div>
                    <h1 class="admin-page-title">Dashboard Admin</h1>
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
            <div class="admin-stat-grid mb-4">
                @foreach ($cards as $filter => $card)
                    <a class="admin-stat-card surface-card admin-stat-link {{ $currentStatus === $filter ? 'active' : '' }}"
                        href="{{ route('admin.dashboard', ['jenis' => $currentJenis, 'status' => $filter]) }}"
                        @if ($currentStatus === $filter) aria-current="page" @endif>
                        <span class="card-label">{{ $card['label'] }}</span>
                        <h2 class="mt-2 mb-0">{{ $card['total'] }}</h2>
                    </a>
                @endforeach
            </div>

            <form class="filter-panel mb-4" method="GET" action="{{ route('admin.dashboard') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label" for="jenis">Jenis laporan</label>
                        <select class="form-select" id="jenis" name="jenis" onchange="this.form.submit()"
                            aria-describedby="hint-jenis">
                            @foreach ($jenisLabels as $value => $label)
                                <option value="{{ $value }}" @selected($currentJenis === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="form-text" id="hint-jenis">Perubahan jenis langsung diterapkan.</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label" for="status">Status laporan</label>
                        <select class="form-select" id="status" name="status">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected($currentStatus === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-4 d-flex justify-content-lg-end">
                        <button class="btn btn-primary ms-lg-auto" type="submit">Terapkan</button>
                    </div>
                </div>
            </form>

            @if ($reports->isEmpty())
                <div class="empty-state">
                    <span class="icon-box" aria-hidden="true">⌕</span>
                    <h2>Belum ada laporan {{ strtolower($currentStatusLabel) }}</h2>
                    <p>Coba pilih status lain atau tunggu laporan baru masuk.</p>
                </div>
            @else
                <div class="table-responsive surface-card">
                    <table class="table table-admin-compact align-middle mb-0">
                        <caption class="visually-hidden">Laporan {{ $jenisLabels[$currentJenis] }} dengan status {{ $currentStatusLabel }}</caption>
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">Barang</th>
                                <th scope="col" class="text-center">Jenis</th>
                                <th scope="col" class="text-center">Kategori</th>
                                <th scope="col" class="text-center">Lokasi</th>
                                <th scope="col" class="text-center">Tanggal</th>
                                <th scope="col" class="text-center">Pelapor</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Dikirim</th>
                                <th scope="col" class="text-center"><span class="visually-hidden">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td><strong>{{ $report['item_name'] }}</strong></td>
                                    <td>{{ $report['kind_label'] }}</td>
                                    <td>{{ $report['category'] }}</td>
                                    <td>{{ $report['location'] }}</td>
                                    <td class="text-nowrap">{{ $report['lost_at']->translatedFormat('d M Y') }}</td>
                                    <td>{{ $report['user_name'] }}</td>
                                    <td class="text-nowrap">
                                        <span class="status-badge status-{{ $report['status'] }}">{{ $report['status_label'] }}</span>
                                    </td>
                                    <td class="text-nowrap">{{ $report['created_at']->translatedFormat('d M Y H:i') }}</td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" data-tt-action-menu
                                                aria-expanded="false"
                                                aria-label="Aksi untuk {{ $report['item_name'] }}">
                                                Aksi
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end tt-action-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ $report['show_url'] }}">Tinjau</a>
                                                </li>
                                                @if ($report['edit_url'])
                                                    <li>
                                                        <a class="dropdown-item" href="{{ $report['edit_url'] }}">Edit</a>
                                                    </li>
                                                @endif
                                                @if ($report['delete_url'])
                                                    <li>
                                                        <form method="POST" action="{{ $report['delete_url'] }}"
                                                            data-tt-confirm="Hapus permanen laporan ini beserta buktinya? Tindakan ini tidak dapat dibatalkan."
                                                            data-tt-confirm-title="Hapus permanen laporan?"
                                                            data-tt-confirm-label="Ya, hapus permanen"
                                                            data-tt-confirm-variant="danger">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="dropdown-item text-danger" type="submit">Hapus</button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
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
