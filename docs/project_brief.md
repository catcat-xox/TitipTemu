# PROJECT BRIEF
# TITIPTEMU
### Portal Barang Temuan Kampus: Pelaporan oleh User, Pencarian oleh Guest, Verifikasi oleh Admin

**Platform:** Website  
**Teknologi:** Laravel dan MySQL  
**Lisensi:** MIT License  
**Status:** Dokumen perencanaan; alur kerja diselaraskan dengan keputusan pemilik 2026-11-22

> **Catatan penyelarasan 2026-11-22:** alur yang berlaku adalah User melaporkan **barang yang ditemukan**, laporan yang disetujui Admin tampil di **Barang Hilang**, dan laporan yang pengembaliannya disetujui Admin tampil di **Barang Ditemukan**. Barang Ditemukan tetap merupakan riwayat pengembalian, bukan daftar pelaporan barang temuan terpisah. Akun Admin terpisah dari akun User dan masuk melalui jalur tersembunyi.

---

## 1. Latar Belakang

Kehilangan barang di lingkungan kampus merupakan kejadian yang dapat dialami oleh mahasiswa maupun civitas akademika. Informasi kehilangan barang sering disampaikan melalui grup WhatsApp, media sosial, atau pengumuman secara langsung. Cara tersebut terkadang membuat informasi sulit ditemukan kembali karena bercampur dengan pesan lain dan tidak tersimpan dalam satu tempat yang terstruktur.

Berdasarkan permasalahan tersebut, kelompok kami mengusulkan pengembangan **TitipTemu**, yaitu website yang memusatkan **laporan barang temuan** di lingkungan institusi pendidikan. User yang menemukan barang membuat laporan berisi data penemu, informasi barang, dan foto. Laporan diperiksa oleh Admin sebelum ditampilkan kepada publik.

Guest yang kehilangan barang dapat mencari laporan tersebut tanpa akun. Setiap detail publik menyediakan tombol WhatsApp untuk menghubungi penemu; nomor HP penemu tetap disembunyikan dan tidak ditampilkan sebagai teks.

TitipTemu juga menyediakan mekanisme konfirmasi ketika barang telah kembali kepada pemiliknya. User mengirimkan **nama pemilik** dan **foto bersama penemu–pemilik** sebagai bukti. Admin memeriksa bukti tersebut. Apabila disetujui, laporan berpindah dari halaman Barang Hilang ke halaman Barang Ditemukan sebagai riwayat barang yang telah kembali.

Dengan adanya TitipTemu, informasi barang temuan dan pengembaliannya diharapkan dapat dikelola secara lebih terpusat, terstruktur, dan mudah diakses.

## 2. Tujuan Proyek

Tujuan pengembangan TitipTemu adalah:

1. Menyediakan website sebagai pusat laporan barang temuan di lingkungan institusi.
2. Mempermudah User melaporkan barang temuan dengan data penemu, informasi barang, dan foto.
3. Memungkinkan Guest melihat dan mencari informasi barang tanpa login.
4. Menyediakan proses pemeriksaan laporan oleh Admin sebelum dipublikasikan.
5. Menghubungkan Guest dengan penemu melalui WhatsApp tanpa menampilkan nomor HP secara publik.
6. Menyediakan fitur konfirmasi pengembalian dengan nama pemilik dan foto bersama yang privat.
7. Menyediakan riwayat status laporan dan pengelolaan publikasi barang oleh Admin.
8. Menerapkan pengembangan perangkat lunak menggunakan teknologi open source.

## 3. Ruang Lingkup Proyek

Pengembangan TitipTemu ditujukan untuk seluruh lingkungan kampus, bukan hanya satu fakultas atau program studi.

### A. Fitur utama

