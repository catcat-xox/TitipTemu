# PRODUCT REQUIREMENTS DOCUMENT (PRD) — TitipTemu

**Versi:** 2.0 (draft untuk konfirmasi kelompok)  
**Status:** Draft — belum seluruh keputusan disahkan  
**Produk:** Website TitipTemu  
**Tujuan:** Informasi dan pengelolaan laporan barang hilang serta dokumentasi barang yang telah dikembalikan  
**Lingkup awal:** Satu fakultas/program studi/kampus (lingkup final perlu dipilih)  
**Stack acuan dari dokumen sebelumnya:** Laravel, MySQL


## 1. Ringkasan Produk

TitipTemu adalah website yang membantu civitas akademika mencari informasi barang hilang dan menyampaikan laporan kehilangan di lingkungan institusi. Informasi laporan yang telah disetujui Admin ditampilkan kepada publik agar orang yang menemukan barang dapat mengetahui siapa yang kehilangan dan bagaimana informasi tersebut dapat ditindaklanjuti.

TitipTemu menyediakan dua halaman publik utama: **Barang Hilang** dan **Barang Ditemukan**. Barang Hilang berisi laporan kehilangan yang telah disetujui Admin dan masih aktif. Barang Ditemukan, menurut konsep awal, berisi laporan kehilangan yang telah dikonfirmasi kembali oleh pelapor dan disetujui Admin sebagai barang yang sudah kembali ke pemilik. Item pada halaman Barang Ditemukan ditampilkan selama 30 hari, kemudian tidak lagi tampil secara publik.

Guest dapat melihat informasi tanpa login. Pengguna harus memiliki akun dan login untuk membuat laporan kehilangan serta mengirim konfirmasi pengembalian. Admin memeriksa laporan dan konfirmasi sebelum mengubah status/publikasi.

## 2. Masalah yang ingin diselesaikan
- Informasi kehilangan barang sering tersebar di grup percakapan, media sosial, atau pengumuman.
- Pelapor kesulitan menjangkau orang yang mungkin menemukan barang.
- Informasi kehilangan tidak tersimpan dalam daftar terpusat dan mudah ditelusuri.
- Informasi publik perlu dimoderasi agar laporan tidak menyesatkan atau mengandung data pribadi berlebihan.
- Status barang dan proses pengembaliannya perlu tercatat dengan jelas.

## 3. Tujuan Produk
- Memusatkan laporan barang hilang dalam satu website untuk lingkup yang disepakati.
- Memudahkan mahasiswa membuat laporan kehilangan dengan format terstruktur dan foto.
- Memungkinkan publik melihat dan mencari laporan tanpa akun.
- Memastikan laporan dipublikasikan setelah persetujuan Admin.
- Menyediakan proses konfirmasi pengembalian oleh pelapor disertai bukti foto dan pemeriksaan Admin.
- Menyediakan riwayat status bagi pelapor dan Admin.

## 4. Pengguna dan peran
### 4.1 Guest
- Melihat beranda, Barang Hilang, Barang Ditemukan, detail publik, pencarian/filter, dan informasi panduan.
- Tidak dapat membuat laporan atau mengonfirmasi pengembalian.
### 4.2 User (pelapor)
- Mendaftar dan login.
- Membuat laporan kehilangan, mengunggah foto, menyetujui ketentuan.
- Melihat laporan dan status miliknya.
- Mengedit/membatalkan laporan sesuai aturan yang nantinya disepakati.
- Mengajukan konfirmasi barang telah kembali pada laporan miliknya, mengisi data konfirmasi dan mengunggah foto bukti.
- Melihat status pemeriksaan konfirmasi pengembalian.
### 4.3 Admin
- Login ke area admin.
- Memeriksa, menyetujui, menolak, atau meminta perbaikan laporan.
- Mengelola laporan, status, kategori, dan moderasi.
- Memeriksa bukti pengembalian, menyetujui atau menolak konfirmasi.
- Mengelola publikasi, arsip, dan aktivitas moderasi.

## 5. Prinsip produk
1. Publik dapat menemukan informasi tanpa harus membuat akun.
2. Laporan baru tidak otomatis dipublikasikan.
3. Informasi yang ditampilkan cukup membantu pencarian, tetapi tidak membocorkan detail sensitif yang dapat memicu klaim palsu.
4. Foto dan data pribadi hanya digunakan sesuai tujuan sistem dan aksesnya dibatasi.
5. Perubahan status penting dicatat.
6. Pengembalian barang harus dikonfirmasi oleh pelapor dan diperiksa Admin.

