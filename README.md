
# TitipTemu

Portal barang hilang & temuan kampus: pelaporan kehilangan oleh pemilik, pelaporan temuan oleh penemu, pencarian oleh Guest, dan verifikasi oleh Admin.

> **Catatan A18–A25 (2026-11-22):** baseline produk yang disetujui memakai **dua kanal laporan**: **Barang Hilang = laporan kehilangan User pemilik**, **Barang Ditemukan = laporan temuan User penemu**. Keputusan lama yang menyatakan Barang Hilang berisi laporan temuan dan Barang Ditemukan hanya riwayat pengembalian sudah dicabut. Dokumentasi ini memakai baseline A18. A19 menambah fondasi tabel/model; A20 menambah formulir, rute, dan pengelolaan laporan kehilangan di **Akun**; A21 mengubah daftar dan detail publik; A22 merapikan navbar; A23 menyesuaikan panel Admin; A24 menambah seeder dan regresi akhir; A25 menyempurnakan tabel Admin, tombol filter, beranda lintas jenis, serta tampilan navbar/Akun; A26 merapikan kolom status dan tombol pada halaman Akun.

## Tentang Proyek

TitipTemu merupakan website untuk seluruh lingkungan kampus. User (mahasiswa, dosen, atau staf aktif) dapat membuat dua jenis laporan: **kehilangan** sebagai pemilik dan **temuan** sebagai penemu. Keduanya wajib memuat foto dan no. HP pelapor yang bersifat privat, serta melalui pemeriksaan Admin sebelum dipublikasikan. Guest mencari informasi di kedua kanal tanpa akun dan menghubungi pelapor lewat `wa.me` tanpa melihat nomor HP. Admin menggunakan akun terpisah dan jalur login tersembunyi; beberapa akun Admin dapat berdiri sendiri.

Tiap daftar publik menampilkan **2 card status → pencarian → card item**:

- **Barang Hilang**: **Barang Belum Ditemukan** / **Barang Sudah Ditemukan**.
- **Barang Ditemukan**: **Barang Belum Kembali** / **Barang Sudah Kembali**.

Klik card status tetap berada di dalam jenis laporan aktif. Daftar “Sudah” menampilkan barang selama **30 hari sejak persetujuan Admin**, lalu diarsipkan otomatis tanpa menghapus data. Laporan pemilik dan penemu disimpan di tabel terpisah dan tidak ditautkan/dicocokkan, meskipun barangnya sama.

## Tujuan

- Memusatkan dua kanal laporan yang sudah diverifikasi Admin: kehilangan oleh pemilik dan temuan oleh penemu.
- Memudahkan User melaporkan kehilangan maupun temuan lengkap dengan data pelapor, foto, dan no. HP privat.
- Menyediakan pencarian publik untuk Guest di dalam menu aktif, dengan dua card status per menu.
- Menghubungkan Guest dengan pemilik/penemu lewat WhatsApp tanpa menampilkan nomor HP di halaman publik.
- Menyediakan verifikasi Admin atas kedua jenis laporan dan konfirmasi status “Sudah”.
- Menjaga privasi nama pemilik, no. HP pemilik/penemu, dan bukti konfirmasi.
- Menyediakan sidebar Admin 5 menu dan dropdown **Buat Laporan** di navbar User.

## Fitur Utama