1. **Halaman Beranda:** menampilkan informasi umum dan navigasi utama website.
2. **Barang Hilang:** menampilkan laporan barang temuan yang telah disetujui Admin dan masih menunggu diambil pemiliknya.
3. **Barang Ditemukan:** menampilkan riwayat laporan yang pengembaliannya disetujui Admin. Bukan daftar pelaporan barang temuan terpisah.
4. **Pencarian Barang:** membantu Guest mencari informasi berdasarkan kata kunci.
5. **Kontak WhatsApp:** tombol pada detail publik yang membuka chat dengan penemu tanpa menampilkan nomor HP.
6. **Registrasi dan Login User:** menyediakan akun untuk melaporkan barang temuan dan mengirim konfirmasi pengembalian.
7. **Login Admin terpisah:** jalur masuk tersembunyi, tidak ditautkan pada navigasi publik.
8. **Laporkan Barang Temuan:** memungkinkan User mengisi data penemu, data barang, dan foto.
9. **Akun:** menampilkan aktivitas dan laporan milik User beserta statusnya.
10. **Konfirmasi Barang Telah Kembali:** memungkinkan User mengirim nama pemilik dan foto bersama penemu–pemilik yang bersifat privat.
11. **Dashboard Admin:** memeriksa laporan dan konfirmasi, menyetujui/menolak, mengedit laporan `Pending`, serta menghapus permanen laporan yang telah selesai.
12. **Pengarsipan dan retensi:** Barang Ditemukan tidak ditampilkan setelah 30 hari dan diarsipkan; data tetap disimpan kecuali Admin melakukan penghapusan permanen yang disetujui.

### B. Hak akses pengguna

| Pengguna | Hak akses |
|---|---|
| Guest | Melihat beranda, daftar barang, detail publik, melakukan pencarian, dan menghubungi penemu via WhatsApp tanpa login |
| User (mahasiswa, dosen, atau staf aktif) | Melaporkan barang temuan, mengunggah foto, melihat laporan miliknya di menu **Akun**, menghubungi penemu, dan mengirim konfirmasi pengembalian |
| Admin | Memeriksa, menyetujui, menolak, mengedit laporan `Pending`, memeriksa bukti privat, mengelola publikasi, dan menghapus permanen laporan `returned`/`archived` |

Guest merupakan pengunjung tanpa login, bukan jenis akun terdaftar. Sistem memiliki dua jenis akun yang terpisah: User dan Admin. Admin masuk melalui jalur tersembunyi dan tidak memiliki tautan pada navigasi publik. Beberapa akun Admin dapat berdiri sendiri.

### C. Batasan proyek

Pada tahap MVP, proyek berfokus pada website. Pengembangan aplikasi mobile native, integrasi WhatsApp API, notifikasi SMS, pencocokan barang menggunakan AI, dan integrasi SSO akademik belum termasuk ruang lingkup utama.

## 4. Alur Kerja Sistem

### A. Proses pelaporan barang temuan

1. User melakukan login.
2. User membuka formulir **Laporkan Barang Temuan**.
3. User mengisi data penemu (nama tercatat pada akun dan **no. HP penemu**), informasi barang seperti nama, kategori, deskripsi, tanggal dan lokasi **ditemukan**.
4. User mengunggah foto barang dan menyetujui ketentuan laporan.
5. Sistem memvalidasi data dan menyimpan laporan dengan status `Pending`.
6. Admin memeriksa laporan; Admin juga dapat mengedit langsung laporan `Pending` dan mencatat perubahannya.
7. Admin menyetujui atau menolak laporan dengan alasan wajib saat menolak; User dapat memperbaiki dan mengirim ulang.
8. Jika disetujui, laporan ditampilkan pada halaman **Barang Hilang**.
9. Guest membuka detail dan memakai tombol WhatsApp. Sistem mengarahkan ke `wa.me` tanpa menampilkan nomor penemu di halaman publik.

### B. Proses konfirmasi barang telah kembali

