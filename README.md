
# TitipTemu

Portal barang temuan kampus: pelaporan oleh User, pencarian oleh Guest, dan verifikasi oleh Admin.

## Tentang Proyek

TitipTemu merupakan website untuk seluruh lingkungan kampus. User (mahasiswa, dosen, atau staf aktif) melaporkan barang yang ditemukan dan mengirim konfirmasi pengembalian. Guest mencari barang tanpa akun. Admin menggunakan akun terpisah dan jalur login tersembunyi untuk memeriksa laporan serta konfirmasi sebelum status dan publikasinya diperbarui.

Laporan yang disetujui tampil di **Barang Hilang**. Setelah pengembalian disetujui Admin, laporan berpindah ke **Barang Ditemukan** selama 30 hari, lalu diarsipkan otomatis tanpa menghapus data.

## Tujuan

- Memusatkan laporan barang temuan yang sudah diverifikasi Admin.
- Memudahkan User melaporkan barang temuan lengkap dengan data penemu dan foto.
- Menyediakan pencarian publik untuk Guest tanpa akun.
- Menghubungkan Guest dengan penemu lewat WhatsApp tanpa menampilkan nomor HP di halaman publik.
- Menyediakan verifikasi laporan dan konfirmasi pengembalian oleh Admin.
- Menjaga privasi nama pemilik dan foto bersama penemu–pemilik.

## Fitur Utama

- Halaman publik Barang Hilang dan Barang Ditemukan, lengkap dengan pencarian.
- Registrasi dan login User tanpa verifikasi email; menu **Akun** dimulai dari kartu profil dan berisi aktivitas serta laporan User.
- Login Admin terpisah melalui jalur tersembunyi dari `ADMIN_LOGIN_URI`.
- Dashboard Admin dengan filter status dan pencarian laporan lintas status melalui menu **Cari laporan**.
- Form **Laporkan Barang Temuan** dengan no. HP penemu dan foto wajib.
- Tombol WhatsApp pada detail publik tanpa menampilkan nomor penemu.
- Verifikasi, penolakan, dan edit Admin hanya untuk laporan `Pending`.
- Konfirmasi pengembalian dengan nama pemilik dan foto bersama penemu–pemilik.
- Pengarsipan otomatis Barang Ditemukan setelah 30 hari dan penghapusan permanen manual oleh Admin untuk laporan `returned`/`archived`.
- Notifikasi mengambang di bawah navbar untuk setiap hasil aksi, hilang otomatis setelah 3 detik.

## Teknologi

- Laravel
- PHP
- MySQL
- HTML, CSS, JavaScript
- Bootstrap
- Git dan GitHub

## Persiapan Pengembangan

Prasyarat:

- PHP 8.3 atau lebih baru dengan ekstensi `pdo_mysql`, `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `gd`, dan `zip`.
  - `pdo_mysql` dipakai aplikasi dengan MySQL.
  - `pdo_sqlite` dan `sqlite3` dipakai pengujian dengan database SQLite in-memory.
  - `gd` dipakai saat pengujian membuat berkas gambar; `zip` dipakai Composer saat memasang dependensi.
  - Fedora: `sudo dnf install php-mysqlnd php-pdo php-sqlite3 php-gd php-zip`
  - Debian/Ubuntu: `sudo apt install php-mysql php-sqlite3 php-gd php-zip`
  - Windows/XAMPP: pastikan baris `extension=pdo_sqlite`, `extension=sqlite3`, `extension=gd`, dan `extension=zip` tidak diberi awalan `;` pada `php.ini` yang dimuat PHP. Restart Apache jika aplikasi dijalankan melalui Apache.
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

# Jalur login Admin; tidak ditautkan di navigasi publik.
ADMIN_LOGIN_URI=/admin-login
```

Ganti nilai `ADMIN_LOGIN_URI` dengan jalur lain jika diperlukan. Jangan menautkan jalur ini dari navbar, footer, atau halaman publik lainnya.

Jalankan migrasi:

```bash
php artisan migrate
```

Isi data demo fiktif:

```bash
php artisan db:seed
```

Seeder dapat dijalankan ulang tanpa menggandakan User atau Admin demo. Seeder membuat:

- User demo: `demo.user@titiptemu.test`
- Admin demo: `admin@titiptemu.test` di tabel `admins`
- Jika akun User bernama `wandi saputra` sudah terdaftar, `WandiStatusDemoSeeder` menambahkan 6 laporan fiktif untuk masing-masing status (`pending`, `approved`, `return_pending`, `rejected`, `returned`, `archived`, `cancelled`) dengan riwayat dan bukti pengembalian. Seeder ini dilewati tanpa error bila akun tersebut belum ada. Jalankan sendiri dengan `php artisan db:seed --class=WandiStatusDemoSeeder`.

Keduanya memakai password demo `password` hanya untuk pengembangan. Ganti password sebelum dipakai di lingkungan bersama. Sistem mendukung beberapa akun Admin; tambahkan Admin lain melalui `App\Models\Admin::create()` atau mekanisme internal yang disetujui, bukan dengan menaikkan role akun User.

Admin masuk lewat `ADMIN_LOGIN_URI`, bukan lewat `/login` dan bukan lewat tautan navigasi.

`backend/.env` diabaikan Git. Jangan commit kredensial atau file environment lokal.

### 3. Siapkan frontend

```bash
cd frontend
npm install
```

### 4. Jalankan aplikasi

Untuk menjalankan aplikasi dengan aset yang sudah dibangun, cukup satu terminal:

```bash
cd backend
php artisan serve
```

Aplikasi dapat dibuka di `http://localhost:8000`. Hasil build berada di `backend/public/build` dan dipakai secara default.

Jika ingin hot reload saat mengerjakan CSS/JS, jalankan juga:

```bash
cd frontend
npm run dev
```

Saat `npm run dev` dimatikan, hapus `backend/public/hot` agar Laravel kembali memakai aset hasil build. Untuk memperbarui aset produksi:

```bash
cd frontend
npm run build
```

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

### 7. Masalah umum saat menjalankan

- `could not find driver (Connection: sqlite ...)` ketika menjalankan `php artisan test`: aktifkan `extension=pdo_sqlite` dan `extension=sqlite3` pada `php.ini` yang dibaca PHP CLI, lalu cek dengan `php -m`.
- `GD extension is not installed` ketika menjalankan tes unggah foto: aktifkan `extension=gd` pada `php.ini` yang sama.
- `Vite manifest not found` ketika membuka halaman: jalankan `npm run dev` bersamaan dengan `php artisan serve`, atau jalankan `npm run build` sekali sebelum `php artisan serve`.
- Tampilan hanya berupa struktur HTML tanpa CSS/JS setelah `npm run dev` dimatikan: hapus `backend/public/hot` agar Laravel memakai aset hasil `npm run build`, atau hidupkan kembali `npm run dev` jika ingin memakai hot reload.
- `SQLSTATE[42S02] ... performance_schema.session_status` pada perintah diagnostik seperti `php artisan db:show` dapat muncul di sebagian instalasi XAMPP. Perintah tersebut opsional dan tidak menghalangi migrasi, pengujian, atau aplikasi.

## Peran Pengguna

- **Guest**: bukan akun database; melihat dan mencari Barang Hilang/Barang Ditemukan serta membuka detail publik.
- **User**: akun terdaftar; melaporkan barang temuan, memantau status lewat menu **Akun**, dan mengirim konfirmasi pengembalian. No. HP Akun wajib dan privat; diisi saat daftar atau diubah pada biodata **Akun**.
- **Admin**: akun terpisah di tabel `admins`; masuk lewat jalur tersembunyi, memeriksa laporan, menyetujui/menolak, mengedit laporan `Pending`, memeriksa bukti privat, serta menghapus permanen laporan yang sudah `returned`/`archived`.

## Struktur Repository

- `backend/` — aplikasi Laravel: controller, model, migration, konfigurasi, public, dan tes.
- `frontend/` — tampilan Blade, CSS, JavaScript, gambar, dan konfigurasi Vite.
- `docs/` — dokumentasi proyek.

## Struktur Dokumentasi

- `docs/project_brief.md`
- `docs/prd.md`
- `docs/requirement.md`
- `docs/ARCHITECTURE.md`
- `docs/RULES.md`
- `docs/TASKS.md`
- `docs/CHANGELOG_ANGGOTA_2.md` (log historis pekerjaan Anggota 2)

## Lisensi

Proyek ini direncanakan menggunakan MIT License.
Lihat file `LICENSE` untuk ketentuan lisensi.

## Status Pengembangan

Fondasi dan alur utama tersedia: pelaporan barang temuan oleh User, pencarian publik oleh Guest, kontak WhatsApp tanpa eksposur nomor, verifikasi Admin terpisah, konfirmasi pengembalian privat, serta retensi 30 hari dan penghapusan permanen manual.
