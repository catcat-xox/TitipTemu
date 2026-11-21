# PRODUCT REQUIREMENTS DOCUMENT (PRD) — TitipTemu

**Versi:** 2.6 (seluruh keputusan produk P0 diterapkan)

**Status:** Disetujui untuk MVP; pertanyaan non-P0 yang tersisa dicatat terbuka

**Produk:** Website TitipTemu

**Tujuan:** Informasi dan pengelolaan laporan barang hilang serta dokumentasi barang yang telah dikembalikan

**Lingkup:** Seluruh lingkungan kampus

**Stack acuan:** Laravel, MySQL, Blade, Bootstrap


## 1. Ringkasan Produk

TitipTemu adalah website yang membantu civitas akademika mencari informasi barang hilang dan menyampaikan laporan kehilangan di lingkungan institusi. Informasi laporan yang telah disetujui Admin ditampilkan kepada publik agar orang yang menemukan barang dapat mengetahui siapa yang kehilangan dan bagaimana informasi tersebut dapat ditindaklanjuti.

TitipTemu menyediakan dua halaman publik utama: **Barang Hilang** dan **Barang Ditemukan**. Barang Hilang berisi laporan kehilangan yang telah disetujui Admin dan masih aktif. Barang Ditemukan hanya berisi laporan kehilangan yang telah berhasil dikembalikan kepada pemilik, setelah konfirmasi pengembalian disetujui Admin. Sistem tidak menerima laporan barang temuan baru. Item pada halaman Barang Ditemukan ditampilkan selama 30 hari, kemudian tidak lagi tampil secara publik.

Guest dapat melihat informasi tanpa login. Pengguna harus memiliki akun, login, dan email terverifikasi untuk membuat laporan kehilangan serta mengirim konfirmasi pengembalian. Admin memeriksa laporan dan konfirmasi sebelum mengubah status/publikasi.

## 2. Masalah yang ingin diselesaikan
- Informasi kehilangan barang sering tersebar di grup percakapan, media sosial, atau pengumuman.
- Pelapor kesulitan menjangkau orang yang mungkin menemukan barang.
- Informasi kehilangan tidak tersimpan dalam daftar terpusat dan mudah ditelusuri.
- Informasi publik perlu dimoderasi agar laporan tidak menyesatkan atau mengandung data pribadi berlebihan.
- Status barang dan proses pengembaliannya perlu tercatat dengan jelas.

## 3. Tujuan Produk
- Memusatkan laporan barang hilang dalam satu website untuk seluruh lingkungan kampus.
- Memudahkan mahasiswa, dosen, dan staf aktif membuat laporan kehilangan dengan format terstruktur dan foto.
- Memungkinkan publik melihat dan mencari laporan tanpa akun.
- Memastikan laporan dipublikasikan setelah persetujuan Admin.
- Menyediakan proses konfirmasi pengembalian oleh pelapor disertai bukti foto dan pemeriksaan Admin.
- Menyediakan riwayat status bagi pelapor dan Admin.

## 4. Pengguna dan peran
### 4.1 Guest
- Melihat beranda, Barang Hilang, Barang Ditemukan, detail publik, pencarian/filter, dan informasi panduan.
- Tidak dapat membuat laporan atau mengonfirmasi pengembalian.
### 4.2 User (pelapor)
- User adalah mahasiswa, dosen, atau staf aktif kampus yang memiliki akun.
- Mendaftar dan login.
- Membuat laporan kehilangan, mengunggah foto, menyetujui ketentuan.
- Melihat laporan dan status miliknya.
- Memperbaiki dan mengirim ulang laporan yang ditolak setelah membaca alasan Admin.
- Mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung.
- Mengajukan konfirmasi barang telah kembali pada laporan miliknya, mengisi data konfirmasi dan mengunggah foto bukti.
- Melihat status pemeriksaan konfirmasi pengembalian.
### 4.3 Admin
- Sistem mendukung beberapa akun Admin, masing-masing dengan akun sendiri.
- Login ke area admin.
- Memeriksa, menyetujui, atau menolak laporan dengan alasan wajib saat menolak.
- Mengelola laporan, status, dan moderasi; pengelolaan kategori hanya jika diaktifkan.
- Memeriksa bukti pengembalian, menyetujui atau menolak konfirmasi dengan alasan wajib saat menolak.
- Mengelola publikasi, arsip, dan aktivitas moderasi.