1. User membuka laporan miliknya yang masih aktif lewat menu **Akun**.
2. User memilih fitur konfirmasi barang telah kembali.
3. User mengisi **nama pemilik**, mengunggah **foto bersama penemu–pemilik**, dan menyetujui pernyataan pengembalian. Catatan pengembalian opsional.
4. Sistem menyimpan konfirmasi dengan status `Return Pending`; nama pemilik dan foto bersama tidak tampil di publik.
5. Admin memeriksa konfirmasi dan bukti foto melalui halaman privat.
6. Admin menyetujui atau menolak konfirmasi dengan alasan wajib saat menolak.
7. Jika disetujui, status laporan berubah menjadi `Returned` dan laporan berpindah ke halaman **Barang Ditemukan**. Jika ditolak, laporan tetap di Barang Hilang dan User dapat memperbaiki serta mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
8. Setelah masa tampil 30 hari sejak persetujuan konfirmasi pengembalian dan publikasi Admin, sistem mengarsipkan laporan sehingga tidak lagi tampil kepada publik; data tetap disimpan.
9. Pada status `returned` atau `archived`, Admin dapat menghapus laporan secara permanen setelah konfirmasi; penghapusan juga membersihkan foto dan bukti privat terkait.

## 5. Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| Laravel | Framework backend untuk pengembangan website |
| PHP | Bahasa pemrograman sisi server |
| MySQL | Pengelolaan dan penyimpanan basis data |
| HTML | Membentuk struktur halaman website |
| CSS | Mengatur tampilan dan tata letak |
| JavaScript | Mendukung interaksi pada halaman website |
| Bootstrap | Membantu pembuatan antarmuka responsif |
| Git | Mengelola versi dan riwayat perubahan kode |
| GitHub | Menyimpan repository dan mendukung kolaborasi tim |

Seluruh teknologi dipilih dengan mempertimbangkan ketersediaan, dokumentasi, dan dukungannya terhadap pengembangan perangkat lunak berbasis open source.

## 6. Struktur Repository

Repository proyek menggunakan dua folder utama: aplikasi Laravel di `backend/` serta tampilan dan aset sumber di `frontend/`. Folder `docs/` menyimpan dokumentasi proyek.

```text
titipemu/
├── backend/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── routes/
│   ├── storage/
│   ├── tests/
│   ├── artisan
│   ├── composer.json
│   ├── composer.lock
│   └── phpunit.xml
├── docs/
│   ├── project_brief.md
│   ├── prd.md
│   ├── requirement.md
│   ├── ARCHITECTURE.md
│   ├── RULES.md
│   ├── TASKS.md
│   ├── diagrams/
│   └── wireframes/
├── frontend/
│   ├── css/
│   ├── images/
│   ├── js/
│   ├── views/
│   ├── package.json
│   ├── package-lock.json
│   └── vite.config.js
├── .gitignore
├── LICENSE
└── README.md
```

Folder `docs` digunakan untuk menyimpan dokumen perencanaan, kebutuhan sistem, diagram, dan rancangan antarmuka. Aset frontend dibangun dengan Vite ke `backend/public/build`. File konfigurasi rahasia `.env` tidak akan dimasukkan ke repository publik.

## 7. Lisensi Proyek

Proyek TitipTemu direncanakan menggunakan **MIT License**.

MIT License dipilih karena memberikan kebebasan kepada pihak lain untuk menggunakan, mempelajari, memodifikasi, dan mendistribusikan kode sumber, termasuk untuk kebutuhan komersial, dengan tetap mempertahankan pemberitahuan hak cipta dan lisensi.

Lisensi ini sesuai untuk proyek kelompok yang bertujuan menerapkan prinsip keterbukaan perangkat lunak dan memungkinkan pengembangan lebih lanjut oleh komunitas.

Lisensi hanya diterapkan pada kode dan materi yang memang dimiliki atau berhak dilisensikan oleh kelompok. Foto, logo, library, dan aset pihak ketiga tetap mengikuti lisensi atau ketentuan pemilik masing-masing.

## 8. Pembagian Tugas Anggota Kelompok

