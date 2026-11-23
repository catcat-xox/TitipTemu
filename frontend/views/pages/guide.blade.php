@extends('layouts.public')

@section('title', 'Panduan — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Cara menggunakan TitipTemu</h1>
            <p class="page-intro">Cari barang di dua daftar publik tanpa akun, atau masuk untuk melaporkan barang hilang maupun barang temuan.</p>
        </div>
    </section>
    <section class="section pt-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4"><div class="surface-card"><span class="step-number">1</span><h2>Kirim laporan</h2><p>Pemilik atau penemu memakai menu <strong>Buat Laporan</strong>, lalu mengisi no. HP, data barang, dan foto. Laporan menunggu pemeriksaan tim kami sebelum tayang.</p></div></div>
                <div class="col-md-4"><div class="surface-card"><span class="step-number">2</span><h2>Cari dan hubungi</h2><p>Pengunjung mencari di <strong>Barang Hilang</strong> atau <strong>Barang Ditemukan</strong>. Setelah laporan disetujui, tombol WhatsApp menghubungkan Anda dengan pemilik atau penemu tanpa menampilkan nomor.</p></div></div>
                <div class="col-md-4"><div class="surface-card"><span class="step-number">3</span><h2>Tandai sudah ketemu</h2><p>Pemilik menandai barang hilangnya langsung. Untuk barang temuan, penemu mengirim nama pemilik dan foto bersama yang diperiksa tim kami sebelum masuk ke <strong>Barang Sudah Kembali</strong>.</p></div></div>
            </div>
        </div>
    </section>
@endsection
