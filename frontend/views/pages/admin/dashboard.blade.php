@extends('layouts.app')

@section('title', 'Dashboard Admin — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <span class="eyebrow">Area Admin</span>
            <h1 class="mt-3">Dashboard Admin</h1>
            <p class="page-intro">Tinjau antrean laporan kehilangan yang masuk untuk dimoderasi.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            <div class="row g-3 mb-4">
                @foreach ($statusLabels as $status => $label)
                    <div class="col-6 col-lg-3">
                        <div class="surface-card h-100">
                            <span class="card-label">{{ $label }}</span>
                            <h2 class="mt-2 mb-0">{{ $counts->get($status, 0) }}</h2>
                        </div>
                    </div>
                @endforeach
            </div>

            <form class="filter-panel mb-4" method="GET" action="{{ route('admin.dashboard') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label" for="status">Status laporan</label>
                        <select class="form-select" id="status" name="status">
                            @foreach ($statusLabels as $status => $label)
                                <option value="{{ $status }}" @selected($currentStatus === $status)>
                                    {{ $label }} ({{ $counts->get($status, 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-2 d-grid">
                        <button class="btn btn-primary" type="submit">Terapkan</button>
                    </div>
                </div>
            </form>

            @if ($reports->isEmpty())
                <div class="empty-state">
                    <span class="icon-box" aria-hidden="true">⌕</span>
                    <h2>Belum ada laporan {{ strtolower($statusLabels[$currentStatus]) }}</h2>
                    <p>Coba pilih status lain atau tunggu laporan baru masuk.</p>
                </div>
            @else
                <div class="table-responsive surface-card">
                    <table class="table align-middle mb-0">
                        <caption class="visually-hidden">Antrean laporan kehilangan {{ $statusLabels[$currentStatus] }}</caption>
                        <thead>
                            <tr>
                                <th scope="col">Barang</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Tanggal hilang</th>
                                <th scope="col">Pelapor</th>
                                <th scope="col">Status</th>
                                <th scope="col">Dikirim</th>
                                <th scope="col"><span class="visually-hidden">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td><strong>{{ $report->item_name }}</strong></td>
                                    <td>{{ $report->category }}</td>
                                    <td>{{ $report->location }}</td>
                                    <td>{{ $report->lost_at->translatedFormat('d M Y') }}</td>
                                    <td>{{ $report->user?->name ?? '-' }}</td>
                                    <td><span class="status-badge status-{{ $report->status }}">{{ $statusLabels[$report->status] ?? $report->status }}</span></td>
                                    <td>{{ $report->created_at->translatedFormat('d M Y H:i') }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.reports.show', $report) }}">Tinjau</a>
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
