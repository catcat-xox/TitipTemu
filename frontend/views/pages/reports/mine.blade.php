@extends('layouts.app')

@section('title', 'Laporan Saya — TitipTemu')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Menunggu pemeriksaan',
            'approved' => 'Masih dicari',
            'rejected' => 'Ditolak',
            'return_pending' => 'Pengembalian diperiksa',
            'returned' => 'Sudah kembali',
            'archived' => 'Diarsipkan',
            'cancelled' => 'Dibatalkan',
        ];
    @endphp
    <section class="page-hero">
        <div class="container">
            <span class="eyebrow">Area User</span>
            <h1 class="mt-3">Laporan saya</h1>
            <p class="page-intro">Pantau status laporan kehilangan yang kamu kirimkan.</p>
        </div>
    </section>
    <section class="section pt-0">
        <div class="container">
            @if ($reports->isEmpty())
                <div class="empty-state"><h2>Belum ada laporan</h2><p>Mulai dengan membuat laporan kehilangan.</p>
                    <a class="btn btn-primary" href="{{ route('reports.create') }}">Buat laporan</a>
                </div>
            @else
                <div class="table-responsive surface-card">
                    <table class="table align-middle mb-0">
                        <caption class="visually-hidden">Daftar laporan kehilangan milik saya</caption>
                        <thead><tr>
                            <th scope="col">Barang</th>
                            <th scope="col">Tanggal</th>
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
                                        @if ($report->status === 'approved' && $report->latestReturnRejection)
                                            <div class="small text-danger mt-1">Bukti ditolak: {{ $report->latestReturnRejection->rejection_reason }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                                            @if (in_array($report->status, ['pending', 'rejected'], true))
                                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('reports.edit', $report) }}">Ubah</a>
                                            @endif
                                            @if ($report->status === 'pending')
                                                <form method="POST" action="{{ route('reports.cancel', $report) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-secondary" type="submit">Batalkan</button>
                                                </form>
                                            @endif
                                            @if ($report->status === 'rejected')
                                                <form method="POST" action="{{ route('reports.resubmit', $report) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-primary" type="submit">Kirim ulang</button>
                                                </form>
                                            @endif
                                            @if ($report->status === 'approved')
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('reports.return.create', $report) }}">Konfirmasi kembali</a>
                                            @endif
                                            @if ($report->isPublic())
                                                <a class="btn btn-sm btn-link" href="{{ route('reports.show', [$report->status === 'returned' ? 'found' : 'lost', $report]) }}">Detail</a>
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
        </div>
    </section>
@endsection
