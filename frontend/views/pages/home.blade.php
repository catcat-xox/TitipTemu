@extends('layouts.app')

@section('title', 'Beranda — TitipTemu')
@section('description', 'TitipTemu memusatkan informasi barang hilang kampus dan mendokumentasikan barang yang telah kembali kepada pemiliknya.')

@section('content')
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="eyebrow">Portal barang hilang kampus</span>
                    <h1 class="mt-3">
                        Barang yang hilang, <span class="accent">lebih mudah kembali.</span>
                    </h1>
                    <p class="lead">
                        TitipTemu memusatkan laporan kehilangan dalam satu tempat.
                        Setiap laporan diperiksa Admin sebelum dipublikasikan, dan
                        pengembalian barang didokumentasikan dengan bukti yang privat.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <a href="#cara-kerja" class="btn btn-primary btn-lg">Lihat cara kerja</a>
                        <a href="#daftar-publik" class="btn btn-outline-secondary btn-lg">Tentang daftar publik</a>
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
                            Laporan ditinjau Admin sebelum tampil.
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m5 12 4 4L19 6" />
                            </svg>
                            Foto bukti pengembalian tetap privat.
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
                                    <strong>Lapor kehilangan</strong>
                                    <p>User masuk, mengisi data barang, mengunggah foto, dan menyetujui ketentuan.</p>
                                </div>
                            </li>
                            <li>
                                <span class="flow-index" aria-hidden="true">02</span>
                                <div>
                                    <strong>Ditinjau Admin</strong>
                                    <p>Laporan diperiksa. Setelah disetujui, laporan tampil di daftar Barang Hilang.</p>
                                </div>
                            </li>
                            <li>
                                <span class="flow-index" aria-hidden="true">03</span>
                                <div>
                                    <strong>Konfirmasi kembali</strong>
                                    <p>Pemilik mengirim bukti. Setelah disetujui, laporan masuk Barang Ditemukan.</p>
                                </div>
                            </li>
                        </ol>
                        <div class="hero-card-note">
                            Barang Ditemukan tampil selama 30 hari sejak disetujui Admin,
                            lalu diarsipkan. Data tetap tersimpan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="daftar-publik" aria-labelledby="judul-daftar-publik">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">Daftar publik</span>
                <h2 id="judul-daftar-publik">Dua daftar, satu alur yang jelas</h2>
                <p>
                    Guest dapat melihat informasi yang telah disetujui Admin tanpa
                    membuat akun. Data pribadi dan bukti verifikasi tidak ditampilkan.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="surface-card">
                        <span class="icon-box" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-4-4" />
                            </svg>
                        </span>
                        <span class="card-label">Daftar publik</span>
                        <h3>Barang Hilang</h3>
                        <p>
                            Berisi laporan kehilangan yang telah disetujui Admin dan masih
                            aktif. Informasi ditampilkan secukupnya untuk membantu pencarian.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="surface-card">
                        <span class="icon-box accent" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7 9.5 17.5 4 12" />
                                <path d="M20 13v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h9" />
                            </svg>
                        </span>
                        <span class="card-label">Riwayat pengembalian</span>
                        <h3>Barang Ditemukan</h3>
                        <p>
                            Berisi laporan yang pengembaliannya telah disetujui Admin.
                            Tampil selama 30 hari, lalu diarsipkan dari daftar publik.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-0" id="cara-kerja" aria-labelledby="judul-cara-kerja">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">Cara kerja</span>
                <h2 id="judul-cara-kerja">Dari laporan hingga kembali ke pemilik</h2>
                <p>
                    Setiap perubahan status penting diperiksa dan dicatat oleh Admin.
                    Laporan tidak langsung tampil ke publik.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="step-number">1</span>
                        <h3>Laporkan dengan lengkap</h3>
                        <p>
                            User mengisi nama barang, kategori, deskripsi, tanggal dan
                            lokasi kehilangan, foto, serta menyetujui ketentuan.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="step-number">2</span>
                        <h3>Tunggu pemeriksaan Admin</h3>
                        <p>
                            Laporan berstatus Pending dan belum tampil publik. Admin
                            menyetujui atau menolak setelah memeriksa isi laporan.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="step-number">3</span>
                        <h3>Konfirmasi pengembalian</h3>
                        <p>
                            Pemilik mengirim bukti bahwa barang telah diterima. Setelah
                            disetujui Admin, laporan berpindah ke Barang Ditemukan.
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
                                Nomor identitas, email, dan kontak pribadi tidak ditampilkan.
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m5 12 4 4L19 6" />
                                </svg>
                                Foto bukti pengembalian hanya diakses untuk verifikasi.
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m5 12 4 4L19 6" />
                                </svg>
                                Laporan User hanya dapat dikelola oleh pemilik dan Admin.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-0" aria-labelledby="judul-peran">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">Peran pengguna</span>
                <h2 id="judul-peran">Akses sesuai kebutuhan</h2>
                <p>Guest bukan akun terdaftar. Sistem memiliki akun User dan Admin.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="card-label">Guest</span>
                        <h3>Melihat informasi</h3>
                        <p>Membuka beranda, daftar barang yang dipublikasikan, detail publik, dan pencarian tanpa login.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="card-label">User</span>
                        <h3>Melapor dan memantau</h3>
                        <p>Membuat laporan kehilangan, melihat status laporan miliknya, dan mengirim konfirmasi pengembalian.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="surface-card">
                        <span class="card-label">Admin</span>
                        <h3>Memeriksa dan memoderasi</h3>
                        <p>Meninjau laporan, memeriksa bukti pengembalian, serta mengelola publikasi dan arsip.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
