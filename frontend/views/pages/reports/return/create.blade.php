@extends('layouts.app')

@section('title', 'Konfirmasi Pengembalian — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <a class="back-link" href="{{ route('reports.mine') }}">← Kembali ke laporan saya</a>
            <h1 class="mt-3">Konfirmasi barang telah kembali</h1>
            <p class="page-intro">Kirim bukti pengembalian untuk laporan <strong>{{ $report->item_name }}</strong>. Bukti hanya dapat dilihat oleh Anda dan Admin.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            <div class="form-panel">
                @if ($latestRejection)
                    <div class="alert alert-warning" role="alert">
                        <strong>Konfirmasi sebelumnya ditolak.</strong> Alasan Admin: {{ $latestRejection->rejection_reason }}
                    </div>
                @endif

                @if ($attempt > 3)
                    <div class="privacy-note" role="status">
                        <strong>Batas pengiriman tercapai</strong>
                        <p class="mb-0">Konfirmasi pengembalian sudah dikirim tiga kali. Penyelesaian selanjutnya dilakukan melalui Admin.</p>
                    </div>
                @else
                    <p class="text-muted small">Pengiriman ke-{{ $attempt }} dari maksimal 3 kali.</p>
                    <form method="POST" action="{{ route('reports.return.store', $report) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="proof">Foto bukti pengembalian</label>
                            <input class="form-control" type="file" id="proof" name="proof[]" accept="image/jpeg,image/png,image/webp" multiple required>
                            <div class="form-text">Wajib 1–3 foto JPG/JPEG/PNG/WebP, maksimal 2 MB per foto.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="note">Catatan pengembalian</label>
                            <textarea class="form-control" id="note" name="note" rows="3" maxlength="500">{{ old('note') }}</textarea>
                            <div class="form-text">Opsional. Jangan cantumkan kontak pribadi.</div>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="return_statement" name="return_statement" value="1" @checked(old('return_statement') === '1')>
                            <label class="form-check-label" for="return_statement">Saya menyatakan barang ini telah diterima kembali oleh pemiliknya.</label>
                        </div>
                        <button class="btn btn-primary" type="submit">Kirim konfirmasi</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
