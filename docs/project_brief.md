# PROJECT BRIEF
# TITIPTEMU
### Portal Barang Hilang & Temuan Kampus: Pelaporan oleh User, Pencarian oleh Guest, Verifikasi oleh Admin

**Platform:** Website  
**Teknologi:** Laravel dan MySQL  
**Lisensi:** MIT License  
**Status:** Dokumen perencanaan; alur kerja diselaraskan dengan keputusan pemilik A18 (2026-11-22)

> **Catatan A18 (2026-11-22):** baseline aktif adalah dua kanal laporan. **Barang Hilang** berisi laporan **kehilangan** yang dibuat User pemilik; **Barang Ditemukan** berisi laporan **temuan** yang dibuat User penemu. Keduanya tersimpan di tabel terpisah, tidak ditautkan, memakai dua card status per menu, dan aturan 30 hari pada daftar “Sudah”. Catatan lama yang menyatakan Barang Ditemukan hanya riwayat pengembalian **sudah digantikan**. Akun Admin terpisah dan masuk melalui jalur tersembunyi.

---

## 1. Latar Belakang

Kehilangan barang di lingkungan kampus merupakan kejadian yang dapat dialami oleh mahasiswa maupun civitas akademika. Informasi kehilangan barang sering disampaikan melalui grup WhatsApp, media sosial, atau pengumuman secara langsung. Cara tersebut terkadang membuat informasi sulit ditemukan kembali karena bercampur dengan pesan lain dan tidak tersimpan dalam satu tempat yang terstruktur.

Berdasarkan permasalahan tersebut, kelompok kami mengusulkan pengembangan **TitipTemu**, yaitu website yang memusatkan dua kanal laporan di lingkungan institusi pendidikan: **laporan kehilangan** yang dibuat pemilik dan **laporan temuan** yang dibuat penemu. Tiap laporan berisi data pelapor, informasi barang, dan foto, lalu diperiksa Admin sebelum ditampilkan kepada publik.

Guest dapat mencari informasi di kedua kanal tanpa akun. Setiap detail publik menyediakan tombol WhatsApp untuk menghubungi pemilik atau penemu; nomor HP tetap disembunyikan dan tidak ditampilkan sebagai teks.

TitipTemu juga menyediakan konfirmasi status “Sudah”. Pada laporan temuan, User mengirimkan **nama pemilik** dan **foto bersama penemu–pemilik** sebagai bukti. Admin memeriksa bukti tersebut. Apabila disetujui, laporan berpindah dari **Barang Belum Kembali** ke **Barang Sudah Kembali** di menu **Barang Ditemukan**. Laporan kehilangan berpindah dari **Barang Belum Ditemukan** ke **Barang Sudah Ditemukan** di menu **Barang Hilang**. Keduanya tetap terpisah dan tidak dicocokkan.

Dengan adanya TitipTemu, informasi barang hilang, barang temuan, dan statusnya diharapkan dapat dikelola secara lebih terpusat, terstruktur, dan mudah diakses.

## 2. Tujuan Proyek

Tujuan pengembangan TitipTemu adalah:

1. Menyediakan website sebagai pusat laporan kehilangan dan laporan temuan di lingkungan institusi.
2. Mempermudah User melaporkan kehilangan maupun temuan dengan data pelapor, informasi barang, dan foto.
3. Memungkinkan Guest melihat dan mencari informasi barang pada kedua kanal tanpa login.
4. Menyediakan proses pemeriksaan kedua jenis laporan oleh Admin sebelum dipublikasikan.
5. Menghubungkan Guest dengan pemilik/penemu melalui WhatsApp tanpa menampilkan nomor HP secara publik.
6. Menyediakan fitur konfirmasi status “Sudah” dengan bukti privat dan aturan tampil 30 hari.
7. Menyediakan dua card status per menu, dropdown **Buat Laporan**, dan sidebar Admin 5 menu.
8. Menerapkan pengembangan perangkat lunak menggunakan teknologi open source.

## 3. Ruang Lingkup Proyek

Pengembangan TitipTemu ditujukan untuk seluruh lingkungan kampus, bukan hanya satu fakultas atau program studi.