### 4.4 Data pendaftaran User
- Nama lengkap — wajib.
- NIM/NIP — wajib dan privat.
- Email — wajib, unik, dan harus diverifikasi sebelum User dapat membuat laporan.
- Password — wajib dan disimpan dengan hashing aman.
- Fakultas — wajib dan privat.
- Program studi — wajib untuk mahasiswa; tidak berlaku untuk dosen dan staf.
- Sistem mencatat identitas kampus, tetapi tidak melakukan pencocokan dengan database resmi kampus.

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
- Barang Ditemukan (riwayat laporan kehilangan yang telah kembali)
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
7. Admin menyetujui atau menolak dengan alasan yang wajib dicatat; User dapat memperbaiki dan mengirim ulang laporan yang ditolak.
8. Laporan yang disetujui tampil pada Barang Hilang.
### 7.3 Konfirmasi pengembalian
1. User pemilik laporan membuka laporan miliknya yang masih aktif.
2. User memilih konfirmasi barang telah kembali.
3. User mengisi informasi konfirmasi dan mengunggah foto bukti.
4. Sistem menyimpan permintaan konfirmasi dengan status menunggu Admin.
5. Admin memeriksa informasi dan bukti.
6. Admin menyetujui atau menolak dengan alasan wajib saat menolak.
7. Jika disetujui, laporan tidak lagi muncul pada Barang Hilang dan masuk Barang Ditemukan.
8. Jika ditolak, laporan tetap aktif di Barang Hilang; User dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
9. Barang Ditemukan ditampilkan selama 30 hari sejak Admin menyetujui pengembalian dan mempublikasikannya, lalu diarsipkan/tidak ditampilkan kepada publik secara otomatis. Data tetap disimpan dan foto bukti pengembalian tetap privat.
## 8. Fitur MVP
### Must Have
- Halaman publik/Guest.
- Daftar Barang Hilang.
- Daftar Barang Ditemukan berdasarkan laporan kehilangan yang telah kembali.
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
## 9. Data laporan kehilangan
Field formulir yang ditetapkan:
- Nama barang (wajib)
- Kategori (wajib)
- Deskripsi umum (wajib; jangan memuat seluruh ciri unik yang dapat digunakan untuk verifikasi)
- Tanggal kehilangan (wajib)
- Lokasi kehilangan (wajib)
- Foto barang (wajib; maksimal 3 foto)
- Format foto: JPG, JPEG, PNG, atau WebP
- Ukuran maksimal: 2 MB per foto
- Foto disimpan pada storage lokal Laravel dan hanya ditampilkan publik setelah laporan disetujui Admin
- Informasi kontak atau cara menghubungi pelapor (tidak ditampilkan secara publik)
- Pernyataan persetujuan ketentuan (wajib)

Kategori tetap:
- Elektronik
- Dompet & Uang
- Dokumen & Kartu Identitas
- Kunci
- Tas
- Pakaian & Aksesoris
- Buku & Alat Tulis
- Lainnya
## 10. Data konfirmasi pengembalian
- Waktu/tanggal barang diterima (wajib atau otomatis; perlu diputuskan)
- Catatan singkat pengembalian (wajib atau opsional; perlu diputuskan)
- Foto bukti pengembalian (wajib; batas format/ukuran belum ditetapkan)
- Checkbox pernyataan bahwa barang sudah diterima oleh pelapor (wajib)
- Status verifikasi Admin
- Alasan Admin (wajib jika ditolak)
## 11. Aturan bisnis
- BR-01: Sistem berlaku untuk seluruh lingkungan kampus.
- BR-02: Guest dapat melihat data publik tanpa login.
- BR-03: User harus login untuk membuat laporan dan mengonfirmasi pengembalian.
- BR-04: Laporan kehilangan wajib memiliki foto dan data wajib lengkap.
- BR-05: Laporan baru berstatus `Pending` dan tidak tampil publik. Laporan `Approved`/`Lost Active` dan `Return Pending` dapat tampil di Barang Hilang; status lain tidak.
- BR-06: Hanya pemilik laporan yang dapat mengajukan konfirmasi pengembalian untuk laporannya.
- BR-07: Konfirmasi pengembalian wajib memiliki foto bukti dan persetujuan pernyataan.
- BR-08: Konfirmasi pengembalian menunggu pemeriksaan Admin.
- BR-09: Setelah konfirmasi disetujui, laporan berpindah dari Barang Hilang ke Barang Ditemukan.
- BR-10: Barang Ditemukan ditampilkan selama 30 hari sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya pada daftar tersebut, menggunakan zona waktu Asia/Makassar (WITA). Setelah batas itu, laporan tidak tampil di daftar publik dan berstatus `Archived`.
- BR-11: Pengarsipan 30 hari tidak menghapus data secara permanen. Data laporan tetap disimpan di database dan foto bukti pengembalian tetap privat.
- BR-12: Bukti foto konfirmasi dan data pribadi, termasuk kontak pribadi pengguna, tidak ditampilkan kepada publik.
- BR-13: Admin wajib menyertakan alasan saat menolak konfirmasi pengembalian.
- BR-14: Kontak pribadi pengguna tidak ditampilkan kepada publik.
- BR-15: Pemilik dapat mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung.
- BR-16: Penolakan laporan wajib memiliki alasan. Pemilik dapat memperbaiki dan mengirim ulang laporan yang ditolak.
- BR-17: Penolakan konfirmasi pengembalian wajib memiliki alasan. Pemilik dapat memperbaiki dan mengirim ulang maksimal 3 kali; setelah batas tercapai, penyelesaian dilakukan melalui Admin.
- BR-18: Data laporan, data konfirmasi, dan riwayat status tidak dihapus permanen.
## 12. Status dan transisi
### 12.1 Status laporan
- `Pending`: menunggu pemeriksaan Admin.
- `Approved / Lost Active`: disetujui dan ditampilkan pada Barang Hilang.
- `Rejected`: ditolak Admin.
- `Return Pending`: User mengirim konfirmasi pengembalian, menunggu Admin; laporan tetap tampil di Barang Hilang sampai keputusan Admin.
- `Returned`: pengembalian disetujui; ditampilkan pada Barang Ditemukan.
- `Archived`: masa tampil 30 hari berakhir; tidak tampil publik; data dan riwayat tetap disimpan.
- `Cancelled`: laporan berstatus `Pending` dibatalkan oleh pemilik.
### 12.2 Alur transisi dasar
`Pending → Approved/Lost Active → Return Pending → Returned → Archived`
Penolakan laporan: `Pending → Rejected`, dengan alasan wajib.

