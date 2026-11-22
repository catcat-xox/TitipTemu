@extends('layouts.public')

@section('title', 'Beranda — TitipTemu')
@section('description', 'TitipTemu memudahkan pelaporan barang temuan kampus dan pencarian barang oleh pemiliknya.')

@section('content')
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="eyebrow">Portal barang temuan kampus</span>
                    <h1 class="mt-3">
                        Barang yang ditemukan, <span class="accent">lebih mudah kembali.</span>
                    </h1>
                    <p class="lead">
                        TitipTemu memusatkan laporan barang temuan dalam satu tempat.
                        Setiap laporan kami periksa dulu sebelum ditampilkan, agar informasi
                        yang Anda lihat tetap akurat dan aman.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <a href="#cara-kerja" class="btn btn-primary btn-lg">Lihat cara kerja</a>
                        <a href="{{ route('reports.index', 'lost') }}" class="btn btn-outline-secondary btn-lg">Lihat barang hilang</a>
                    </div>

                    <ul class="hero-points">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m5 12 4 4L19 6" />
                            </svg>
                            Informasi publik dapat dilihat tanpa akun.
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m5 12 4 4L19 6" />
                            </svg>
                            Laporan kami periksa dulu sebelum ditampilkan.
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m5 12 4 4L19 6" />
                            </svg>
                            Foto bukti pengembalian tidak kami tampilkan di halaman publik.
                        </li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <div class="hero-card" aria-label="Alur pengelolaan laporan">
                        <div class="hero-card-head">Alur laporan</div>
                        <ol class="flow-list">
                            <li>
                                <span class="flow-index" aria-hidden="true">01</span>
                                <div>
                                    <strong>Laporkan temuan</strong>
                                    <p>Penemu barang mengisi data penemu dan barang, mengunggah foto, lalu mengirim laporan.</p>
                                </div>
                            </li>
                            <li>
                                <span class="flow-index" aria-hidden="true">02</span>
                                <div>
                                    <strong>Diperiksa tim kami</strong>
                                    <p>Laporan kami periksa dulu. Setelah lolos pemeriksaan, barang muncul di daftar Barang Hilang.</p>
                                </div>
                            </li>
                            <li>
                                <span class="flow-index" aria-hidden="true">03</span>
                                <div>
                                    <strong>Konfirmasi pengembalian</strong>
                                    <p>Pemilik barang mengirim nama dan foto bersama. Setelah kami periksa, barang muncul di daftar Barang Ditemukan.</p>
                                </div>
                            </li>
                        </ol>
                        <div class="hero-card-note">
                            Barang yang sudah kembali tampil sekitar 30 hari, setelah itu tidak
                            ditampilkan lagi. Datanya tetap kami simpan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-0" id="laporan-terbaru" aria-labelledby="judul-laporan-terbaru">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
                <div>
                    <span class="eyebrow">Laporan terbaru</span>
                    <h2 id="judul-laporan-terbaru" class="mt-2 mb-1">Barang hilang yang baru dilaporkan</h2>
                    <p class="text-muted mb-0">Beberapa barang temuan yang sudah diverifikasi dan masih menunggu pemiliknya.</p>
                </div>
                <a class="btn btn-outline-primary" href="{{ route('reports.index', 'lost') }}">
                    Lihat selengkapnya <span aria-hidden="true">→</span>
                </a>
            </div>

            @if ($latestReports->isEmpty())
                <div class="empty-state">
                    <span class="icon-box" aria-hidden="true">⌕</span>
                    <h2>Belum ada laporan barang hilang</h2>
                    <p>Laporan yang sudah diverifikasi akan muncul di sini.</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($latestReports as $report)
                        <div class="col-md-6 col-lg-3">
                            <article class="report-card home-report-card">
                                <div class="report-image">
                                    <img src="{{ route('reports.photo', $report) }}"
                                        alt="Foto {{ $report->item_name }}" loading="lazy">
                                </div>
                                <div class="report-card-body">
                                    <div class="d-flex justify-content-between gap-2 align-items-start">
                                        <span class="status-badge status-{{ $report->status }}">Masih dicari</span>
                                        <span class="report-date">{{ $report->lost_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <h2>{{ $report->item_name }}</h2>
                                    <p class="report-meta">{{ $report->category }} · {{ $report->location }}</p>
                                    <div class="mt-auto pt-2">
                                        <a class="btn btn-sm btn-outline-primary"
                                            href="{{ route('reports.show', ['lost', $report]) }}">
                                            Lihat detail <span aria-hidden="true">→</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="section pt-0" id="cara-kerja" aria-labelledby="judul-cara-kerja">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">Cara kerja</span>
                <h2 id="judul-cara-kerja">Dari laporan hingga kembali ke pemilik</h2>
                <p>
                    Setiap perubahan penting kami periksa dan catat. Laporan tidak langsung
                    muncul di daftar publik.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="step-number">1</span>
                        <h3>Laporkan dengan lengkap</h3>
                        <p>
                            Penemu barang mengisi nomor HP, nama barang, kategori, deskripsi,
                            tanggal dan lokasi ditemukan, foto, serta menyetujui ketentuan.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="step-number">2</span>
                        <h3>Tunggu pemeriksaan</h3>
                        <p>
                            Laporan masih menunggu pemeriksaan dan belum ditampilkan. Setelah
                            isinya kami periksa, laporan kami setujui atau kami kembalikan untuk diperbaiki.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="step-number">3</span>
                        <h3>Konfirmasi pengembalian</h3>
                        <p>
                            Pemilik mengirim bukti bahwa barang sudah diterima. Setelah kami
                            periksa, barang berpindah ke daftar Barang Ditemukan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-0" aria-labelledby="judul-privasi">
        <div class="container">
            <div class="privacy-band">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <span class="eyebrow text-white">Privasi</span>
                        <h2 id="judul-privasi" class="mt-3">Data pribadi dan bukti tetap terlindungi</h2>
                        <p>
                            Informasi publik dibatasi agar membantu pencarian, bukan
                            mempermudah klaim palsu.
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <ul class="privacy-list">
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m5 12 4 4L19 6" />
                                </svg>
                                Nomor identitas dan email tidak ditampilkan. Nomor HP penemu hanya dipakai untuk tombol WhatsApp.
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m5 12 4 4L19 6" />
                                </svg>
                                Nama pemilik dan foto bersama hanya diakses untuk verifikasi.
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m5 12 4 4L19 6" />
                                </svg>
                                Laporan hanya dapat dikelola oleh penemu barang dan tim kami.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