### A. Fitur utama

1. **Halaman Beranda:** menampilkan informasi umum dan navigasi utama website.
2. **Barang Hilang:** menampilkan laporan **kehilangan** pemilik yang disetujui Admin, dengan card **Barang Belum Ditemukan** dan **Barang Sudah Ditemukan**.
3. **Barang Ditemukan:** menampilkan laporan **temuan** penemu yang disetujui Admin, dengan card **Barang Belum Kembali** dan **Barang Sudah Kembali**.
4. **Card status publik:** dapat diklik dan membawa ke state berbeda di dalam menu aktif; diikuti pencarian dan card item.
5. **Pencarian Barang:** membantu Guest mencari informasi berdasarkan kata kunci di dalam menu aktif.
6. **Kontak WhatsApp:** tombol pada detail publik yang membuka chat dengan pemilik/penemu tanpa menampilkan nomor HP.
7. **Registrasi dan Login User:** menyediakan akun untuk membuat kedua jenis laporan dan mengirim konfirmasi status.
8. **Login Admin terpisah:** jalur masuk tersembunyi, tidak ditautkan pada navigasi publik.
9. **Buat Laporan:** dropdown di navbar berisi **Laporkan Kehilangan Barang** dan **Laporkan Barang Temuan**; **Akun** menampilkan daftar keduanya tanpa tombol buat laporan.
10. **Akun:** menampilkan profil, aktivitas, dan kedua jenis laporan milik User beserta statusnya.
11. **Konfirmasi status “Sudah”:** User mengirim bukti privat; pada laporan temuan memakai nama pemilik dan foto bersama penemu–pemilik.
12. **Dashboard Admin:** sidebar 5 menu — Dashboard, Cari laporan, Barang Hilang, Barang Ditemukan, Keluar Admin — dengan card status yang dapat diklik; menu jenis difilter menurut jenis laporan.
13. **Pengarsipan dan retensi:** kedua daftar “Sudah” tidak ditampilkan setelah 30 hari dan diarsipkan; data tetap disimpan kecuali Admin melakukan penghapusan permanen yang disetujui.

### B. Hak akses pengguna

| Pengguna | Hak akses |
|---|---|
| Guest | Melihat beranda, dua daftar barang, detail publik, melakukan pencarian, dan menghubungi pemilik/penemu via WhatsApp tanpa login |
| User (mahasiswa, dosen, atau staf aktif) | Melaporkan kehilangan maupun temuan, mengunggah foto, melihat kedua jenis laporan di menu **Akun**, menghubungi pelapor, dan mengirim konfirmasi status |
| Admin | Memeriksa, menyetujui, menolak, mengedit laporan pada status menunggu verifikasi, memeriksa bukti privat, mengelola publikasi, dan menghapus permanen laporan selesai/arsip |

Guest merupakan pengunjung tanpa login, bukan jenis akun terdaftar. Sistem memiliki dua jenis akun yang terpisah: User dan Admin. Admin masuk melalui jalur tersembunyi dan tidak memiliki tautan pada navigasi publik. Beberapa akun Admin dapat berdiri sendiri.

### C. Batasan proyek

Pada tahap MVP, proyek berfokus pada website. Pengembangan aplikasi mobile native, integrasi WhatsApp API, notifikasi SMS, pencocokan barang menggunakan AI maupun aturan sederhana, penautan laporan pemilik dan penemu, dan integrasi SSO akademik belum termasuk ruang lingkup utama. Field/bukti konfirmasi laporan kehilangan serta penggantian URI publik masih terbuka pada A18.

## 4. Alur Kerja Sistem

### A. Proses pelaporan kehilangan (pemilik)

