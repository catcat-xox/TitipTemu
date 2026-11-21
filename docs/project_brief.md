# PROJECT BRIEF
# TITIPTEMU
### Website Informasi Barang Hilang dan Konfirmasi Pengembalian Barang

**Platform:** Website  
**Teknologi:** Laravel dan MySQL  
**Lisensi:** MIT License  
**Status:** Proposal tahap perencanaan

---

## 1. Latar Belakang

Kehilangan barang di lingkungan kampus merupakan kejadian yang dapat dialami oleh mahasiswa maupun civitas akademika. Informasi kehilangan barang sering disampaikan melalui grup WhatsApp, media sosial, atau pengumuman secara langsung. Cara tersebut terkadang membuat informasi sulit ditemukan kembali karena bercampur dengan pesan lain dan tidak tersimpan dalam satu tempat yang terstruktur.

Berdasarkan permasalahan tersebut, kelompok kami mengusulkan pengembangan **TitipTemu**, yaitu website yang berfungsi sebagai pusat informasi laporan barang hilang di lingkungan institusi pendidikan. Website ini memungkinkan pengguna membuat laporan kehilangan dengan menyertakan foto dan informasi barang. Laporan yang masuk akan diperiksa oleh Admin sebelum ditampilkan kepada publik.

TitipTemu juga menyediakan mekanisme konfirmasi ketika barang telah kembali kepada pemiliknya. Pemilik laporan dapat mengirimkan konfirmasi beserta foto bukti. Admin kemudian memeriksa konfirmasi tersebut. Apabila disetujui, laporan berpindah dari halaman Barang Hilang ke halaman Barang Ditemukan sebagai riwayat barang yang telah kembali.

Dengan adanya TitipTemu, informasi kehilangan dan pengembalian barang diharapkan dapat dikelola secara lebih terpusat, terstruktur, dan mudah diakses.

## 2. Tujuan Proyek

Tujuan pengembangan TitipTemu adalah:

1. Menyediakan website sebagai pusat informasi barang hilang di lingkungan institusi.
2. Mempermudah pengguna membuat laporan kehilangan dengan informasi dan foto barang.
3. Memungkinkan pengunjung melihat informasi barang tanpa harus login.
4. Menyediakan proses pemeriksaan laporan oleh Admin sebelum dipublikasikan.
5. Menyediakan fitur konfirmasi barang telah kembali oleh pemilik laporan dengan foto bukti.
6. Menyediakan riwayat status laporan dan pengelolaan publikasi barang.
7. Menerapkan pengembangan perangkat lunak menggunakan teknologi open source.

## 3. Ruang Lingkup Proyek

Pengembangan TitipTemu ditujukan untuk seluruh lingkungan kampus, bukan hanya satu fakultas atau program studi.

### A. Fitur utama

1. **Halaman Beranda:** menampilkan informasi umum dan navigasi utama website.
2. **Barang Hilang:** menampilkan laporan kehilangan yang telah disetujui Admin dan masih aktif.
3. **Barang Ditemukan:** menampilkan riwayat laporan kehilangan yang berhasil dikembalikan kepada pemilik setelah konfirmasi pengembalian disetujui Admin. Laporan barang temuan baru tidak termasuk.
4. **Pencarian Barang:** membantu pengunjung mencari informasi berdasarkan kata kunci.
5. **Registrasi dan Login:** menyediakan akses akun User dan Admin.
6. **Laporan Kehilangan:** memungkinkan User membuat laporan dengan data dan foto barang.
7. **Laporan Saya:** menampilkan laporan yang dibuat oleh User beserta statusnya.
8. **Konfirmasi Barang Telah Kembali:** memungkinkan pemilik laporan mengirimkan konfirmasi dan foto bukti.
9. **Dashboard Admin:** digunakan untuk memeriksa laporan dan konfirmasi pengembalian.
10. **Pengarsipan:** barang pada halaman Barang Ditemukan tidak lagi ditampilkan kepada publik setelah masa 30 hari dan diarsipkan.

### B. Hak akses pengguna

| Pengguna | Hak akses |
|---|---|
| Guest | Melihat beranda, daftar barang, detail publik, dan melakukan pencarian tanpa login |
| User (mahasiswa, dosen, atau staf aktif) | Membuat laporan kehilangan, mengunggah foto, melihat laporan miliknya, dan mengirim konfirmasi barang telah kembali |
| Admin | Memeriksa, menyetujui, menolak, mengelola laporan, memeriksa konfirmasi pengembalian, dan mengelola publikasi |

