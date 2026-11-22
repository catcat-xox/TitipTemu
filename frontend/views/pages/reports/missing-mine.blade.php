@extends('layouts.public')

@section('title', 'Laporan Kehilangan Saya — TitipTemu')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Menunggu pemeriksaan',
            'approved' => 'Barang Belum Ditemukan',
            'rejected' => 'Ditolak',
            'found_pending' => 'Konfirmasi diperiksa',
            'found' => 'Barang Sudah Ditemukan',
            'archived' => 'Diarsipkan',
            'cancelled' => 'Dibatalkan',
        ];
    @endphp
    <section class="page-hero">
        <div class="container">
            <h1>Laporan kehilangan saya</h1>
            <p class="page-intro">Pantau status laporan kehilangan yang kamu kirimkan.</p>
        </div>
    </section>
    <section class="section pt-0">
        <div class="container">
            @if ($reports->isEmpty())
                <div class="empty-state"><h2>Belum ada laporan kehilangan</h2><p>Mulai dengan melaporkan barang yang kamu hilangkan.</p>
                    <a class="btn btn-primary" href="{{ route('missing-reports.create') }}">Laporkan Kehilangan Barang</a>
                </div>
            @else
                <div class="table-responsive surface-card">
                    <table class="table align-middle mb-0">
                        <caption class="visually-hidden">Daftar laporan kehilangan milik saya</caption>
                        <thead><tr>
                            <th scope="col">Barang</th>
                            <th scope="col">Tanggal hilang</th>
                            <th scope="col">Status</th>
                            <th scope="col"><span class="visually-hidden">Aksi</span></th>
                        </tr></thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td><strong>{{ $report->item_name }}</strong><br><small>{{ $report->category }}</small></td>
                                    <td>{{ $report->lost_at->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $report->status }}">{{ $statusLabels[$report->status] ?? ucfirst(str_replace('_', ' ', $report->status)) }}</span>
                                        @if ($report->status === 'rejected' && $report->latestRejection)
                                            <div class="small text-danger mt-1">Alasan Admin: {{ $report->latestRejection->reason }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                                            @if (in_array($report->status, ['pending', 'rejected'], true))
                                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('missing-reports.edit', $report) }}">Ubah</a>
                                            @endif
                                            @if ($report->status === 'pending')
                                                <form method="POST" action="{{ route('missing-reports.cancel', $report) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-secondary" type="submit">Batalkan</button>
                                                </form>
                                            @endif
                                            @if ($report->status === 'rejected')
                                                <form method="POST" action="{{ route('missing-reports.resubmit', $report) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-primary" type="submit">Kirim ulang</button>
                                                </form>
                                            @endif
                                            @if ($report->isPublic())
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('reports.show', ['lost', $report]) }}">Detail</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $reports->links() }}</div>
            @endif
            <div class="mt-4 d-flex flex-wrap gap-2">
                <a class="btn btn-primary" href="{{ route('missing-reports.create') }}">Laporkan Kehilangan Barang</a>
                <a class="btn btn-primary" href="{{ route('reports.create') }}">Laporkan Barang Temuan</a>
            </div>
        </div>
    </section>
@endsection
