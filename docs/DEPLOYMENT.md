# Panduan Deployment TitipTemu

Dokumen ini menjelaskan penyiapan lingkungan produksi untuk aplikasi TitipTemu. Tidak ada kredensial, token, atau nilai rahasia yang boleh ditulis ke repositori. Seluruh perintah dijalankan dari direktori yang disebutkan.

## 1. Prasyarat

- PHP 8.3 atau lebih baru dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `gd`, `zip`, dan `pdo_sqlite` hanya untuk pengujian.
- MySQL 8 atau lebih baru.
- Composer 2.
- Node.js 20.19 atau lebih baru dan npm untuk membangun aset.
- Web server dengan document root **`backend/public`**. Direktori `backend/` dan `frontend/` tidak boleh diekspos langsung.
- HTTPS aktif. Gunakan pengaturan web server/proxy untuk mengalihkan HTTP ke HTTPS.

## 2. Variabel environment

Salin `backend/.env.example` menjadi `backend/.env`, lalu isi nilai untuk lingkungan produksi. Nama variabel penting:

| Variabel | Catatan produksi |
|---|---|
| `APP_NAME` | Nama aplikasi. |
| `APP_ENV` | `production`. |
| `APP_KEY` | Hasil `php artisan key:generate`; jangan dibagikan atau di-commit. |
| `APP_DEBUG` | `false`. |
| `APP_URL` | URL HTTPS publik aplikasi. |
| `APP_TIMEZONE` | `Asia/Makassar` sesuai aturan 30 hari. |
| `APP_LOCALE` | `id`. |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Koneksi MySQL produksi. Berikan hanya hak yang diperlukan aplikasi. |
| `SESSION_DRIVER` | `database` sesuai migrasi bawaan. Pastikan tabel `sessions` ada. |
| `SESSION_SECURE_COOKIE` | `true` pada HTTPS. Nilai awal produksi juga aman bila tidak ditulis, tetapi tetapkan eksplisit. |
| `CACHE_STORE` | `database`; tabel `cache` dipakai juga oleh scheduler locking. |
| `QUEUE_CONNECTION` | `database` pada contoh. Alur MVP tidak memerlukan worker terpisah untuk pekerjaan rutin. |
| `FILESYSTEM_DISK` | `local` untuk penyimpanan privat. |
| `ADMIN_LOGIN_URI` | Jalur login Admin yang tidak ditautkan di antarmuka publik. Simpan sebagai konfigurasi rahasia operasional. |
| `MAIL_*` | Tidak dipakai untuk verifikasi email karena verifikasi email dinonaktifkan. |

Jangan commit `backend/.env`. Pastikan file tersebut tidak ikut terarsip dalam backup publik atau direktori web.

## 3. Langkah pemasangan

Dari root repositori:

```bash
cd backend
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

Isi `.env` sesuai tabel di atas, lalu:

```bash
php artisan migrate --force
php artisan config:cache
```

Bangun aset dari direktori `frontend`:

```bash
cd frontend
npm ci
npm run build
```

Pastikan `backend/public/build/manifest.json` ada. Jika berkas `backend/public/hot` masih ada dari sesi pengembangan, hapus agar Laravel memakai aset hasil build, bukan dev server.

Beri akses tulis hanya pada direktori yang memerlukannya:

```bash
backend/storage/
backend/bootstrap/cache/
```

Gunakan pemilik dan izin web server yang sesuai. Hindari `chmod 777`.

## 4. HTTPS dan session

- Pasang sertifikat HTTPS pada web server atau reverse proxy.
- Setelah HTTPS aktif, tetapkan `SESSION_SECURE_COOKIE=true`.
- Jika aplikasi berada di belakang proxy, sesuaikan konfigurasi trust proxy di lapisan web server/proxy sesuai lingkungan; jangan menaruh alamat IP atau rahasia infrastruktur di repositori.
- Jalur Admin berasal dari `ADMIN_LOGIN_URI`. Jangan menautkannya dari navbar, footer, atau halaman publik lain.

## 5. Penjadwalan arsip 30 hari

Dua command terdaftar pada scheduler Laravel dan berjalan setiap hari pukul `00:05` zona waktu Asia/Makassar:

- `php artisan reports:archive-returned` untuk laporan temuan.
- `php artisan missing-reports:archive-found` untuk laporan kehilangan.

Verifikasi dari `backend/`:

```bash
php artisan schedule:list
```

Agar scheduler berjalan, server memerlukan satu entri cron:

```bash
* * * * * cd /path/ke/backend && php artisan schedule:run >> /dev/null 2>&1
```

Entri cron tersebut merupakan konfigurasi server dan harus dipasang oleh pengelola deployment. Repositori hanya mendefinisikan jadwal aplikasi; tidak mengubah cron server. Keduanya memakai `withoutOverlapping()`. Perhitungan 30 hari dilakukan aplikasi dari `found_at` untuk laporan kehilangan dan dari persetujuan Admin untuk laporan temuan; cron hanya memicu proses.

Untuk menjalankan sekali secara sengaja:

```bash
cd backend
php artisan reports:archive-returned
php artisan missing-reports:archive-found
```

Keduanya idempoten dan hanya mengubah status yang sudah lewat batas. Pengarsipan tidak menghapus data laporan.

## 6. Data demo

`php artisan db:seed` hanya untuk pengembangan dan pengujian. Seeder membuat akun User dan Admin fiktif dengan password pengembangan seperti yang tercatat di README.

Sebelum produksi, pastikan basis data tidak berisi akun, laporan, atau bukti demo. Jika data demo sudah terlanjur ada, gunakan prosedur penghapusan yang disetujui pemilik atau siapkan basis data produksi bersih sebelum migrasi. Agent tidak menghapus data tanpa persetujuan eksplisit.

## 7. Pemeriksaan setelah deployment

Dari `backend/`:

```bash
php artisan about
php artisan migrate:status
php artisan route:list
php artisan schedule:list
```

Pemeriksaan HTTP:

- `/`, `/barang/hilang`, `/barang/ditemukan`, `/panduan`, `/login`, dan `/register` mengembalikan `200`.
- `/barang/lost` dan `/barang/found` mengarahkan `301` ke URI kanonik.
- `ADMIN_LOGIN_URI` dapat dibuka langsung oleh Admin, tetapi tidak muncul di halaman publik.
- Foto laporan, foto profil, dan bukti privat tidak dapat diakses langsung sebagai berkas publik.
- Tombol WhatsApp pada detail publik mengarah ke `wa.me` dan tidak menampilkan nomor HP sebagai teks.

Jika ada perubahan konfigurasi setelah `config:cache`, jalankan `php artisan config:clear` lalu `php artisan config:cache` kembali. Untuk perubahan view, jalankan `php artisan view:clear`.

## 8. Cadangan dan pemulihan

Cadangkan secara berkala dan simpan di lokasi terpisah dari web root:

- Basis data MySQL.
- Direktori penyimpanan privat yang berisi foto laporan, foto profil, dan bukti pengembalian.
- File `.env` dan konfigurasi server, dengan akses terbatas dan tidak masuk Git.

Penghapusan permanen oleh Admin tidak dapat dibatalkan oleh aplikasi, oleh karena itu cadangan harus tersedia sebelum tindakan tersebut dilakukan.

## 9. Batasan yang belum diatur oleh repositori

- Cron server `schedule:run` belum dipasang oleh agent karena merupakan konfigurasi infrastruktur.
- Tidak ada integrasi SSO, WhatsApp API berbayar, Redis, atau layanan eksternal lain.
- Target waktu muat pada PRD belum diukur dengan alat produksi; perlu pengujian tersendiri bila dibutuhkan.
