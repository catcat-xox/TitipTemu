@extends('layouts.admin')

@section('title', 'Tinjau '.$report->item_name.' — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <div class="admin-page-head">
                <div>
                    <h1 class="admin-page-title">Tinjau laporan kehilangan</h1>
                    <p class="page-intro">Periksa detail laporan, foto, dan riwayat verifikasi sebelum mengambil keputusan.</p>
                </div>
                <button type="button" class="btn btn-outline-secondary admin-back-button"
                    onclick="history.back()" aria-label="Kembali ke halaman sebelumnya" title="Kembali">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 18 9 12l6-6" />
                    </svg>
                </button>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                @if ($report->status === 'pending')
                    <a class="btn btn-outline-primary" href="{{ route('admin.reports.missing.edit', $report) }}">Edit laporan</a>
                @endif
            </div>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <div class="detail-image detail-image-admin">
                        <img src="{{ route('admin.reports.missing.photo', $report) }}" alt="Foto {{ $report->item_name }}">
                    </div>
                    <p class="mt-3 small text-muted">Foto laporan disajikan melalui tautan privat yang hanya dapat diakses Admin.</p>
                </div>
                <div class="col-lg-7">
                    <div class="surface-card p-4">
                        <span class="status-badge status-{{ $report->status }}">{{ $statusLabels[$report->status] ?? $report->status }}</span>
                        <h1 class="mt-3 h2">{{ $report->item_name }}</h1>
                        <p>{{ $report->description }}</p>
                        <dl class="detail-list">
                            <div><dt>Kategori</dt><dd>{{ $report->category }}</dd></div>
                            <div><dt>Lokasi hilang</dt><dd>{{ $report->location }}</dd></div>
                            <div><dt>Tanggal hilang</dt><dd>{{ $report->lost_at->translatedFormat('d F Y') }}</dd></div>
                            <div><dt>Pelapor</dt><dd>{{ $report->user?->name ?? '-' }}</dd></div>
                            <div><dt>No. HP pemilik</dt><dd>{{ $report->contact_phone ?? '-' }}</dd></div>
                            <div><dt>Dikirim</dt><dd>{{ $report->created_at->translatedFormat('d M Y H:i') }}</dd></div>
                            @if ($report->archived_at)
                                <div><dt>Diarsipkan</dt><dd>{{ $report->archived_at->translatedFormat('d M Y H:i') }}</dd></div>
                            @endif
                        </dl>
                        <div class="privacy-note">
                            <strong>Data privat</strong>
                            <p class="mb-0">Nomor identitas, email, dan catatan internal tidak ditampilkan pada halaman publik.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-lg-7">
                    @if ($report->status === 'pending')
                        <form class="form-panel" method="POST" action="{{ route('admin.reports.missing.verify', $report) }}">
                            @csrf
                            <fieldset>
                                <legend class="h4">Keputusan verifikasi</legend>
                                <p class="text-muted small">Pilih satu keputusan. Penolakan wajib disertai alasan yang dapat diperbaiki pelapor.</p>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="decision" id="decision-approve" value="approve" @checked(old('decision') === 'approve')>
                                    <label class="form-check-label" for="decision-approve">Setujui dan tampilkan di Barang Hilang</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="decision" id="decision-reject" value="reject" @checked(old('decision') === 'reject')>
                                    <label class="form-check-label" for="decision-reject">Tolak dan minta perbaikan</label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="reason">Alasan penolakan</label>
                                    <textarea class="form-control" id="reason" name="reason" rows="3" maxlength="500">{{ old('reason') }}</textarea>
                                    <div class="form-text">Wajib diisi jika laporan ditolak.</div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="note">Catatan internal Admin</label>
                                    <textarea class="form-control" id="note" name="note" rows="2" maxlength="500">{{ old('note') }}</textarea>
                                    <div class="form-text">Hanya terlihat oleh Admin pada riwayat verifikasi.</div>
                                </div>
                                <button class="btn btn-primary" type="submit">Simpan keputusan</button>
                            </fieldset>
                        </form>
                    @else
                        <div class="surface-card p-4">
                            <h2 class="h4">Verifikasi selesai</h2>
                            <p class="mb-0">Laporan ini sudah memiliki keputusan Admin. Status lanjutan mengikuti alur penemuan barang.</p>
                        </div>
                    @endif
                </div>
                <div class="col-lg-5">
                    <div class="surface-card p-4 h-100">
                        <h2 class="h4">Riwayat verifikasi</h2>
                        @forelse ($report->statusHistories as $history)
                            <div class="moderation-entry">
                                <div class="d-flex justify-content-between gap-2">
                                    <strong>{{ $actionLabels[$history->action] ?? $history->action }}</strong>
                                    <span class="text-muted small">{{ $history->created_at->translatedFormat('d M Y H:i') }}</span>
                                </div>
                                <p class="mb-1 small">Oleh {{ $history->actor?->name ?? $history->admin?->name ?? 'Sistem' }} · {{ $history->from_status }} → {{ $history->to_status }}</p>
                                @if ($history->reason)
                                    <p class="mb-1 small"><strong>Alasan:</strong> {{ $history->reason }}</p>
                                @endif
                                @if ($history->note)
                                    <p class="mb-0 small"><strong>Catatan:</strong> {{ $history->note }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada keputusan verifikasi untuk laporan ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
