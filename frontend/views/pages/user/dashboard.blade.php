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
        $typeLabels = [
            'student' => 'Mahasiswa',
            'lecturer' => 'Dosen',
            'staff' => 'Staf',
        ];
    @endphp

    <section class="section">
        <div class="container">
            <div class="surface-card profile-card p-3 p-md-4 mb-4 d-flex flex-column flex-md-row align-items-md-center">
                <div class="profile-avatar mb-3 mb-md-0" aria-hidden="true">
                    @if ($user->profile_photo_path)
                        <img src="{{ route('user.profile-photo') }}" alt="">
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

            <div class="surface-card p-3 p-md-4 mb-4">
                <h2 class="h5 mb-3">Ubah profil</h2>
                <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data"
                    class="profile-form">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Nama</label>
                            <input id="name" name="name" type="text"
                                value="{{ old('name', $user->name) }}"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="phone">No. HP</label>
                            <input id="phone" name="phone" type="tel"
                                value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                                class="form-control @error('phone') is-invalid @enderror" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="nim_nip">NIM/NIP</label>
                            <input id="nim_nip" name="nim_nip" type="text"
                                value="{{ old('nim_nip', $user->nim_nip) }}"
                                class="form-control @error('nim_nip') is-invalid @enderror" required>
                            @error('nim_nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="user_type">Status civitas</label>
                            <select id="user_type" name="user_type"
                                class="form-select @error('user_type') is-invalid @enderror" required>
                                @foreach ($typeLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('user_type', $user->user_type) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('user_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="faculty">Fakultas</label>
                            <input id="faculty" name="faculty" type="text"
                                value="{{ old('faculty', $user->faculty) }}"
                                class="form-control @error('faculty') is-invalid @enderror" required>
                            @error('faculty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="study_program">Program studi <span class="text-muted fw-normal">(wajib diisi mahasiswa)</span></label>
                            <input id="study_program" name="study_program" type="text"
                                value="{{ old('study_program', $user->study_program) }}"
                                class="form-control @error('study_program') is-invalid @enderror">
                            @error('study_program')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="profile_photo">Foto profil <span class="text-muted fw-normal">(opsional)</span></label>
                            <input id="profile_photo" name="profile_photo" type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="form-control @error('profile_photo') is-invalid @enderror">
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Simpan profil</button>
                    </div>
                </form>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <h2 class="h4 mb-0">Laporan terbaru</h2>
                <a class="btn btn-primary" href="{{ route('reports.create') }}">Laporkan Barang Temuan</a>
            </div>

            @if ($reports->isEmpty())
                <div class="empty-state">
                    <h2>Belum ada aktivitas laporan</h2>
                    <p>Laporkan barang yang Anda temukan agar pemiliknya lebih mudah mencarinya.</p>
                </div>
            @else
                <div class="table-responsive surface-card">
                    <table class="table align-middle mb-0">
                        <caption class="visually-hidden">Aktivitas laporan barang temuan milik saya</caption>
                        <thead><tr>
                            <th scope="col">Barang</th>
                            <th scope="col">Tanggal ditemukan</th>
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