- Halaman publik **Barang Hilang** (laporan kehilangan) dan **Barang Ditemukan** (laporan temuan), masing-masing dengan 2 card status, pencarian, dan card item.
- Beranda menampilkan empat laporan terbaru dari kedua kanal, diurutkan dari yang paling baru.
- Registrasi dan login User tanpa verifikasi email; menu **Akun** memakai satu baris profil/biodata dengan tombol **Edit profil** dan **Keluar**, lalu daftar laporan kedua jenis tanpa tombol buat laporan. Pembuatan laporan tetap lewat dropdown **Buat Laporan** di navbar.
- Navbar publik menampilkan **Akun** lalu dropdown **Buat Laporan** berisi **Laporkan Kehilangan Barang** dan **Laporkan Barang Temuan** di pinggir kanan; **Keluar** tersedia di **Akun**, teks “Halo…” di navbar publik dihapus.
- Login Admin terpisah melalui jalur tersembunyi dari `ADMIN_LOGIN_URI`.
- Sidebar Admin 5 menu: **Dashboard**, **Cari laporan**, **Barang Hilang**, **Barang Ditemukan**, dan **Keluar Admin** di bawah. Status tidak masuk sidebar; card status dapat diklik dan tetap dalam jenis aktif.
- Dashboard Admin memakai tabel ringkas satu baris per laporan dengan tombol **Terapkan** di kanan; halaman **Cari laporan** menaruh tombol **Cari** di kanan dengan jarak dari dropdown.
- Cari laporan lintas jenis dan status; menu **Barang Hilang**/**Barang Ditemukan** di Admin difilter menurut jenis laporan.
- Form **Laporkan Kehilangan Barang** (no. HP pemilik, tanggal/lokasi hilang, foto wajib) dan **Laporkan Barang Temuan** (no. HP penemu, tanggal/lokasi ditemukan, foto wajib), dengan pengelolaan laporan kehilangan yang setara dengan laporan temuan.
- Tombol WhatsApp pada detail publik tanpa menampilkan nomor pemilik/penemu.
- Verifikasi, penolakan beralasan, dan edit Admin untuk laporan pada status menunggu verifikasi.
- Konfirmasi status “Sudah” dengan bukti privat; laporan berpindah ke card “Sudah” di menu yang sama.
- Pengarsipan otomatis kedua daftar “Sudah” setelah 30 hari dan penghapusan permanen manual oleh Admin untuk laporan selesai/arsip.
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

`MissingReportDemoSeeder` (A24) juga dijalankan oleh `DatabaseSeeder` dan menambah 8 laporan kehilangan fiktif untuk akun demo yang sama: 4 `approved`, 2 `found_pending`, dan 2 `found` yang masih berada dalam jendela tampil 30 hari. Seeder ini idempoten dan tidak mengubah 54 laporan lama dari seeder sebelumnya.

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

Pengarsipan otomatis untuk daftar **“Sudah”** dijalankan oleh scheduler Laravel pada `00:05`
zona waktu Asia/Makassar (WITA). Sesuai A18, aturan 30 hari berlaku untuk **Barang Sudah Ditemukan** dan **Barang Sudah Kembali**. `ArchiveReturnedReports` saat ini menjalankan `ReportArchiveService` untuk laporan temuan; `MissingReportArchiveService` untuk laporan kehilangan sudah tersedia dan diuji langsung, tetapi belum dipasang ke command/scheduler. Sementara itu, `MissingReport::publicFound()` tetap menyembunyikan laporan kehilangan setelah 30 hari meskipun status basis datanya belum `archived`. Aktifkan scheduler deployment dengan entri cron:

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

- **Guest**: bukan akun database; melihat dan mencari Barang Hilang (laporan kehilangan) serta Barang Ditemukan (laporan temuan), memilih card status, dan membuka detail publik.
- **User**: akun terdaftar; membuat laporan **kehilangan** maupun **temuan**, memantau keduanya lewat menu **Akun**, dan mengirim konfirmasi status “Sudah”. No. HP Akun wajib dan privat; diisi saat daftar atau diubah pada biodata **Akun**. Foto dan no. HP pelapor pada kedua formulir juga wajib dan tidak ditampilkan publik.
- **Admin**: akun terpisah di tabel `admins`; masuk lewat jalur tersembunyi, memeriksa kedua jenis laporan, menyetujui/menolak dengan alasan, mengedit laporan pada status menunggu verifikasi, memeriksa bukti privat, serta menghapus permanen laporan selesai/arsip. Beberapa akun Admin dapat berdiri sendiri.

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

Fondasi dan alur utama tersedia pada kode saat ini: pelaporan dua jenis oleh User, pencarian publik oleh Guest, kontak WhatsApp tanpa eksposur nomor, verifikasi Admin terpisah, konfirmasi privat, serta retensi 30 hari dan penghapusan permanen manual.

**A18 baru berupa spesifikasi:** dokumen produk/teknis telah diselaraskan dengan baseline dua kanal. **A19** sudah menambah fondasi laporan kehilangan — tabel `missing_reports`, model/factory, konstanta status, scope publik, serta service moderasi dan arsip 30 hari — tanpa mengubah data lama. **A20** menambah formulir **Laporkan Kehilangan Barang**, tujuh rute `missing-reports.*`, penyimpanan foto privat, dan halaman **Akun** dengan dua tombol serta dua daftar laporan. **A21** memetakan ulang daftar publik: `/barang/lost` memakai laporan kehilangan dan `/barang/found` memakai laporan temuan; tiap halaman memuat dua card status (`status=belum|sudah`) sebelum pencarian, label A18, detail dengan kontak WhatsApp privat, dan batas tampil 30 hari pada daftar “Sudah”. Beranda kini menampilkan empat laporan terbaru dari kedua kanal (A25). **A22** awalnya mengganti navbar publik dengan dropdown **Buat Laporan** dan tombol ikon **Akun**/**Keluar**; **A25** menghapus **Keluar** dari navbar, menaruh **Akun** sebelum dropdown **Buat Laporan** di pinggir kanan, dan memindahkan form profil ke rute `user.profile.edit` dengan tombol **Edit profil**/**Keluar** di halaman **Akun**. **A23** menyederhanakan sidebar Admin menjadi lima menu, menambah filter `jenis`/`status` pada dashboard, kartu status yang tetap dalam jenis aktif, pencarian lintas jenis, serta tinjauan dan verifikasi laporan kehilangan. **A24** menambah `MissingReportDemoSeeder`; **A25** merapikan tujuh bagian antarmuka yang diminta pemilik; **A26** merata-tengahkan kolom status di **Akun**, menghapus dua tombol buat laporan dari **Laporan saya**, dan memperbaiki tombol **Detail** agar memakai rute sesuai jenis. Regresi terakhir: 148 tes/1103 assertion lulus, Pint dan build lulus, serta 57 rute terdaftar. Dua hal masih terbuka dan tidak diubah tanpa keputusan pemilik: field/bukti konfirmasi laporan kehilangan ke **Barang Sudah Ditemukan**, serta penggantian URI publik `/barang/lost` dan `/barang/found` (sementara tetap dipakai).