Guest merupakan pengunjung tanpa login, bukan jenis akun terdaftar. Sistem memiliki dua jenis akun, yaitu User dan Admin.

### C. Batasan proyek

Pada tahap MVP, proyek berfokus pada website. Pengembangan aplikasi mobile native, integrasi WhatsApp API, notifikasi SMS, pencocokan barang menggunakan AI, dan integrasi SSO akademik belum termasuk ruang lingkup utama.

## 4. Alur Kerja Sistem

### A. Proses laporan kehilangan

1. User melakukan login.
2. User membuka formulir laporan barang hilang.
3. User mengisi informasi barang seperti nama, kategori, deskripsi, tanggal, dan lokasi kehilangan.
4. User mengunggah foto barang dan menyetujui ketentuan laporan.
5. Sistem memvalidasi data dan menyimpan laporan dengan status `Pending`.
6. Admin memeriksa laporan.
7. Admin menyetujui atau menolak laporan dengan alasan wajib saat menolak; User dapat memperbaiki dan mengirim ulang.
8. Jika disetujui, laporan ditampilkan pada halaman Barang Hilang.

### B. Proses konfirmasi barang telah kembali

1. User membuka laporan miliknya yang masih aktif.
2. User memilih fitur konfirmasi barang telah kembali.
3. User mengisi informasi konfirmasi, mengunggah foto bukti, dan menyetujui pernyataan pengembalian.
4. Sistem menyimpan konfirmasi dengan status `Return Pending`.
5. Admin memeriksa konfirmasi dan bukti foto.
6. Admin menyetujui atau menolak konfirmasi dengan alasan wajib saat menolak.
7. Jika disetujui, status laporan berubah menjadi `Returned` dan laporan berpindah ke halaman Barang Ditemukan. Jika ditolak, laporan tetap di Barang Hilang dan User dapat memperbaiki serta mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
8. Setelah masa tampil 30 hari sejak persetujuan konfirmasi pengembalian dan publikasi Admin, sistem mengarsipkan laporan sehingga tidak lagi tampil kepada publik; data tetap disimpan.

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
| 3 | Anggota 3 — [Nama] | Fitur laporan kehilangan, pencarian, detail barang, dan pengelolaan status laporan |
| 4 | Anggota 4 — [Nama] | Dashboard Admin, moderasi laporan dan konfirmasi pengembalian, dokumentasi, serta pengujian |

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
| P15 — Implementasi | Mengembangkan autentikasi, halaman publik, laporan kehilangan, dashboard Admin, konfirmasi pengembalian, dan pengarsipan | Website MVP |
| P16 — Pengujian dan penyelesaian | Melakukan pengujian, memperbaiki kesalahan, melengkapi dokumentasi, demo, dan presentasi | Website yang telah diuji dan siap didemonstrasikan |

## 11. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Laporan palsu atau tidak sesuai | Menerapkan login dan pemeriksaan Admin |
| Foto tidak sesuai atau tidak pantas | Melakukan validasi file dan moderasi laporan |
| Konfirmasi pengembalian tidak benar | Meminta foto bukti dan pemeriksaan Admin |
| Data pribadi pengguna tersebar | Membatasi informasi publik dan hak akses data |
| Perubahan kode menimbulkan konflik | Menggunakan branch dan Pull Request |
| Data proyek hilang | Melakukan backup basis data dan file penting |
| Pengerjaan terlambat | Membagi pekerjaan melalui Issues dan menetapkan target setiap anggota |

## 12. Penutup

TitipTemu dirancang sebagai solusi berbasis website untuk membantu pengelolaan informasi barang hilang dan dokumentasi pengembalian barang di lingkungan institusi pendidikan. Dengan fitur laporan kehilangan, moderasi Admin, konfirmasi barang telah kembali, dan pencatatan status, sistem ini diharapkan dapat membantu pengguna memperoleh informasi secara lebih terstruktur.

Penggunaan Laravel, MySQL, Git, GitHub, dan MIT License mendukung penerapan teknologi open source dalam proses pengembangan proyek. Melalui kolaborasi empat anggota kelompok, proyek akan dikembangkan secara bertahap mulai dari perencanaan, implementasi, hingga pengujian dan demonstrasi.

---
