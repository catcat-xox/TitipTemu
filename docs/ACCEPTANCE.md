# Checklist Penerimaan MVP TitipTemu

Dokumen ini memetakan kriteria PRD v2.11 dan Requirement v2.11 ke implementasi serta bukti pengujian. Baseline aktif adalah A18/A28: **Barang Hilang = laporan kehilangan pemilik**, **Barang Ditemukan = laporan temuan penemu**; konfirmasi “Barang ditemukan” pada laporan kehilangan langsung oleh pemilik, dengan Admin tetap memverifikasi publikasi awal.

## 1. Fitur publik

| Kriteria | Referensi | Implementasi | Bukti |
|---|---|---|---|
| Beranda tanpa login | FR-01 | `PublicReportController::home()` dengan empat laporan terbaru dari kedua kanal. | `HomePageTest`, `PublicReportTest` |
| Dua daftar dengan dua kartu status | FR-02, FR-25, FR-47, US-01 | `/barang/hilang` dan `/barang/ditemukan`; kartu status tetap dalam jenis aktif; URI lama dialihkan 301. | `PublicReportTest`, `PublicUriTest` |
| Detail publik hanya untuk laporan yang boleh ditayangkan | FR-03 | Scope `isPublic()` pada kedua model; status nonpublik ditolak 404. | `PublicReportTest` |
| Pencarian dan filter dalam menu aktif | FR-04, FR-05 | `PublicReportController::index()` memakai `type`, `status`, `category`, `date`, `location`, dan `q`. | `PublicReportTest` |
| WhatsApp tanpa eksposur nomor | FR-35, BR-06, NFR-13 | `WhatsAppLink` dibuat server-side; nomor tidak dirender sebagai teks HTML. | `WhatsAppContactTest`, `PublicReportTest` |

## 2. Akun, peran, dan otorisasi

| Kriteria | Referensi | Implementasi | Bukti |
|---|---|---|---|
| Registrasi dan login User | FR-06, FR-34 | `AuthController`, Form Request, hashing, regenerasi sesi. | `AuthenticationTest` |
| Login Admin terpisah dan tersembunyi | FR-07, BR-21 | Tabel `admins`, guard `admin`, `ADMIN_LOGIN_URI`, layout `admin-auth`. | `AdminAuthenticationTest`, `LayoutSeparationTest` |
| Pemisahan Guest/User/Admin | FR-08, FR-39, NFR-04 | Middleware `role:guest|user|admin`, Gate, `main-site`. | `UserAuthorizationTest`, `LayoutSeparationTest`, `SecurityHardeningTest` |
| User hanya mengelola laporan sendiri | FR-17, NFR-05 | Form Request dan service memeriksa `user_id` pada kedua jenis laporan. | `LostReportManagementTest`, `MissingReportManagementTest`, `ReturnConfirmationTest` |
| Rate limit autentikasi | P8.1, ARCHITECTURE §9 | Limiter `throttle:auth` dengan batas 10/menit per IP dan email pada login User, registrasi, dan login Admin. | `SecurityHardeningTest` |
| Akun User lama ber-role admin tidak masuk Admin | FR-39, RULES §5 | Guard dan provider Admin terpisah; middleware peran memakai `isUser()`/`isAdmin()`. | `UserAuthorizationTest`, `SecurityHardeningTest` |

## 3. Pelaporan dua kanal

| Kriteria | Referensi | Implementasi | Bukti |
|---|---|---|---|
| Tabel terpisah tanpa penautan | FR-09, FR-46, BR-22 | `lost_reports` untuk temuan dan `missing_reports` untuk kehilangan. | `MissingReportManagementTest`, `AdminDualReportFlowTest` |
| Form dengan no. HP, tanggal, lokasi, dan foto wajib | FR-10, FR-32 | `StoreLostReportRequest`, `StoreMissingReportRequest`, partial form bersama. | `LostReportsTest`, `MissingReportManagementTest` |
| Validasi file dan input | FR-11, FR-31, NFR-03, NFR-06 | Satu foto JPG/JPEG/PNG/WebP maksimal 2 MB, lima kategori tetap, pesan inline. | `LostReportManagementTest`, `MissingReportManagementTest` |
| Laporan baru menunggu verifikasi | FR-12 | Status awal `pending`; tidak masuk scope publik. | `PublicReportTest`, `LostReportsTest` |
| Persetujuan ketentuan wajib | FR-32 | Checkbox `terms_accepted` dan `terms_accepted_at`. | `LostReportsTest`, `MissingReportManagementTest` |