## 6. Navigasi
### 6.1 Publik
- Beranda
- Barang Hilang
- Barang Ditemukan (riwayat barang yang telah kembali — **definisi ini perlu dikonfirmasi**)
- Detail laporan
- Cari/filter
- Panduan / Ketentuan
- Login
- Daftar
### 6.2 User
- Beranda
- Barang Hilang
- Barang Ditemukan
- Buat Laporan Barang Hilang
- Laporan Saya
- Detail Laporan Saya
- Konfirmasi Barang Telah Kembali
- Profil
- Logout
### 6.3 Admin
- Dashboard
- Laporan menunggu verifikasi
- Laporan aktif / Barang Hilang
- Konfirmasi pengembalian menunggu verifikasi
- Barang Ditemukan / riwayat pengembalian
- Detail laporan
- Pengaturan kategori (jika diaktifkan)
- Riwayat aktivitas
- Logout

## 7. Alur utama
### 7.1 Browsing sebagai Guest
1. Guest membuka TitipTemu.
2. Guest melihat dua navigasi utama: Barang Hilang dan Barang Ditemukan.
3. Guest membuka daftar, melakukan pencarian/filter, lalu membuka detail.
4. Jika ingin melaporkan kehilangan, sistem mengarahkan Guest ke login/registrasi.
### 7.2 Membuat laporan kehilangan
1. User login.
2. User membuka formulir laporan.
3. User mengisi data yang wajib, mengunggah foto, dan menyetujui ketentuan.
4. Sistem memvalidasi input.
5. Laporan dibuat berstatus menunggu Admin.
6. Admin meninjau laporan.
7. Admin menyetujui, menolak, atau meminta revisi (opsi meminta revisi perlu dikonfirmasi).
8. Laporan yang disetujui tampil pada Barang Hilang.
### 7.3 Konfirmasi pengembalian
1. User pemilik laporan membuka laporan miliknya yang masih aktif.
2. User memilih konfirmasi barang telah kembali.
3. User mengisi informasi konfirmasi dan mengunggah foto bukti.
4. Sistem menyimpan permintaan konfirmasi dengan status menunggu Admin.
5. Admin memeriksa informasi dan bukti.
6. Admin menyetujui atau menolak.
7. Jika disetujui, laporan tidak lagi muncul pada Barang Hilang dan masuk Barang Ditemukan.
8. Barang Ditemukan ditampilkan selama 30 hari, lalu diarsipkan/tidak ditampilkan kepada publik secara otomatis. Kebijakan penghapusan permanen data belum ditetapkan.
## 8. Fitur MVP
### Must Have
- Halaman publik/Guest.
- Daftar Barang Hilang.
- Daftar Barang Ditemukan (sesuai definisi sementara di atas).
- Detail laporan.
- Search dasar.
- Registrasi dan login User.
- Login Admin.
- Form laporan kehilangan dengan foto wajib.
- Persetujuan ketentuan pelaporan.
- Moderasi laporan oleh Admin.
- Dashboard/riwayat laporan milik User.
- Form konfirmasi pengembalian pada laporan milik User.
- Foto bukti pengembalian wajib.
- Moderasi konfirmasi pengembalian oleh Admin.
- Perpindahan status/list setelah konfirmasi disetujui.
- Pengarsipan otomatis dari daftar publik Barang Ditemukan setelah 30 hari.
- Pembatasan akses dan perlindungan data.
## 9. Data laporan kehilangan (draft)
Data awal yang disarankan untuk form:
- Nama barang (wajib)
- Kategori (wajib)
- Deskripsi umum (wajib; jangan memuat seluruh ciri unik yang dapat digunakan untuk verifikasi)
- Tanggal kehilangan (wajib)
- Lokasi kehilangan (wajib)
- Foto barang (wajib, jumlah dan batas ukuran perlu disepakati)
- Informasi kontak atau cara menghubungi pelapor (sebaiknya tidak ditampilkan secara publik; mekanisme kontak perlu dikonfirmasi)
- Pernyataan persetujuan ketentuan (wajib)
## 10. Data konfirmasi pengembalian (draft)
- Waktu/tanggal barang diterima (wajib atau otomatis; perlu diputuskan)
- Catatan singkat pengembalian (wajib atau opsional; perlu diputuskan)
- Foto bukti pengembalian (wajib)
- Checkbox pernyataan bahwa barang sudah diterima oleh pelapor (wajib)
- Status verifikasi Admin
- Catatan Admin jika ditolak
## 11. Aturan bisnis draft
- BR-01: Lingkup institusi ditentukan dan dibatasi sesuai kesepakatan kelompok.
- BR-02: Guest dapat melihat data publik tanpa login.
- BR-03: User harus login untuk membuat laporan dan mengonfirmasi pengembalian.
- BR-04: Laporan kehilangan wajib memiliki foto dan data wajib lengkap.
- BR-05: Laporan baru tidak tampil publik sebelum disetujui Admin.
- BR-06: Hanya pemilik laporan yang dapat mengajukan konfirmasi pengembalian untuk laporannya.
- BR-07: Konfirmasi pengembalian wajib memiliki foto bukti dan persetujuan pernyataan.
- BR-08: Konfirmasi pengembalian menunggu pemeriksaan Admin.
- BR-09: Setelah konfirmasi disetujui, laporan berpindah dari Barang Hilang ke Barang Ditemukan.
- BR-10: Barang Ditemukan ditampilkan selama 30 hari sejak waktu yang ditentukan (**awal hitungan 30 hari perlu dikonfirmasi**), kemudian tidak tampil di daftar publik.
- BR-11: Pengarsipan 30 hari tidak sama dengan penghapusan permanen database. Kebijakan retensi data perlu diputuskan.
- BR-12: Bukti foto konfirmasi dan data pribadi tidak ditampilkan kepada Guest kecuali kelompok menyetujui bagian tertentu sebagai informasi publik.
- BR-13: Admin dapat menolak konfirmasi pengembalian dengan alasan/catatan.
## 12. Status dan transisi
### 12.1 Status laporan
- `Pending`: menunggu pemeriksaan Admin.
- `Approved / Lost Active`: disetujui dan ditampilkan pada Barang Hilang.
- `Rejected`: ditolak Admin.
- `Return Pending`: User mengirim konfirmasi pengembalian, menunggu Admin.
- `Returned`: pengembalian disetujui; ditampilkan pada Barang Ditemukan.
- `Archived`: masa tampil 30 hari berakhir; tidak tampil publik (data tetap disimpan, kecuali kebijakan retensi mengatur berbeda).
- `Cancelled`: laporan dibatalkan (ketersediaan dan siapa yang boleh membatalkan perlu dikonfirmasi).
### 12.2 Alur transisi dasar
`Pending → Approved/Lost Active → Return Pending → Returned → Archived`
Penolakan laporan: `Pending → Rejected`.  
Penolakan konfirmasi pengembalian: `Return Pending → Approved/Lost Active` (rekomendasi; perlu disetujui).  
Pembatalan: transisi dari status yang diizinkan belum ditetapkan.
## 13. Informasi publik dan privasi
### Publik dapat melihat (draft)
- Nama barang, kategori, deskripsi yang telah dimoderasi.
- Foto laporan yang disetujui dan aman ditampilkan.
- Tanggal dan lokasi kehilangan dengan tingkat detail yang ditentukan Admin.
- Status laporan.
### Tidak ditampilkan publik (draft)
- NIM/nomor identitas mahasiswa.
- Email pribadi, nomor telepon, alamat, dan data akun.
- Foto bukti pengembalian yang digunakan khusus untuk verifikasi (kecuali dipilih foto publik terpisah).
- Detail unik barang yang sengaja disimpan sebagai bahan verifikasi.
## 14. Kebutuhan nonfungsional
- NFR-01: Halaman publik responsif pada desktop dan mobile.
- NFR-02: Password disimpan dengan hashing yang aman.
- NFR-03: Input divalidasi di server.
- NFR-04: Akses User dan Admin dibatasi dengan autentikasi dan otorisasi.
- NFR-05: File upload divalidasi MIME/format, ukuran, dan akses.
- NFR-06: Bukti pengembalian tidak dapat diakses publik secara langsung.
- NFR-07: Perubahan status laporan dan keputusan Admin tercatat.
- NFR-08: Sistem menyediakan backup database yang dapat dijalankan pengelola.
- NFR-09: Antarmuka mudah dipahami dan navigasi utama konsisten.
- NFR-10: Struktur Laravel mudah dipelihara dan dikembangkan.
- NFR-11: Target awal waktu muat beranda ≤3 detik dan pencarian ≤2 detik pada kondisi uji yang disepakati (target, bukan jaminan produksi).
## 15. KPI / kriteria keberhasilan MVP
- Guest dapat melihat Barang Hilang dan Barang Ditemukan tanpa login.
- Guest tidak dapat membuat laporan atau konfirmasi pengembalian.
- User dapat mendaftar, login, dan mengirim laporan lengkap beserta foto.
- Laporan tidak tampil sebelum persetujuan Admin.
- Admin dapat menyetujui/menolak laporan.
- User hanya dapat mengirim konfirmasi untuk laporan miliknya.
- Konfirmasi pengembalian wajib memuat foto dan pernyataan.
- Admin dapat menyetujui/menolak konfirmasi pengembalian.
- Laporan yang disetujui pengembaliannya berpindah daftar dengan benar.
- Item Barang Ditemukan tidak lagi tampil publik setelah 30 hari.
- Data pribadi dan bukti verifikasi tidak terbuka untuk Guest.
## 16. Risiko dan mitigasi
| Risiko | Mitigasi draft |
|---|---|
| Laporan palsu/spam | Login, validasi, moderasi Admin, batas frekuensi jika diperlukan |
| Foto atau data tidak pantas | Moderasi dan ketentuan upload |
| Bukti pengembalian palsu | Admin memeriksa bukti; keputusan status tidak otomatis |
| Informasi sensitif terpublikasi | Minimalkan field publik dan batasi akses file |
| Admin lambat memeriksa | Dashboard antrean dan penanda pending |
| Data hilang | Backup database dan file foto |
| Postingan tetap muncul melewati 30 hari | Job terjadwal untuk mengubah visibilitas/arsip, bukan mengandalkan penghapusan manual |
## 17. Keputusan yang perlu dikonfirmasi
Daftar ini adalah pertanyaan terbuka, bukan aturan final. Lihat bagian “Pertanyaan konfirmasi” di akhir dokumen.
## 18. Batasan teknis awal
- Website, bukan aplikasi mobile native.
- Laravel dan MySQL mengikuti baseline sebelumnya, kecuali kelompok mengubah keputusan.
- Guest/public read-only.
- Akun User dan Admin.
- Belum ada SSO kampus.
- Foto laporan dan bukti pengembalian disimpan dengan kontrol akses.
## 19. Rencana kerja
1. Finalisasi PRD dan Requirement.
2. Tentukan flow, status, form, ketentuan dan wireframe.
3. Rancang ERD, skema database, dan API/route.
4. Implementasi autentikasi dan role.
5. Implementasi publikasi laporan dan dashboard Admin.
6. Implementasi konfirmasi pengembalian serta pengarsipan 30 hari.
7. Pengujian, dokumentasi, demo, dan evaluasi.
## 20. Dokumen proyek terkait
- `PRD.md`: tujuan, ruang lingkup, pengguna, fitur, alur produk.
- `REQUIREMENT.md`: requirement fungsional/nonfungsional, validasi, aturan status dan acceptance criteria.
- Use Case Diagram, Activity Diagram, ERD, Data Dictionary, wireframe, test case.
## 21. Riwayat revisi
| Versi | Perubahan |
|---|---|
| 1.1 | PRD sebelumnya masih memuat laporan penemuan terpisah, klaim digital, dan pertemuan langsung. |
| 2.0 | Draft disesuaikan dengan konsep TitipTemu: laporan kehilangan, persetujuan Admin, konfirmasi pengembalian oleh pelapor dengan foto, perpindahan ke Barang Ditemukan, dan masa tampil 30 hari. |


