@extends('layouts.app')

@section('title', 'Buat Laporan Kehilangan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <span class="eyebrow">Untuk User</span>
            <h1 class="mt-3">Buat laporan kehilangan</h1>
            <p class="page-intro">Lengkapi informasi agar Admin dapat memeriksa laporanmu sebelum dipublikasikan.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            <form class="form-panel" method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger"><strong>Periksa kembali:</strong><ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul></div>
                @endif
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label" for="item_name">Nama barang *</label>
                        <input class="form-control" id="item_name" name="item_name" value="{{ old('item_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="category">Kategori *</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Deskripsi umum *</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            minlength="10" maxlength="1000" required>{{ old('description') }}</textarea>
                        <div class="form-text">Jangan tulis ciri unik yang sebaiknya digunakan untuk verifikasi.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="lost_at">Tanggal kehilangan *</label>
                        <input class="form-control" type="date" id="lost_at" name="lost_at" value="{{ old('lost_at') }}"
                            max="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="location">Lokasi kehilangan *</label>
                        <input class="form-control" id="location" name="location" value="{{ old('location') }}"
                            maxlength="150" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="photo">Foto barang *</label>
                        <input class="form-control" type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
                        <div class="form-text">JPG, JPEG, atau PNG; maksimal 5 MB.</div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" value="1"
                                @checked(old('terms_accepted')) required>
                            <label class="form-check-label" for="terms_accepted">
                                Saya menyetujui ketentuan pelaporan dan memastikan informasi ini benar.
                            </label>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary btn-lg" type="submit">Kirim untuk ditinjau</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