## 4. Verifikasi dan pengelolaan Admin

| Kriteria | Referensi | Implementasi | Bukti |
|---|---|---|---|
| Antrean laporan dan keputusan beralasan | FR-13, FR-14, FR-23 | Dashboard filter `jenis`/`status`, moderasi, alasan penolakan wajib. | `AdminReportModerationTest`, `AdminDualReportFlowTest` |
| Edit laporan sesuai aturan A28 | FR-36 | Temuan hanya `pending`; kehilangan semua status; riwayat mencatat status asal. | `AdminReportEditDeleteTest` |
| Cari lintas jenis dan status | FR-40, FR-43–FR-45 | `AdminReportController::search()` dan sidebar 5 menu. | `AdminDualReportFlowTest`, `InterfaceRefinementTest` |
| Penghapusan permanen terbatas | FR-38 | Temuan hanya `returned`/`archived`; kehilangan semua status; berkas dan riwayat ikut dibersihkan. | `AdminReportEditDeleteTest` |
| Riwayat status | FR-29, NFR-07, NFR-14 | `report_status_histories` dan `missing_report_status_histories`. | `ReportArchiveTest`, `AdminDualReportFlowTest` |

## 5. Transisi status “Sudah”

| Kriteria | Referensi | Implementasi | Bukti |
|---|---|---|---|
| Pemilik menandai laporan kehilangan langsung | FR-18, FR-20, BR-10, BR-27 | `MissingReportOwnerService::markFound()`: `approved → found`, `found_at`, riwayat `found_by_owner`, tanpa bukti/Admin. | `MissingReportManagementTest` |
| Pembatalan laporan kehilangan | FR-48, BR-30 | `pending`/`approved → cancelled` oleh pemilik. | `MissingReportManagementTest` |
| Konfirmasi laporan temuan dengan bukti privat | FR-19, FR-33, FR-37 | Nama pemilik, 1–3 foto, pernyataan, alasan penolakan, maksimal 3 percobaan. | `ReturnConfirmationTest` |
| Admin memeriksa konfirmasi temuan | FR-21, FR-22 | Antrean dan galeri bukti privat; tidak ada antrean konfirmasi kehilangan. | `ReturnConfirmationTest`, `AdminDualReportFlowTest` |
| Perpindahan kartu sesuai menu | FR-24, FR-25 | Kehilangan ke **Barang Sudah Ditemukan**; temuan ke **Barang Sudah Kembali**. | `PublicReportTest`, `ReturnConfirmationTest` |

## 6. Privasi, retensi, dan keamanan

| Kriteria | Referensi | Implementasi | Bukti |
|---|---|---|---|
| Data pribadi dan foto bukti tidak publik | FR-28, FR-37, NFR-07 | Penyimpanan privat, endpoint dengan pengecekan akses, header anti-cache, validasi path. | `ProfilePhotoTest`, `ReturnConfirmationTest`, `SecurityHardeningTest` |
| Arsip otomatis 30 hari | FR-26, FR-27, FR-30, BR-12 | Dua command scheduler harian 00:05 WITA; perhitungan dari `found_at`/persetujuan Admin. | `ReportArchiveTest`, `MissingReportArchiveCommandTest` |
| Arsip bukan penghapusan data | BR-13, BR-15 | Hanya status dan `archived_at` yang berubah; data, bukti, dan riwayat tetap ada. | `ReportArchiveTest` |
| CSRF dan validasi server | NFR-03, ARCHITECTURE §9 | `@csrf`, Form Request, middleware web. | Suite feature seluruhnya |
| Hash kata sandi | NFR-02 | `Hash::make()` pada registrasi dan seeder. | `AuthenticationTest` |
| Tidak ada dialog browser bawaan | RULES §2 | Satu modal konfirmasi Bootstrap dipakai bersama. | `ConfirmModalTest` |

