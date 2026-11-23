# Catatan Perubahan — Anggota 2

Dokumen ini mencatat setiap file yang diubah atau ditambahkan untuk pekerjaan Anggota 2
(P3 — Penjelajahan publik dan P4 — Pelaporan barang hilang).

## 2026-11-21 — Inisialisasi catatan

- Membuat file ini sebagai log perubahan khusus pekerjaan Anggota 2.
- Cakupan pekerjaan: halaman publik, daftar Barang Hilang/Ditemukan, detail,
  pencarian/filter/pagination, form laporan, validasi, upload foto, daftar laporan
  milik User, status laporan, dan pengujian terkait.
- Tidak mengubah alur autentikasi, verifikasi Admin, atau konfirmasi pengembalian
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

## 2026-11-21 — Anggota 3: P3.6, P4.5, P4.6

Sesuai permintaan pemilik, seluruh perubahan Anggota 3 dicatat pada berkas ini.

- P3.6: menambahkan `tests/Feature/PublicReportTest.php` untuk daftar dan detail publik,
  penolakan tipe `lost`/`found` yang tidak valid, penyembunyian status `pending`,
  `rejected`, `cancelled`, dan `archived`, batas tampil 30 hari pada Barang Ditemukan,
  pencarian/filter, serta pemeriksaan bahwa identitas pribadi pemilik tidak tampil.
- P4.5: menambahkan route `reports.edit`, `reports.update`, `reports.cancel`, dan
  `reports.resubmit`; `LostReportController` kini membatasi akses hanya untuk pemilik,
  mengizinkan ubah/batal pada `Pending`, memperbaiki dan mengirim ulang `Rejected`,
  serta menolak perubahan pada `Approved` dengan `403`. `UpdateLostReportRequest`
  memvalidasi input dan kepemilikan di sisi server. Foto lama dipertahankan jika tidak
  ada unggahan baru.
- P4.5: menambahkan `frontend/views/partials/lost-report-form.blade.php` yang dipakai
  bersama oleh `create.blade.php` dan `edit.blade.php`, serta menambahkan aksi Ubah,
  Batalkan, Kirim ulang, dan Detail pada `mine.blade.php` dengan label status Bahasa
  Indonesia. `frontend/css/app.css` mendapat warna badge untuk status tambahan.
- P4.6: menambahkan `tests/Feature/LostReportManagementTest.php` dan
  `database/factories/LostReportFactory.php` untuk validasi, unggahan foto, batas ukuran
  dan format, kepemilikan, pembatalan, pengiriman ulang, dan visibilitas laporan.
- `tasks.md` diperbarui: P3.6, P4.5, dan P4.6 ditandai selesai beserta catatan
  keputusan dan riwayat perubahan 1.8.

### Pemeriksaan

- `php -l` untuk seluruh berkas PHP baru/yang diubah: lulus.
- `git diff --check`: lulus; Git hanya memberi peringatan normal tentang konversi
  line ending Windows.
- `php artisan test`: 48 tes lulus, 192 assertion (sebelum perubahan 31 tes lulus).
- `npm install` berhasil pada percobaan kedua setelah `ECONNRESET` sementara;
  `npm run build` lulus dengan satu peringatan opsional tentang paket `fontaine`.
- Lingkungan lokal perlu ekstensi PHP `openssl`, `zip`, `gd`, `mbstring`, `fileinfo`,
  `pdo_sqlite`, dan `pdo_mysql`; untuk menjalankan tes digunakan pengaturan sementara
  lewat variabel `PHPRC` dan `APP_KEY` tanpa menulis berkas ke repositori.

## 2026-11-21 — Anggota 3: P5.2–P5.5

Sesuai instruksi pemilik, Tahap 5 dilanjutkan dari P5.2 sampai P5.5, lalu dihentikan
untuk konfirmasi. Tahap 6 belum dimulai.

### Perubahan

- P5.2: menambahkan `AdminReportController::show`, halaman
  `frontend/views/pages/admin/reports/show.blade.php`, rute `admin.reports.show`, dan
  tautan `Tinjau` pada dashboard Admin. Pratinjau foto memakai rute `admin.reports.photo`
  yang hanya dapat diakses Admin.