| No. | Anggota | Tanggung Jawab |
|---:|---|---|
| 1 | Anggota 1 — [Nama] | Koordinator proyek, backend Laravel, perancangan database MySQL, integrasi sistem, dan pengelolaan repository |
| 2 | Anggota 2 — [Nama] | Perancangan UI/UX, halaman beranda, navigasi publik, dan tampilan formulir laporan |
| 3 | Anggota 3 — [Nama] | Fitur pelaporan barang temuan, pencarian, detail barang, dan pengelolaan status laporan |
| 4 | Anggota 4 — [Nama] | Dashboard Admin, verifikasi laporan dan konfirmasi pengembalian, dokumentasi, serta pengujian |

Pembagian tugas dapat disesuaikan melalui GitHub Issues. Setiap anggota diharapkan berkontribusi melalui branch masing-masing dan mengajukan perubahan menggunakan Pull Request.

## 9. Kolaborasi dan Pengelolaan Repository

Pengembangan TitipTemu menggunakan Git dan GitHub untuk mendukung kerja sama antaranggota.

Rencana kolaborasi meliputi:

1. Membuat satu repository GitHub publik dengan nama `titiptemu`.
2. Menambahkan seluruh anggota kelompok sebagai collaborator.
3. Menyimpan kode sumber dan dokumen proyek dalam repository.
4. Membuat GitHub Issues untuk membagi pekerjaan.
5. Menggunakan branch terpisah untuk pengembangan setiap fitur.
6. Mengajukan Pull Request sebelum perubahan digabungkan ke branch `main`.
7. Melakukan pemeriksaan perubahan atau Code Review sebelum merge.
8. Melindungi branch `main` melalui pengaturan branch protection.

## 10. Rencana Pengembangan

| Tahap | Kegiatan | Target hasil |
|---|---|---|
| P14 — Perencanaan | Menyusun Project Brief, PRD, Requirement, repository, lisensi, README, Issues, dan branch protection | Dokumen perencanaan dan repository awal |
| P15 — Implementasi | Mengembangkan autentikasi User/Admin terpisah, halaman publik, pelaporan barang temuan, dashboard Admin, konfirmasi pengembalian privat, dan pengarsipan | Website MVP |
| P16 — Pengujian dan penyelesaian | Melakukan pengujian, memperbaiki kesalahan, melengkapi dokumentasi, demo, dan presentasi | Website yang telah diuji dan siap didemonstrasikan |

## 11. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Laporan palsu atau tidak sesuai | Menerapkan login dan pemeriksaan Admin |
| Foto tidak sesuai atau tidak pantas | Melakukan validasi file dan verifikasi laporan |
| Konfirmasi pengembalian tidak benar | Meminta foto bukti dan pemeriksaan Admin |
| Data pribadi pengguna tersebar | Membatasi informasi publik dan hak akses data |
| Perubahan kode menimbulkan konflik | Menggunakan branch dan Pull Request |
| Data proyek hilang | Melakukan backup basis data dan file penting |
| Pengerjaan terlambat | Membagi pekerjaan melalui Issues dan menetapkan target setiap anggota |

## 12. Penutup

TitipTemu dirancang sebagai solusi berbasis website untuk membantu pengelolaan laporan barang temuan dan dokumentasi pengembalian barang di lingkungan institusi pendidikan. Dengan fitur pelaporan oleh User, pencarian oleh Guest, kontak WhatsApp tanpa eksposur nomor, verifikasi Admin terpisah, konfirmasi pengembalian yang privat, dan pencatatan status, sistem ini diharapkan dapat membantu pengguna memperoleh informasi secara lebih terstruktur.

Penggunaan Laravel, MySQL, Git, GitHub, dan MIT License mendukung penerapan teknologi open source dalam proses pengembangan proyek. Melalui kolaborasi empat anggota kelompok, proyek akan dikembangkan secara bertahap mulai dari perencanaan, implementasi, hingga pengujian dan demonstrasi.

---