## 7. Kualitas, dokumentasi, dan rilis

| Kriteria | Referensi | Implementasi | Bukti |
|---|---|---|---|
| Responsif dan konsisten | NFR-01, NFR-09, RULES §2 | Token desain, Bootstrap 5, tabel responsif, target sentuh mobile, teks Bahasa Indonesia. | `InterfaceRefinementTest`, tinjauan P8.2 |
| Aksesibilitas dasar | RULES §2 | Label, `aria-invalid`/`aria-describedby`, skip link dengan `tabindex="-1"`, heading tunggal, fokus terlihat. | Tinjauan P8.2 |
| Struktur dapat dipelihara | NFR-10, NFR-11 | Controller fokus, service domain, partial Blade, dokumentasi arsitektur. | `ARCHITECTURE.md`, `RULES.md` |
| Prosedur backup dan deployment | NFR-08, NFR-11 | `docs/DEPLOYMENT.md`, panduan User/Admin, daftar lisensi. | Dokumentasi P8.5–P8.7 |
| Pemeriksaan otomatis | RULES §8 | `php artisan test`, Pint, `npm run build`, `route:list`, `schedule:list`, `git diff --check`. | Lihat hasil di bawah |

## 8. Hasil verifikasi aktual

Pemeriksaan dijalankan dari `backend/` dan `frontend/` pada 2026-11-23 setelah seluruh perubahan P8:

| Pemeriksaan | Hasil aktual |
|---|---|
| `php artisan test` | Lulus: 181 tes, 1521 assertion. Termasuk 5 tes/40 assertion pada `SecurityHardeningTest`. Satu regresi copy ditemukan pada putaran pertama (teks “Alasan Admin:” pada empat view), diperbaiki menjadi “Alasan tim kami:”, lalu suite dijalankan ulang hingga lulus. |
| `./vendor/bin/pint --test` | Lulus, tidak ada pelanggaran format. |
| `npm run build` | Lulus; 61 modules transformed. Peringatan opsional `fontaine` tetap muncul karena paket fallback font opsional tidak dipasang; tidak menambah dependensi sesuai batasan P8. |
| `php artisan route:list` | Lulus; 63 rute terdaftar, termasuk redirect 301 legacy dan tiga endpoint autentikasi dengan `throttle:auth`. |
| `php artisan schedule:list` | Lulus; `reports:archive-returned` dan `missing-reports:archive-found` terjadwal harian pukul `00:05`. |
| `git diff --check` | Tidak ada whitespace error; hanya notifikasi konversi CRLF/LF pada file yang disunting. |

Rincian perubahan dan status ada pada `docs/TASKS.md` riwayat 1.40. Tidak ada migrasi, penghapusan data, perubahan dependensi, atau perubahan konfigurasi server yang dilakukan.

## 9. Status dan catatan residual

- **Status fungsional:** kriteria MVP A18/A28 telah diimplementasikan dan diuji.
- **Infrastruktur:** entri cron `schedule:run` harus dipasang oleh pengelola server agar status arsip dalam basis data berubah otomatis. Ini bukan perubahan kode dan belum dilakukan agent.
- **Kinerja:** target waktu muat PRD belum diukur pada lingkungan produksi; diperlukan pengujian tersendiri bila menjadi syarat rilis.
- **Keputusan terbuka:** notifikasi status dan SSO masih di luar MVP menurut dokumen proyek; tidak ditambahkan dalam P8.
- **Data historis:** catatan lama sebelum A18/A28 tetap ada sebagai arsip keputusan dan ditandai sudah digantikan, bukan aturan aktif.