- P5.2: foto laporan baru dan foto pengganti kini disimpan pada disk privat `local`
  melalui `App\Support\ReportPhoto`. Rute publik `reports.photo` hanya menyajikan foto
  laporan yang memenuhi syarat publik. Rute foto memvalidasi path dari database dan
  menolak path traversal. Daftar dan detail publik kini memuat foto melalui rute tersebut.
- P5.2: aturan unggahan foto diselaraskan dengan P0.4: JPG/JPEG/PNG/WebP, maksimal 2 MB
  per file.
- P5.3: menambahkan `ModerateLostReportRequest`, `AdminReportController::moderate`, dan
  `App\Services\ReportModerationService`. Admin hanya dapat meverifikasi laporan `Pending`;
  penolakan wajib memiliki alasan minimal 5 karakter. Laporan yang ditolak dapat
  diperbaiki dan dikirim ulang oleh pemilik; alasan Admin tampil pada `Laporan Saya`.
- P5.4: menambahkan migration `report_status_histories`, model `ReportStatusHistory`,
  relasi `statusHistories`/`latestRejection` pada `LostReport`, serta tampilan riwayat
  pada detail Admin. Setiap keputusan mencatat Admin, waktu, status awal/akhir, alasan
  penolakan, dan catatan internal.
- P5.5: menambahkan `tests/Feature/AdminReportModerationTest.php`. Tes lama pada
  `LostReportsTest` dan `LostReportManagementTest` disesuaikan dengan disk privat.

### Pemeriksaan

- `php -l` untuk seluruh berkas PHP baru/yang diubah: lulus.
- `git diff --check`: lulus; Git hanya memberi peringatan normal tentang konversi
  line ending Windows.
- `vendor/bin/pint --test`: lulus setelah perapian format pada `LostReportController`
  dan `routes/web.php`.
- `php artisan test`: 61 tes lulus, 252 assertion (sebelumnya 48 tes).
- `npm run build`: lulus dengan satu peringatan opsional tentang paket `fontaine`.
- `php artisan route:list --path=admin`: empat rute Admin terdaftar.

### Catatan

- Foto lama yang sudah tersimpan pada disk `public` tidak dipindahkan pada tugas ini
  karena operasi data tersebut memerlukan keputusan terpisah. Foto baru memakai disk privat.
- Aturan P6 yang masih terbuka di `architecture.md` tidak diubah pada tugas ini;
  keputusan implementasinya dicatat pada bagian Tahap 6 berikut.

## 2026-11-21 — P6.1–P6.7

Sesuai instruksi pemilik, Tahap 6 dikerjakan dari P6.1 sampai P6.7, lalu dihentikan
untuk konfirmasi. Tahap 7 belum dimulai.

### Keputusan implementasi

- Aturan yang masih terbuka di `architecture.md:113-116` diisi dengan nilai konservatif:
  bukti JPG/JPEG/PNG/WebP, 1–3 file, maksimal 2 MB per file, disk privat `local`,
  tanggal pengembalian dicatat otomatis, catatan pemilik opsional, dan tanpa notifikasi
  status baru.
- Setelah tiga konfirmasi ditolak, pemilik memilih resolusi langsung oleh Admin: Admin
  menyetujui pengembalian dengan catatan wajib tanpa mengunggah bukti baru.

### Perubahan

- P6.1: menambahkan migration `return_confirmations`, model/factory `ReturnConfirmation`,
  `App\Support\ReturnProof`, dan `App\Services\ReturnConfirmationService`.
- P6.2: menambahkan `StoreReturnConfirmationRequest`, `ReturnConfirmationController`,
  rute `reports.return.create`/`reports.return.store`, dan
  `frontend/views/pages/reports/return/create.blade.php`. Form hanya untuk pemilik
  terverifikasi dengan laporan `Approved`, bukti wajib, dan pernyataan pengembalian.
- P6.3: menambahkan status `return_pending`. Laporan tetap tampil di Barang Hilang,
  belum tampil di Barang Ditemukan, dan mendapat badge tersendiri.
- P6.4: menambahkan `AdminReturnConfirmationController::proof`, galeri bukti pada detail
  Admin, serta filter/status `return_pending` dan `returned` pada dashboard Admin.
- P6.5: persetujuan Admin mengubah konfirmasi menjadi `approved`, laporan menjadi
  `returned`, mencatat `returned_at`, dan menampilkan laporan di Barang Ditemukan.
