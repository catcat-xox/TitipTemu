@extends('layouts.public')

@section('title', 'Konfirmasi Pengembalian — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <a class="back-link" href="{{ route('reports.mine') }}">← Kembali ke laporan saya</a>
            <h1 class="mt-3">Konfirmasi barang telah kembali</h1>
            <p class="page-intro">Kirim data pemilik dan foto bersama untuk laporan <strong>{{ $report->item_name }}</strong>. Data ini hanya dapat dilihat oleh Anda dan tim kami.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            <div class="form-panel">
                @if ($latestRejection)
                    <div class="alert alert-warning" role="alert">
                        <strong>Konfirmasi sebelumnya ditolak.</strong> Alasan tim kami: {{ $latestRejection->rejection_reason }}
                    </div>
                @endif

                @if ($attempt > 3)
                    <div class="privacy-note" role="status">
                        <strong>Batas pengiriman tercapai</strong>
                        <p class="mb-0">Konfirmasi pengembalian sudah dikirim tiga kali. Penyelesaian selanjutnya dilakukan bersama tim kami.</p>
                    </div>
                @else
                    <p class="text-muted small">Pengiriman ke-{{ $attempt }} dari maksimal 3 kali.</p>
                    <form method="POST" action="{{ route('reports.return.store', $report) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="owner_name">Nama pemilik *</label>
                            <input class="form-control {{ $errors->has('owner_name') ? 'is-invalid' : '' }}" id="owner_name" name="owner_name"
                                value="{{ old('owner_name') }}" maxlength="100"
                                aria-invalid="{{ $errors->has('owner_name') ? 'true' : 'false' }}"
                                aria-describedby="hint-owner_name {{ $errors->has('owner_name') ? 'error-owner_name' : '' }}" required>
                            <div class="form-text" id="hint-owner_name">Nama pemilik tidak ditampilkan di halaman publik.</div>
                            @include('partials.field-error', ['name' => 'owner_name'])
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="proof">Foto bersama penemu–pemilik *</label>
                            <input class="form-control {{ $errors->has('proof') ? 'is-invalid' : '' }}" type="file" id="proof" name="proof[]"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple
                                aria-invalid="{{ $errors->has('proof') ? 'true' : 'false' }}"
                                aria-describedby="hint-proof {{ $errors->has('proof') ? 'error-proof' : '' }}" required>
                            <div class="form-text" id="hint-proof">Wajib 1–3 foto JPG/JPEG/PNG/WebP, maksimal 2 MB per foto. Foto ini menjadi bukti pengembalian dan tidak ditampilkan di halaman publik.</div>
                            @include('partials.field-error', ['name' => 'proof'])
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="note">Catatan pengembalian</label>
                            <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" id="note" name="note" rows="3" maxlength="500"
                                aria-invalid="{{ $errors->has('note') ? 'true' : 'false' }}"
                                aria-describedby="hint-note {{ $errors->has('note') ? 'error-note' : '' }}">{{ old('note') }}</textarea>
                            <div class="form-text" id="hint-note">Opsional. Jangan cantumkan kontak pribadi.</div>
                            @include('partials.field-error', ['name' => 'note'])
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input {{ $errors->has('return_statement') ? 'is-invalid' : '' }}" type="checkbox" id="return_statement" name="return_statement" value="1"
                                @checked(old('return_statement') === '1')
                                aria-invalid="{{ $errors->has('return_statement') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('return_statement') ? 'error-return_statement' : '' }}" required>
                            <label class="form-check-label" for="return_statement">Saya menyatakan barang ini telah diterima kembali oleh pemiliknya.</label>
                            @include('partials.field-error', ['name' => 'return_statement'])
                        </div>
                        <button class="btn btn-primary" type="submit">Kirim konfirmasi</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