1. User melakukan login.
2. User membuka **Buat Laporan → Laporkan Kehilangan Barang** atau tombol di **Akun**.
3. User mengisi data pemilik (nama tercatat pada akun dan **no. HP pemilik**), informasi barang seperti nama, kategori, deskripsi, tanggal dan lokasi **hilang**.
4. User mengunggah foto barang dan menyetujui ketentuan laporan.
5. Sistem memvalidasi data dan menyimpan ke tabel laporan kehilangan dengan status menunggu verifikasi.
6. Admin memeriksa laporan; Admin juga dapat mengedit langsung laporan pada status menunggu verifikasi dan mencatat perubahannya.
7. Admin menyetujui atau menolak dengan alasan wajib saat menolak; User dapat memperbaiki dan mengirim ulang.
8. Jika disetujui, laporan ditampilkan pada **Barang Hilang → Barang Belum Ditemukan**.
9. Guest membuka detail dan memakai tombol WhatsApp. Sistem mengarahkan ke `wa.me` tanpa menampilkan nomor pemilik.

### B. Proses pelaporan temuan (penemu)

1. User melakukan login.
2. User membuka **Buat Laporan → Laporkan Barang Temuan** atau tombol di **Akun**.
3. User mengisi data penemu (nama tercatat pada akun dan **no. HP penemu**), informasi barang seperti nama, kategori, deskripsi, tanggal dan lokasi **ditemukan**.
4. User mengunggah foto barang dan menyetujui ketentuan laporan.
5. Sistem memvalidasi data dan menyimpan ke tabel laporan temuan (`lost_reports`) dengan status menunggu verifikasi.
6. Admin memeriksa, lalu menyetujui atau menolak dengan alasan wajib saat menolak.
7. Jika disetujui, laporan ditampilkan pada **Barang Ditemukan → Barang Belum Kembali**.
8. Laporan pemilik dan penemu tidak dicocokkan atau ditautkan, meskipun barangnya sama.

### C. Proses konfirmasi status “Sudah”

1. User membuka laporan miliknya yang masih aktif lewat menu **Akun**.
2. User memilih konfirmasi status “Sudah”.
3. Untuk laporan temuan, User mengisi **nama pemilik**, mengunggah **foto bersama penemu–pemilik**, dan menyetujui pernyataan. Ketentuan untuk laporan kehilangan masih terbuka pada A18.
4. Sistem menyimpan konfirmasi dengan status menunggu pemeriksaan Admin; data privat tidak tampil di publik dan item tetap pada card “Belum”.
5. Admin memeriksa konfirmasi dan bukti melalui halaman privat.
6. Admin menyetujui atau menolak dengan alasan wajib saat menolak.
7. Jika disetujui, laporan berpindah ke card “Sudah” **di menu yang sama**: kehilangan ke **Barang Sudah Ditemukan**, temuan ke **Barang Sudah Kembali**. Jika ditolak, item tetap di card “Belum” dan User dapat memperbaiki serta mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
8. Setelah masa tampil 30 hari sejak persetujuan dan publikasi Admin, sistem mengarsipkan laporan sehingga tidak lagi tampil kepada publik; data tetap disimpan.
9. Admin dapat menghapus laporan selesai/arsip secara permanen setelah konfirmasi; penghapusan juga membersihkan foto dan bukti privat terkait.

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
| P15 — Implementasi | Mengembangkan autentikasi User/Admin terpisah, dua kanal laporan (kehilangan dan temuan) dengan tabel terpisah, halaman publik dengan 2 card status per menu, dropdown **Buat Laporan**, sidebar Admin 5 menu, konfirmasi status privat, dan pengarsipan 30 hari | Website MVP |
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

TitipTemu dirancang sebagai solusi berbasis website untuk membantu pengelolaan laporan kehilangan dan laporan temuan di lingkungan institusi pendidikan. Dengan fitur pelaporan oleh User pada dua kanal terpisah, pencarian oleh Guest, kontak WhatsApp tanpa eksposur nomor, verifikasi Admin terpisah, konfirmasi status “Sudah” yang privat, dan pencatatan status per menu, sistem ini diharapkan dapat membantu pengguna memperoleh informasi secara lebih terstruktur.

Penggunaan Laravel, MySQL, Git, GitHub, dan MIT License mendukung penerapan teknologi open source dalam proses pengembangan proyek. Melalui kolaborasi empat anggota kelompok, proyek akan dikembangkan secara bertahap mulai dari perencanaan, implementasi, hingga pengujian dan demonstrasi.

---
