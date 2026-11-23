@extends('layouts.public')

@section('title', 'Beranda — TitipTemu')
@section('description', 'TitipTemu memudahkan pelaporan barang hilang dan barang temuan kampus, serta pencarian keduanya secara publik.')

@section('content')
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="eyebrow">Portal barang hilang dan temuan kampus</span>
                    <h1 class="mt-3">
                        Barang hilang atau ditemukan, <span class="accent">lebih mudah bertemu lagi.</span>
                    </h1>
                    <p class="lead">
                        TitipTemu memuat dua kanal: laporan kehilangan dari pemilik dan
                        laporan temuan dari penemu. Setiap laporan kami periksa dulu sebelum
                        ditampilkan, agar informasi yang Anda lihat tetap akurat dan aman.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <a href="#cara-kerja" class="btn btn-primary btn-lg">Lihat cara kerja</a>
                        <a href="{{ route('reports.index', 'hilang') }}" class="btn btn-outline-secondary btn-lg">Lihat barang hilang</a>
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
                            Foto laporan, nomor HP, dan bukti privat tidak kami tampilkan di halaman publik.
                        </li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <div class="hero-card" role="group" aria-label="Alur pengelolaan laporan">
                        <div class="hero-card-head">Alur Laporan</div>
                        <ol class="flow-list">
                            <li>
                                <span class="flow-index" aria-hidden="true">01</span>
                                <div>
                                    <strong>Kirim laporan barang</strong>
                                    <p>Laporkan barang yang hilang jika kamu pemiliknya, atau laporkan barang temuan jika kamu menemukannya.</p>
                                </div>
                            </li>
                            <li>
                                <span class="flow-index" aria-hidden="true">02</span>
                                <div>
                                    <strong>Proses pemeriksaan</strong>
                                    <p>Laporan yang masuk akan kami periksa terlebih dahulu sebelum tayang di daftar <strong>Barang Belum Ditemukan</strong> atau <strong>Barang Belum Kembali</strong>.</p>
                                </div>
                            </li>
                            <li>
                                <span class="flow-index" aria-hidden="true">03</span>
                                <div>
                                    <strong>Konfirmasi pengembalian</strong>
                                    <p>Pemilik dapat menandai barang hilang miliknya sudah ketemu. Untuk barang temuan, konfirmasi serah terima kami periksa dahulu sebelum masuk ke daftar <strong>Barang Sudah Kembali</strong>.</p>
                                </div>
                            </li>
                        </ol>
                        <div class="hero-card-note">
                            Laporan pada daftar <strong>Sudah</strong> tetap tampil selama 30 hari, lalu disembunyikan. Riwayat datanya tetap tersimpan.
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
                    <h2 id="judul-laporan-terbaru" class="mt-2 mb-1">Cek laporan terkini</h2>
                    <p class="text-muted mb-0">Daftar laporan barang hilang dan temuan terbaru yang telah diperiksa.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-primary" href="{{ route('reports.index', 'hilang') }}">Barang hilang</a>
                    <a class="btn btn-outline-primary" href="{{ route('reports.index', 'ditemukan') }}">Barang ditemukan</a>
                </div>
            </div>

            @if ($latestReports->isEmpty())
                <div class="empty-state">
                    <span class="icon-box" aria-hidden="true">⌕</span>
                    <h2>Belum ada laporan terbaru</h2>
                    <p>Laporan yang sudah diverifikasi akan muncul di sini.</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($latestReports as $report)
                        <div class="col-md-6 col-lg-3">
                            <article class="report-card home-report-card">
                                <div class="report-image">
                                    <img src="{{ $report['photo_url'] }}"
                                        alt="Foto {{ $report['item_name'] }}" loading="lazy">
                                </div>
                                <div class="report-card-body">
                                    <div class="d-flex justify-content-between gap-2 align-items-start">
                                        <span class="status-badge">{{ $report['status_label'] }}</span>
                                        <span class="report-date">{{ $report['lost_at']->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <h2>{{ $report['item_name'] }}</h2>
                                    <p class="report-meta">{{ $report['kind_label'] }} · {{ $report['category'] }} · {{ $report['location'] }}</p>
                                    <div class="mt-auto pt-2">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ $report['show_url'] }}">
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
                <h2 id="judul-cara-kerja">Dari laporan hingga bertemu lagi</h2>
                <p>
                    Tiga langkah sederhana. Laporan baru kami cek dulu sebelum tampil,
                    supaya informasi yang Anda lihat tetap bisa dipercaya.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="step-number">1</span>
                        <h3>Ceritakan barangnya</h3>
                        <p>
                            Pemilik lapor barang yang hilang, penemu lapor barang yang
                            ditemukan. Sebutkan nama dan kategori barang, kapan serta di
                            mana kejadiannya, lalu tambahkan foto dan nomor HP yang bisa
                            dihubungi.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="step-number">2</span>
                        <h3>Kami cek dulu</h3>
                        <p>
                            Laporan belum tampil selagi menunggu pemeriksaan. Setelah isinya
                            kami cek, laporan kami tampilkan atau kami kembalikan untuk
                            diperbaiki.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="step-number">3</span>
                        <h3>Kabari saat sudah ketemu</h3>
                        <p>
                            Untuk barang hilang, pemilik langsung menandai barangnya sudah
                            ditemukan. Untuk barang temuan, konfirmasinya kami cek dulu.
                            Setelahnya laporan pindah ke bagian “Sudah”.
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
                                Nomor identitas dan email tidak ditampilkan. Nomor HP pemilik dan penemu hanya dipakai untuk tombol WhatsApp.
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
                                Laporan kehilangan dan temuan disimpan terpisah serta hanya dikelola oleh pembuatnya dan tim kami.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
