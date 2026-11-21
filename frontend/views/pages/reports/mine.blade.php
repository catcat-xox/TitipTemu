@extends('layouts.app')

@section('title', 'Laporan Saya — TitipTemu')

@section('content')
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
                        <thead><tr><th>Barang</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td><strong>{{ $report->item_name }}</strong><br><small>{{ $report->category }}</small></td>
                                    <td>{{ $report->lost_at->translatedFormat('d M Y') }}</td>
                                    <td><span class="status-badge status-{{ $report->status }}">{{ str_replace('_', ' ', ucfirst($report->status)) }}</span></td>
                                    <td><a href="{{ route('reports.show', ['lost', $report]) }}">Detail</a></td>
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