## Pertanyaan konfirmasi kelompok
1. Apakah “Barang Ditemukan” benar-benar hanya berisi laporan kehilangan yang sudah dikembalikan, atau juga menerima laporan barang temuan baru dari orang yang menemukan barang?
2. Siapa saja yang boleh mendaftar: mahasiswa saja, dosen/staf, atau seluruh civitas akademika?
3. Apakah pendaftaran harus menggunakan email kampus/NIM dan apakah perlu verifikasi email?
4. Form laporan kehilangan: field apa yang wajib? Rekomendasi: nama barang, kategori, deskripsi, tanggal, lokasi, foto, dan ketentuan.
5. Berapa foto yang boleh diunggah, format, dan ukuran maksimal?
6. Apa ketentuan laporan yang harus dicentang user? Apakah perlu teks persetujuan yang akan disusun?
7. Informasi kontak pelapor apakah ditampilkan publik, disembunyikan, atau komunikasi hanya melalui admin?
8. Pada konfirmasi barang kembali, foto bukti harus memperlihatkan barang, pemilik, proses penyerahan, atau cukup barang saja? (Pertimbangkan privasi.)
9. Apakah User boleh membatalkan laporan atau mengubahnya setelah disetujui?
10. Jika Admin menolak bukti pengembalian, apakah laporan tetap berada di Barang Hilang dan User boleh mengirim ulang bukti?
11. Hitungan 30 hari pada Barang Ditemukan dimulai dari waktu Admin menyetujui pengembalian, atau dari tanggal user menyatakan barang kembali?
12. Setelah 30 hari, apakah postingan hanya disembunyikan/diarsipkan atau benar-benar dihapus dari database?
13. Apakah Admin boleh menghapus laporan, atau hanya menolak/arsipkan?
14. Lingkup final TitipTemu: fakultas tertentu, program studi, atau seluruh kampus?
15. Apakah barang ditemukan baru boleh dikembalikan setelah Admin menyetujui bukti, atau user mengonfirmasi dahulu lalu Admin mengesahkan status?