Penolakan konfirmasi pengembalian: `Return Pending → Approved/Lost Active`; alasan wajib dicatat dan pengiriman ulang maksimal 3 kali, lalu penyelesaian melalui Admin.

Pembatalan: `Pending → Cancelled` oleh pemilik.
## 13. Informasi publik dan privasi
### Publik dapat melihat
- Nama barang, kategori, deskripsi yang telah dimoderasi.
- Foto laporan yang disetujui dan aman ditampilkan.
- Tanggal dan lokasi kehilangan; tingkat detail lokasi masih terbuka.
- Status laporan.
### Tidak ditampilkan publik
- NIM/nomor identitas mahasiswa.
- Email pribadi, nomor telepon, alamat, dan data akun.
- Foto bukti pengembalian yang digunakan khusus untuk verifikasi.
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
- Admin dapat menyetujui/menolak laporan dengan alasan wajib saat menolak.
- User hanya dapat mengirim konfirmasi untuk laporan miliknya.
- Konfirmasi pengembalian wajib memuat foto dan pernyataan.
- Admin dapat menyetujui/menolak konfirmasi pengembalian.
- Laporan yang disetujui pengembaliannya berpindah daftar dengan benar.
- Item Barang Ditemukan tidak lagi tampil publik setelah 30 hari.
- Data pribadi dan bukti verifikasi tidak terbuka untuk Guest.
## 16. Risiko dan mitigasi
| Risiko | Mitigasi |
|---|---|
| Laporan palsu/spam | Login, validasi, moderasi Admin, batas frekuensi jika diperlukan |
| Foto atau data tidak pantas | Moderasi dan ketentuan upload |
| Bukti pengembalian palsu | Admin memeriksa bukti; keputusan status tidak otomatis |
| Informasi sensitif terpublikasi | Minimalkan field publik dan batasi akses file |
| Admin lambat memeriksa | Dashboard antrean dan penanda pending |
| Data hilang | Backup database dan file foto |
| Postingan tetap muncul melewati 30 hari | Job terjadwal untuk mengubah visibilitas/arsip, bukan mengandalkan penghapusan manual |
## 17. Pertanyaan non-P0 yang terbuka
Keputusan P0 yang telah disetujui berlaku. Pertanyaan yang masih terbuka:
1. Pengelolaan kategori oleh Admin, atau kategori tetap saja.
2. Tingkat detail lokasi kehilangan yang ditampilkan publik.
3. Tanggal pengembalian wajib diisi atau dicatat otomatis, serta catatan pengembalian wajib atau opsional.
4. Batas format dan ukuran foto bukti pengembalian.
5. Teks ketentuan pelaporan yang wajib disetujui User.
6. Apakah notifikasi status termasuk MVP.