- P6.6: penolakan memerlukan alasan minimal 5 karakter, mengembalikan laporan ke
  `Approved`, dan membatasi pengiriman ulang sampai tiga kali. Setelah tiga penolakan,
  `ResolveReturnRequest` dan `ReturnConfirmationService::resolveByAdmin` memungkinkan
  Admin menyetujui dengan catatan wajib.
- P6.7: menambahkan `tests/Feature/ReturnConfirmationTest.php` dengan tujuh tes untuk
  kepemilikan, verifikasi, validasi unggahan, bukti privat, persetujuan, penolakan,
  batas percobaan, resolusi Admin, dan perubahan status.

### Pemeriksaan

- `php -l` untuk 88 berkas PHP: lulus.
- `git diff --check`: lulus; Git hanya memberi peringatan normal tentang konversi
  line ending Windows.
- `vendor/bin/pint --test`: lulus.
- `php artisan test`: 68 tes lulus, 334 assertion (sebelumnya 61 tes).
- `npm run build`: lulus dengan satu peringatan opsional tentang paket `fontaine`.
- `php artisan route:list --path=pengembalian`: enam rute konfirmasi pengembalian terdaftar.

### Catatan

- Setiap transisi status P6 mencatat `report_status_histories`; data dan bukti tidak dihapus.
- File P6 belum di-commit, sesuai aturan repo.

## 2026-11-21 — P7.1–P7.5

Sesuai instruksi pemilik, Tahap 7 dikerjakan dari P7.1 sampai P7.5, lalu dihentikan
untuk konfirmasi. Tahap 8 belum dimulai.

### Keputusan implementasi

- Batas arsip adalah `returned_at + 30 hari` pada zona waktu Asia/Makassar (WITA).
  Tepat pada batas tersebut laporan dianggap memenuhi syarat arsip; scope publik
  memakai batas `>` agar tidak ada jendela tampil setelah batas terlewat.
- Pengarsipan dijalankan harian pukul `00:05` WITA oleh scheduler Laravel dengan
  perlindungan `withoutOverlapping`.
- Riwayat arsip menggunakan `actor_id = NULL` dan ditampilkan sebagai “Sistem” karena
  proses ini bukan keputusan akun Admin tertentu.

### Perubahan

- P7.1: menambahkan konstanta `FOUND_DISPLAY_DAYS`, `archiveDueAt()`, dan
  `isArchiveDue()` pada `LostReport`; scope serta `isPublic()` diselaraskan dengan batas.
- P7.2: menambahkan `App\Services\ReportArchiveService`,
  `App\Console\Commands\ArchiveReturnedReports`, dan jadwal pada `routes/console.php`.
  README mendokumentasikan cron scheduler dan command uji.
- P7.3: proses memakai `lockForUpdate()`, filter status, dan `archived_at`; hanya
  laporan yang benar-benar lewat batas yang berubah. Riwayat `archive` ditulis pada
  `report_status_histories`, dan kegagalan dicatat dengan `Log::error`.
- P7.3: menambahkan migration
  `2026_11_21_160000_make_report_status_history_actor_nullable.php` agar aktor Sistem
  dapat dicatat tanpa akun palsu.
- P7.4: laporan `archived` tidak tampil pada daftar/detail/foto publik, tetapi baris
  laporan, konfirmasi, bukti, dan riwayat tetap tersimpan. Admin tetap dapat membuka
  detailnya melalui filter status `archived`.
- P7.5: menambahkan `tests/Feature/ReportArchiveTest.php` untuk batas 30 hari, zona
  waktu WITA, eksekusi berulang, visibilitas publik, keberadaan data, jadwal, dan log
  kegagalan.

### Pemeriksaan

- `php -l` untuk 92 berkas PHP: lulus.
- `git diff --check`: lulus; Git hanya memberi peringatan normal tentang konversi
  line ending Windows.
- `vendor/bin/pint --test`: lulus.
- `php artisan test`: 73 tes lulus, 365 assertion (sebelumnya 68 tes).
- `npm run build`: lulus dengan satu peringatan opsional tentang paket `fontaine`.
- `php artisan schedule:list`: lulus dengan `CACHE_STORE=array`; MySQL lokal tidak
  tersedia sehingga pemeriksaan CLI memakai cache array.

### Catatan

- File P7 belum di-commit, sesuai aturan repo.
- Tidak ada data yang dihapus; hanya status, `archived_at`, dan riwayat yang ditambahkan.
