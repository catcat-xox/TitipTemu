
# TitipTemu

Website Informasi Barang Hilang dan Konfirmasi Pengembalian Barang.

## Tentang Proyek

TitipTemu merupakan website untuk seluruh lingkungan kampus. Mahasiswa, dosen, dan staf aktif dapat membuat laporan kehilangan, melihat informasi publik, serta mengirim konfirmasi apabila barang telah kembali.

Laporan dan konfirmasi akan diperiksa oleh Admin sebelum status dan publikasinya diperbarui.

## Tujuan

- Memusatkan informasi laporan barang hilang.
- Memudahkan pengguna membuat laporan dengan foto.
- Menyediakan pencarian informasi barang.
- Menyediakan moderasi laporan oleh Admin.
- Mendokumentasikan konfirmasi pengembalian barang.

## Fitur Utama

- Halaman publik Barang Hilang dan Barang Ditemukan.
- Registrasi dan login User.
- Login Admin.
- Form laporan kehilangan dengan foto wajib.
- Riwayat laporan User.
- Verifikasi laporan oleh Admin.
- Konfirmasi barang telah kembali dengan foto bukti.
- Pengarsipan publik Barang Ditemukan setelah 30 hari.

## Teknologi

- Laravel
- PHP
- MySQL
- HTML, CSS, JavaScript
- Bootstrap
- Git dan GitHub

## Persiapan Pengembangan

Prasyarat:

- PHP 8.3 atau lebih baru dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, dan `tokenizer`.
  - Fedora: `sudo dnf install php-mysqlnd`
  - Debian/Ubuntu: `sudo apt install php-mysql`
- Composer 2.
- MySQL 8 atau lebih baru.
- Node.js 20.19 atau lebih baru (disarankan 22.12+).
- npm.

### 1. Siapkan database MySQL

Jalankan sebagai pengguna MySQL yang memiliki izin membuat database dan user:

```sql
CREATE DATABASE titiptemu
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER 'titiptemu'@'localhost' IDENTIFIED BY 'ganti-password-lokal';

GRANT ALL PRIVILEGES ON titiptemu.* TO 'titiptemu'@'localhost';

FLUSH PRIVILEGES;
```

Gunakan password lokal yang kuat. Jangan menuliskan password nyata di dokumentasi atau commit.

### 2. Siapkan backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Perintah setup backend otomatis juga tersedia:

```bash
cd backend
composer setup
```

Script tersebut memasang dependensi, membuat `.env` jika belum ada, dan membuat application key. Script tersebut tidak menjalankan migration agar pengembang dapat memeriksa koneksi database terlebih dahulu.

Sesuaikan koneksi database di `backend/.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=titiptemu
DB_USERNAME=titiptemu
DB_PASSWORD=ganti-password-lokal
```

Jalankan migrasi:

```bash
php artisan migrate
```

Isi data demo fiktif:

```bash
php artisan db:seed
```

Seeder dapat dijalankan ulang tanpa menggandakan User demo.

`backend/.env` diabaikan Git. Jangan commit kredensial atau file environment lokal.

### 3. Siapkan frontend

```bash
cd frontend
npm install
```

### 4. Jalankan aplikasi

Gunakan dua terminal.

Terminal backend:

```bash
cd backend
php artisan serve
```

Terminal frontend:

```bash
cd frontend
npm run dev
```

Aplikasi dapat dibuka di `http://localhost:8000`.

Untuk aset produksi:

```bash
cd frontend
npm run build
```

Hasil build ditulis ke `backend/public/build`.

### 5. Perintah pengembang umum

Semua perintah Laravel dijalankan dari `backend/`:

```bash
php artisan about
php artisan migrate:status
php artisan route:list
php artisan config:clear
php artisan view:clear
```

Pengarsipan otomatis Barang Ditemukan dijalankan oleh scheduler Laravel pada `00:05`
zona waktu Asia/Makassar (WITA). Aktifkan scheduler deployment dengan entri cron:

```bash
* * * * * cd /path/ke/backend && php artisan schedule:run >> /dev/null 2>&1
```

Untuk menjalankan atau menguji proses tanpa menunggu jadwal:

```bash
php artisan reports:archive-returned
php artisan schedule:list
```

Perintah Composer:

```bash
composer install
composer update
composer test
```

Jangan menjalankan `composer update` tanpa meninjau perubahan `composer.lock`.

Perintah frontend dijalankan dari `frontend/`:

```bash
npm install
npm run dev
npm run build
```

### 6. Jalankan pengujian

```bash
cd backend
php artisan test
```

Atau gunakan script Composer:

```bash
cd backend
composer test
```

Jalankan satu berkas atau satu tes:

```bash
php artisan test tests/Feature/HomePageTest.php
php artisan test --filter=home_page
```

Pengujian memakai SQLite in-memory dan tidak mengubah database pengembangan MySQL. Aset Vite dinonaktifkan otomatis selama tes, jadi `npm run build` tidak diperlukan sebelum menjalankan tes.

## Peran Pengguna

- Guest: melihat informasi publik.
- User (mahasiswa, dosen, atau staf aktif): membuat laporan dan konfirmasi pengembalian.
- Admin: memoderasi laporan dan konfirmasi.

## Struktur Repository

- `backend/` — aplikasi Laravel: controller, model, migration, konfigurasi, public, dan tes.
- `frontend/` — tampilan Blade, CSS, JavaScript, gambar, dan konfigurasi Vite.
- `docs/` — dokumentasi proyek.

## Struktur Dokumentasi

- `docs/project_brief.md`
- `docs/prd.md`
- `docs/requirement.md`

## Lisensi

Proyek ini direncanakan menggunakan MIT License.
Lihat file `LICENSE` untuk ketentuan lisensi.

## Status Pengembangan

Tahap 1 — Fondasi.
