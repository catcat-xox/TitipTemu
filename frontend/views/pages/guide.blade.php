@extends('layouts.public')

@section('title', 'Panduan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Cara menggunakan TitipTemu</h1>
            <p class="page-intro">Cari barang secara publik tanpa akun, atau masuk sebagai User untuk melaporkan barang yang Anda temukan.</p>
        </div>
    </section>
    <section class="section pt-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4"><div class="surface-card"><span class="step-number">1</span><h2>Laporkan temuan</h2><p>User masuk lewat menu Akun, lalu mengisi no. HP penemu, data barang, dan foto. Laporan berstatus Pending hingga diperiksa Admin.</p></div></div>
                <div class="col-md-4"><div class="surface-card"><span class="step-number">2</span><h2>Cari dan hubungi</h2><p>Guest mencari di Barang Hilang. Setelah disetujui, tombol WhatsApp menghubungkan Guest dengan penemu tanpa menampilkan nomor.</p></div></div>
                <div class="col-md-4"><div class="surface-card"><span class="step-number">3</span><h2>Konfirmasi kembali</h2><p>User mengirim nama pemilik dan foto bersama. Setelah Admin menyetujui, laporan pindah ke Barang Ditemukan selama 30 hari.</p></div></div>
            </div>
        </div>
    </section>
@endsection
