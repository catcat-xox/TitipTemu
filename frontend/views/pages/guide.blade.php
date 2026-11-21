@extends('layouts.app')

@section('title', 'Panduan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <span class="eyebrow">Panduan</span>
            <h1 class="mt-3">Cara menggunakan TitipTemu</h1>
            <p class="page-intro">Temukan laporan secara publik atau kirim laporan setelah masuk sebagai User.</p>
        </div>
    </section>
    <section class="section pt-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4"><div class="surface-card"><span class="step-number">1</span><h2>Cari informasi</h2><p>Gunakan daftar Barang Hilang dan pencarian untuk menemukan informasi yang relevan.</p></div></div>
                <div class="col-md-4"><div class="surface-card"><span class="step-number">2</span><h2>Buat laporan</h2><p>User dapat mengirim data lengkap dan foto. Laporan berstatus Pending hingga diperiksa Admin.</p></div></div>
                <div class="col-md-4"><div class="surface-card"><span class="step-number">3</span><h2>Tunggu verifikasi</h2><p>Setelah disetujui, laporan tampil publik. Data pribadi dan bukti privat tetap terlindungi.</p></div></div>
            </div>
        </div>
    </section>
@endsection