Lihat juga “Pertanyaan konfirmasi kelompok” di akhir dokumen.
## 18. Batasan teknis awal
- Website, bukan aplikasi mobile native.
- Laravel, MySQL, Blade, dan Bootstrap mengikuti keputusan P0.7 dan baseline teknis yang disetujui.
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
| 2.1 | P0.1 ditetapkan: Barang Ditemukan hanya berisi laporan kehilangan yang berhasil dikembalikan setelah konfirmasi disetujui Admin; laporan barang temuan baru tidak termasuk. |
| 2.2 | P0.2 ditetapkan: TitipTemu mencakup seluruh lingkungan kampus; mahasiswa, dosen, dan staf aktif dapat mendaftar; Guest tetap membaca publik tanpa akun. |
| 2.3 | P0.3 ditetapkan: field pendaftaran lengkap dan verifikasi email wajib sebelum pelaporan; identitas kampus dicatat tanpa pencocokan database resmi. |
| 2.4 | P0.4 ditetapkan: field laporan, delapan kategori tetap, maksimal 3 foto JPG/JPEG/PNG/WebP, maksimal 2 MB per foto, storage lokal Laravel, dan publikasi foto setelah persetujuan Admin. |
| 2.5 | P0.5 ditetapkan: kontak privat, edit/batal Pending, Approved tidak diedit langsung, alasan penolakan wajib, pengiriman ulang, batas 3 kali konfirmasi, serta retensi data dan riwayat. |
| 2.6 | P0.8 selesai: seluruh keputusan P0 diterapkan, versi/status diselaraskan, aturan draft yang sudah diputuskan difinalkan, dan daftar pertanyaan terbuka non-P0 disamakan dengan Requirement serta ARCHITECTURE. |


## Pertanyaan konfirmasi kelompok
1. Apakah “Barang Ditemukan” benar-benar hanya berisi laporan kehilangan yang sudah dikembalikan, atau juga menerima laporan barang temuan baru dari orang yang menemukan barang? **Sudah diputuskan: hanya laporan kehilangan yang berhasil dikembalikan setelah konfirmasi disetujui Admin; tidak ada laporan barang temuan baru.**
2. Siapa saja yang boleh mendaftar: mahasiswa saja, dosen/staf, atau seluruh civitas akademika? **Sudah diputuskan: mahasiswa, dosen, dan staf aktif kampus.**
3. Apakah pendaftaran harus menggunakan email kampus/NIM dan apakah perlu verifikasi email? **Sudah diputuskan: pendaftaran meminta nama lengkap, NIM/NIP, email, password, fakultas, dan program studi khusus mahasiswa; email wajib diverifikasi sebelum pelaporan; identitas tidak diverifikasi melalui database resmi kampus.**
4. Form laporan kehilangan: field apa yang wajib? **Sudah diputuskan: nama barang, kategori tetap, deskripsi, tanggal hilang, lokasi hilang, foto, dan ketentuan.**
5. Berapa foto yang boleh diunggah, format, dan ukuran maksimal? **Sudah diputuskan: maksimal 3 foto; JPG/JPEG/PNG/WebP; maksimal 2 MB per foto.**
6. Apa ketentuan laporan yang harus dicentang user? Apakah perlu teks persetujuan yang akan disusun?
7. Informasi kontak pelapor apakah ditampilkan publik, disembunyikan, atau komunikasi hanya melalui admin? **Sudah diputuskan: kontak pribadi pengguna tidak ditampilkan secara publik.**
8. Pada konfirmasi barang kembali, foto bukti harus memperlihatkan barang, pemilik, proses penyerahan, atau cukup barang saja? (Pertimbangkan privasi.)
9. Apakah User boleh membatalkan laporan atau mengubahnya setelah disetujui? **Sudah diputuskan: pemilik boleh mengedit/membatalkan saat `Pending`; `Approved` tidak dapat diedit langsung.**
10. Jika Admin menolak bukti pengembalian, apakah laporan tetap berada di Barang Hilang dan User boleh mengirim ulang bukti? **Sudah diputuskan: alasan wajib, perbaikan dan pengiriman ulang maksimal 3 kali; setelah itu melalui Admin.**
11. Hitungan 30 hari pada Barang Ditemukan dimulai dari waktu Admin menyetujui pengembalian, atau dari tanggal user menyatakan barang kembali? **Sudah diputuskan: sejak Admin menyetujui dan mempublikasikan, zona waktu Asia/Makassar.**
12. Setelah 30 hari, apakah postingan hanya disembunyikan/diarsipkan atau benar-benar dihapus dari database? **Sudah diputuskan: diarsipkan dan disembunyikan dari publik; data tetap disimpan, foto bukti tetap privat.**
13. Apakah Admin boleh menghapus laporan, atau hanya menolak/arsipkan? **Sudah diputuskan: Admin hanya menolak atau mengarsipkan; data dan riwayat tidak dihapus permanen.**
14. Lingkup final TitipTemu: fakultas tertentu, program studi, atau seluruh kampus? **Sudah diputuskan: seluruh lingkungan kampus.**
15. Apakah pengembalian boleh diajukan sebelum Admin memeriksa bukti? **Sudah diputuskan: User mengirim konfirmasi terlebih dahulu, lalu Admin memeriksa dan menyetujui atau menolak; tidak ada alur laporan barang temuan baru.**
