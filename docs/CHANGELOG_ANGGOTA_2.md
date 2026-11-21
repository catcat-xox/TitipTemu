# Catatan Perubahan — Anggota 2

Dokumen ini mencatat setiap file yang diubah atau ditambahkan untuk pekerjaan Anggota 2
(P3 — Penjelajahan publik dan P4 — Pelaporan barang hilang).

## 2026-11-21 — Inisialisasi catatan

- Membuat file ini sebagai log perubahan khusus pekerjaan Anggota 2.
- Cakupan pekerjaan: halaman publik, daftar Barang Hilang/Ditemukan, detail,
  pencarian/filter/pagination, form laporan, validasi, upload foto, daftar laporan
  milik User, status laporan, dan pengujian terkait.
- Tidak mengubah alur autentikasi, moderasi Admin, atau konfirmasi pengembalian
  karena merupakan cakupan anggota/tahap lain.

## 2026-11-21 — Implementasi P3/P4

- Menambahkan migration `lost_reports` untuk data laporan, status, foto, persetujuan
  ketentuan, dan timestamp pengembalian/arsip.
- Menambahkan model `LostReport`, relasi laporan pada `User`, serta scope visibilitas
  publik untuk Barang Hilang dan Barang Ditemukan.
- Menambahkan `PublicReportController` untuk daftar publik, pencarian, filter kategori
  dan tanggal, pagination, serta detail yang hanya menampilkan laporan publik.
- Menambahkan `LostReportController` dan `StoreLostReportRequest` untuk form, validasi
  server-side, upload foto JPG/JPEG/PNG maksimal 5 MB, status awal `pending`, dan
  daftar laporan milik User.
- Menambahkan route publik, route form User, dan halaman Panduan.
- Menambahkan view Blade untuk daftar, detail, form laporan, daftar laporan User, dan
  Panduan.
- Memperbarui navigasi header/footer agar memakai route publik baru dan menampilkan
  tombol buat laporan bagi User yang sudah login.
- Menambahkan styling halaman publik, kartu laporan, filter, detail, form, badge status,
  dan empty state pada `frontend/css/app.css`.
- Menambahkan `tests/Feature/LostReportsTest.php` untuk visibilitas publik, status
  `pending`, submit laporan User, upload foto, serta validasi foto/persetujuan.

### Pemeriksaan

- `php -l` untuk seluruh file PHP baru/yang diubah: lulus.
- `git diff --check`: lulus; Git hanya memberi peringatan normal tentang konversi
  line ending Windows.
- `npm run build`: lulus. Vite memberi peringatan opsional tentang paket `fontaine`;
  build tetap berhasil.
- `php artisan test`: belum dapat dijalankan karena `composer install` masih
  terblokir verifikasi sertifikat TLS lokal (`curl error 60`), sehingga
  `backend/vendor/autoload.php` belum tersedia.
