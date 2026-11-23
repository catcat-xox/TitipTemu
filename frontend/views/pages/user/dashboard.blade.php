@extends('layouts.public')

@section('title', 'Akun — TitipTemu')

@section('content')
    @php
        $user = auth()->user();
        $statusLabels = [
            'pending' => 'Menunggu pemeriksaan',
            'approved' => 'Masih dicari',
            'rejected' => 'Ditolak',
            'return_pending' => 'Pengembalian diperiksa',
            'returned' => 'Sudah kembali',
            'archived' => 'Diarsipkan',
            'cancelled' => 'Dibatalkan',
        ];
        $missingStatusLabels = [
            'pending' => 'Menunggu pemeriksaan',
            'approved' => 'Barang Belum Ditemukan',
            'rejected' => 'Ditolak',
            'found_pending' => 'Konfirmasi diperiksa',
            'found' => 'Barang Sudah Ditemukan',
            'archived' => 'Diarsipkan',
            'cancelled' => 'Dibatalkan',
        ];
        $typeLabels = [
            'student' => 'Mahasiswa',
            'lecturer' => 'Dosen',
            'staff' => 'Staf',
        ];
    @endphp

    <section class="section">
        <div class="container">
            <h1 class="visually-hidden">Akun</h1>
            <div class="surface-card profile-card p-3 p-md-4 mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-9 d-flex align-items-center gap-3">
                        <div class="profile-avatar flex-shrink-0" aria-hidden="true">
                            @if ($user->profile_photo_path)
                                <img src="{{ route('user.profile-photo', ['v' => md5($user->profile_photo_path)]) }}" alt="">
                            @else
                                {{ mb_substr($user->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="profile-info">
                            <h2>{{ $user->name }}</h2>
                            <p>{{ $user->email }}</p>
                            @if ($user->phone)
                                <p>No. HP: {{ $user->phone }}</p>
                            @endif
                            <p>
                                {{ $typeLabels[$user->user_type] ?? 'Civitas akademika' }} · {{ $user->faculty }}
                                @if ($user->study_program)
                                    · {{ $user->study_program }}
                                @endif
                            </p>
                            <p>NIM/NIP: {{ $user->nim_nip }}</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="d-grid gap-2">
                            <a class="btn btn-outline-primary" href="{{ route('user.profile.edit') }}">Edit profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary w-100">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h2 class="h4 mb-1">Laporan saya</h2>
                <p class="text-muted mb-0">Pantau status laporan kehilangan dan laporan temuan milikmu.</p>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="h6 fw-semibold text-muted mb-0">Laporan kehilangan</h3>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('missing-reports.mine') }}">Kelola laporan kehilangan</a>
            </div>
            @if ($missingReports->isEmpty())
                <div class="empty-state">
                    <h4>Belum ada laporan kehilangan</h4>
                    <p>Gunakan menu <strong>Buat Laporan</strong> di navbar untuk menambah laporan kehilangan.</p>
                </div>
            @else
                <div class="table-responsive surface-card">
                    <table class="table align-middle mb-0">
                        <caption class="visually-hidden">Daftar laporan kehilangan milik saya</caption>
                        <thead><tr>
                            <th scope="col" class="text-center">Barang</th>
                            <th scope="col" class="text-center">Tanggal hilang</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center"><span class="visually-hidden">Aksi</span></th>
                        </tr></thead>
                        <tbody>
                            @foreach ($missingReports as $report)
                                <tr>
                                    <td><strong>{{ $report->item_name }}</strong><br><small>{{ $report->category }}</small></td>
                                    <td class="text-center">{{ $report->lost_at->translatedFormat('d M Y') }}</td>
                                    <td class="text-center">
                                        <span class="status-badge status-{{ $report->status }}">{{ $missingStatusLabels[$report->status] ?? ucfirst(str_replace('_', ' ', $report->status)) }}</span>
                                        @if ($report->status === 'rejected' && $report->latestRejection)
                                            <div class="small text-danger mt-1">Alasan tim kami: {{ $report->latestRejection->reason }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($report->status === 'approved')
                                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" data-tt-action-menu
                                                        aria-expanded="false"
                                                        aria-label="Aksi untuk {{ $report->item_name }}">
                                                        Aksi
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end tt-action-menu">
                                                        <li>
                                                            <form method="POST" action="{{ route('missing-reports.cancel', $report) }}"
                                                                data-tt-confirm="Batalkan laporan kehilangan ini? Laporan tidak lagi ditampilkan."
                                                                data-tt-confirm-title="Batalkan laporan kehilangan?"
                                                                data-tt-confirm-label="Ya, batalkan"
                                                                data-tt-confirm-variant="primary">
                                                                @csrf
                                                                <button class="dropdown-item" type="submit">Batalkan</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('missing-reports.mine') }}">Lihat semua</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <form method="POST" action="{{ route('missing-reports.found', $report) }}"
                                                    data-tt-confirm="Tandai barang ini sudah ditemukan? Laporan akan dipindah ke Barang Sudah Ditemukan selama 30 hari."
                                                    data-tt-confirm-title="Tandai barang sudah ditemukan?"
                                                    data-tt-confirm-label="Ya, tandai ditemukan"
                                                    data-tt-confirm-variant="success">
                                                    @csrf
                                                    <button class="btn btn-sm btn-primary" type="submit">Barang ditemukan</button>
                                                </form>
                                            </div>
                                        @else
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('missing-reports.mine') }}">Lihat semua</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $missingReports->links() }}</div>
            @endif

            <div class="mt-5 d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="h6 fw-semibold text-muted mb-0">Laporan barang temuan</h3>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('reports.mine') }}">Kelola laporan temuan</a>
            </div>
            @if ($reports->isEmpty())
                <div class="empty-state">
                    <h4>Belum ada laporan temuan</h4>
                    <p>Gunakan menu <strong>Buat Laporan</strong> di navbar untuk menambah laporan temuan.</p>
                </div>
            @else
                <div class="table-responsive surface-card">
                    <table class="table align-middle mb-0">
                        <caption class="visually-hidden">Aktivitas laporan barang temuan milik saya</caption>
                        <thead><tr>
                            <th scope="col" class="text-center">Barang</th>
                            <th scope="col" class="text-center">Tanggal ditemukan</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center"><span class="visually-hidden">Aksi</span></th>
                        </tr></thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td><strong>{{ $report->item_name }}</strong><br><small>{{ $report->category }}</small></td>
                                    <td class="text-center">{{ $report->lost_at->translatedFormat('d M Y') }}</td>
                                    <td class="text-center">
                                        <span class="status-badge status-{{ $report->status }}">{{ $statusLabels[$report->status] ?? ucfirst(str_replace('_', ' ', $report->status)) }}</span>
                                        @if ($report->status === 'rejected' && $report->latestRejection)
                                            <div class="small text-danger mt-1">Alasan tim kami: {{ $report->latestRejection->reason }}</div>
                                        @endif
                                        @if ($report->status === 'approved' && $report->latestReturnRejection)
                                            <div class="small text-danger mt-1">Bukti ditolak: {{ $report->latestReturnRejection->rejection_reason }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('reports.mine') }}">Lihat semua</a>
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
