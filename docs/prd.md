# PRODUCT REQUIREMENTS DOCUMENT (PRD) — TitipTemu

**Versi:** 2.11 (baseline dua alur pelaporan A18: Barang Hilang = laporan kehilangan pemilik, Barang Ditemukan = laporan temuan penemu; tabel terpisah, dua card status per menu, dan aturan 30 hari pada daftar “Sudah”)

**Status:** Disetujui untuk MVP; keputusan lama sebelum A18 dicabut dan pertanyaan non-P0 yang tersisa dicatat terbuka

**Produk:** Website TitipTemu

**Tujuan:** Pelaporan kehilangan oleh User pemilik dan pelaporan temuan oleh User penemu, pencarian publik oleh Guest, serta dokumentasi barang yang sudah ditemukan/sudah kembali

**Lingkup:** Seluruh lingkungan kampus

**Stack acuan:** Laravel, MySQL, Blade, Bootstrap

> **Catatan A18 (2026-11-22):** keputusan lama yang menyatakan Barang Hilang berisi laporan temuan dan Barang Ditemukan hanya riwayat pengembalian **dicabut**. Baseline aktif: **Barang Hilang = laporan kehilangan yang dibuat User pemilik**, **Barang Ditemukan = laporan temuan yang dibuat User penemu**. Bagian bertanda “catatan historis” pada dokumen ini dan dokumen proyek lain bukan aturan aktif.

## 1. Ringkasan Produk

TitipTemu adalah website yang membantu civitas akademika mencari barang yang hilang dan menemukan pemilik barang yang ditemukan di lingkungan institusi. Tersedia dua kanal laporan yang dibuat oleh User yang login: **laporan kehilangan** dari pihak pemilik dan **laporan temuan** dari pihak penemu. Keduanya memuat foto dan nomor HP pelapor yang bersifat privat, serta melalui pemeriksaan Admin sebelum dipublikasikan.

TitipTemu menyediakan dua halaman publik utama:
- **Barang Hilang** — laporan **kehilangan** yang dibuat User pemilik. Dua card status: **Barang Belum Ditemukan** dan **Barang Sudah Ditemukan**.
- **Barang Ditemukan** — laporan **temuan** yang dibuat User penemu. Dua card status: **Barang Belum Kembali** dan **Barang Sudah Kembali**.

Kedua halaman memakai pola yang sama: 2 card status yang dapat diklik → pencarian → card item. Klik card status membawa ke tampilan/state berbeda **di dalam menu yang sama** dan tidak mengubah jenis laporan aktif. Daftar “Sudah” menampilkan item selama **30 hari sejak persetujuan Admin**, lalu disembunyikan/diarsipkan; data tetap disimpan.

Laporan pemilik dan laporan penemu **disimpan di tabel terpisah dan tidak ditautkan/dicocokkan**, walaupun keduanya bisa merujuk barang yang sama. Guest dapat melihat dan mencari informasi tanpa login, serta menghubungi pelapor lewat tombol WhatsApp yang mengarahkan ke `wa.me` tanpa menampilkan nomor HP. User wajib login untuk membuat kedua jenis laporan dan untuk konfirmasi status; verifikasi email tidak diperlukan. Admin memakai akun terpisah dan jalur login tersembunyi; Admin memeriksa publikasi kedua jenis laporan serta konfirmasi sebelum mengubah status/publikasi.

## 2. Masalah yang ingin diselesaikan
- Informasi barang hilang dan barang temuan sering tersebar di grup percakapan, media sosial, atau pengumuman.
- Penemu kesulitan menjangkau pemilik barang, dan pemilik kesulitan menemukan informasi yang tersebar.
- Informasi dari kedua pihak tidak tersimpan dalam daftar terpusat dan mudah ditelusuri.
- Informasi publik perlu diverifikasi agar laporan tidak menyesatkan atau mengandung data pribadi berlebihan.
- Status "belum" dan "sudah" pada tiap kanal perlu tercatat jelas tanpa membocorkan bukti privat.

## 3. Tujuan Produk
- Memusatkan dua kanal laporan — kehilangan oleh pemilik dan temuan oleh penemu — dalam satu website untuk seluruh lingkungan kampus.
- Memudahkan mahasiswa, dosen, dan staf aktif melaporkan kehilangan maupun temuan dengan format terstruktur, foto, dan nomor HP privat.
- Memungkinkan publik melihat, mencari, dan menghubungi pelapor lewat WhatsApp tanpa akun dan tanpa melihat nomor HP.
- Memastikan laporan dipublikasikan setelah persetujuan Admin.
- Menyediakan dua tampilan status per kanal sesuai keputusan A18 dan aturan tampil 30 hari pada daftar “Sudah”.
- Menyimpan laporan kehilangan dan laporan temuan pada tabel terpisah tanpa pencocokan otomatis.
- Menyediakan akun Admin terpisah dengan jalur masuk tersembunyi dan beberapa akun Admin yang berdiri sendiri.
- Menyediakan riwayat status bagi User dan Admin.

## 4. Pengguna dan peran
### 4.1 Guest
- Melihat beranda, Barang Hilang, Barang Ditemukan, detail publik, pencarian/filter, dan informasi panduan.
- Menghubungi pemilik (pada laporan kehilangan) atau penemu (pada laporan temuan) lewat tombol WhatsApp pada detail publik tanpa melihat nomor HP.
- Tidak dapat membuat laporan atau mengonfirmasi status.
### 4.2 User (pemilik atau penemu)
- User adalah mahasiswa, dosen, atau staf aktif kampus yang memiliki akun.
- Mendaftar dan login; salah satu akun dapat membuat **kedua jenis laporan** (sebagai pemilik pada laporan kehilangan dan sebagai penemu pada laporan temuan).
- **Laporkan Kehilangan Barang**: mengisi no. HP pemilik, data barang, tanggal dan lokasi hilang, mengunggah foto, menyetujui ketentuan.
- **Laporkan Barang Temuan**: mengisi no. HP penemu, data barang, tanggal dan lokasi ditemukan, mengunggah foto, menyetujui ketentuan.
- Nomor HP pada kedua formulir wajib diisi tetapi tidak ditampilkan secara publik; kontak hanya lewat redirect `wa.me`.
- Melihat laporan kedua jenis dan status miliknya melalui menu **Akun**, tanpa tombol buat laporan; pembuatan tetap lewat dropdown **Buat Laporan** di navbar.
- Memperbaiki dan mengirim ulang laporan yang ditolak setelah membaca alasan Admin.
- Mengedit atau membatalkan laporan berstatus `Pending`; laporan yang sudah disetujui tidak dapat diedit langsung.
- Mengajukan konfirmasi status “Sudah” untuk laporan miliknya; mekanisme dan bukti untuk laporan kehilangan ditetapkan pada tugas implementasi terkait (lihat catatan terbuka A18).
- Melihat status pemeriksaan konfirmasi.
### 4.3 Admin
- Sistem mendukung beberapa akun Admin, masing-masing dengan akun sendiri di tabel `admins`, terpisah dari akun User.
- Login ke area Admin melalui jalur tersembunyi yang ditentukan oleh `ADMIN_LOGIN_URI`; tidak ada tautan Admin pada navigasi publik.
- Memeriksa, menyetujui, atau menolak laporan dengan alasan wajib saat menolak.
- Mengedit langsung laporan berstatus `Pending` dan mencatat perubahan pada riwayat.
- Memeriksa bukti/status yang privat, menyetujui atau menolak konfirmasi dengan alasan wajib saat menolak.
- Mengelola publikasi, arsip, dan aktivitas verifikasi untuk **kedua jenis laporan**.
- Memakai sidebar 5 menu: **Dashboard**, **Cari laporan**, **Barang Hilang**, **Barang Ditemukan**, dan **Keluar Admin** di bagian bawah. Status tidak masuk sidebar; card status pada Dashboard dapat diklik dan tetap berada dalam jenis laporan aktif.
- Menu **Barang Hilang** dan **Barang Ditemukan** di Admin difilter menurut **jenis laporan**; **Cari laporan** tetap lintas jenis dan status. Dashboard merangkum kedua jenis.
- Menghapus permanen laporan yang sudah selesai/arsip setelah konfirmasi; data lain tidak dihapus.

### 4.4 Data pendaftaran User
- Nama lengkap — wajib.
- NIM/NIP — wajib dan privat.
- Email — wajib, unik, dan tidak memerlukan verifikasi (ketentuan lama dicabut pada A11).
- No. HP — wajib dan privat; diisi saat pendaftaran dan dapat diubah di menu **Akun**, tidak ditampilkan pada halaman publik. Label formulir cukup **No. HP** tanpa keterangan tambahan.
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
6. Transisi ke status “Sudah” pada tiap kanal dikonfirmasi oleh pelapor jenis terkait dan diperiksa Admin.
7. Laporan pemilik dan laporan penemu tidak dicocokkan atau ditautkan oleh sistem.

## 6. Navigasi
### 6.1 Publik
- Beranda
- **Barang Hilang** — laporan kehilangan pemilik; card **Barang Belum Ditemukan** / **Barang Sudah Ditemukan**
- **Barang Ditemukan** — laporan temuan penemu; card **Barang Belum Kembali** / **Barang Sudah Kembali**
- Detail laporan dengan tombol WhatsApp
- Cari/filter di dalam menu aktif
- Panduan / Ketentuan
- Login
- Daftar
### 6.2 User
- Beranda
- Barang Hilang
- Barang Ditemukan
- Dropdown **Buat Laporan** di navbar: **Laporkan Kehilangan Barang** dan **Laporkan Barang Temuan**
- **Akun** (profil, aktivitas, dan daftar laporan kedua jenis tanpa tombol buat laporan)
- Detail Laporan Saya
- Konfirmasi status “Sudah” untuk laporan miliknya
- Tombol ikon **Akun** di navbar, diikuti dropdown **Buat Laporan** di pinggir kanan; **Keluar** tersedia di halaman **Akun**, bukan navbar. Teks “Halo…” pada navbar publik dihapus
- Logout
### 6.3 Admin
- Login tersembunyi melalui `ADMIN_LOGIN_URI` (tidak ditautkan di navigasi publik)
- Sidebar 5 menu: **Dashboard**, **Cari laporan**, **Barang Hilang**, **Barang Ditemukan**, **Keluar Admin** di bawah
- Dashboard dengan kartu status yang dapat diklik; status tidak muncul sebagai menu sidebar
- Menu **Barang Hilang** dan **Barang Ditemukan** difilter menurut jenis laporan
- Detail laporan, edit `Pending`, bukti privat, dan riwayat aktivitas
- Penghapusan permanen untuk laporan selesai/arsip

## 7. Alur utama
### 7.1 Browsing sebagai Guest
1. Guest membuka TitipTemu.
2. Guest memilih **Barang Hilang** (mencari barang yang dilaporkan hilang pemiliknya) atau **Barang Ditemukan** (melihat barang yang dilaporkan penemunya).
3. Guest memilih salah satu dari dua card status di menu tersebut; daftar yang tampil tetap berada dalam jenis laporan aktif.
4. Guest melakukan pencarian, lalu membuka detail.
5. Guest menekan tombol WhatsApp; sistem mengarahkan ke `wa.me` tanpa menampilkan nomor pelapor di halaman publik.
6. Jika ingin membuat laporan, sistem mengarahkan Guest ke login/registrasi.
### 7.2 Membuat laporan kehilangan (pemilik)
1. User login, lalu membuka **Buat Laporan → Laporkan Kehilangan Barang** (atau tombol di **Akun**).
2. User mengisi no. HP pemilik, data barang, tanggal dan lokasi **hilang**, mengunggah foto, dan menyetujui ketentuan.
3. Sistem memvalidasi input; laporan disimpan di **tabel laporan kehilangan** yang terpisah dari laporan temuan.
4. Laporan berstatus menunggu verifikasi dan belum tampil publik.
5. Admin meninjau dan dapat mengedit laporan pada status menunggu verifikasi.
6. Admin menyetujui atau menolak dengan alasan wajib; User dapat memperbaiki dan mengirim ulang laporan yang ditolak.
7. Laporan yang disetujui tampil di **Barang Hilang → Barang Belum Ditemukan** beserta tombol WhatsApp.
### 7.3 Membuat laporan temuan (penemu)
1. User login, lalu membuka **Buat Laporan → Laporkan Barang Temuan** (atau tombol di **Akun**).
2. User mengisi no. HP penemu, data barang, tanggal dan lokasi **ditemukan**, mengunggah foto, dan menyetujui ketentuan.
3. Sistem memvalidasi input; laporan disimpan di **tabel laporan temuan** (`lost_reports` yang sudah ada).
4. Laporan berstatus menunggu verifikasi dan belum tampil publik.
5. Admin menyetujui atau menolak dengan alasan wajib; User dapat memperbaiki dan mengirim ulang.
6. Laporan yang disetujui tampil di **Barang Ditemukan → Barang Belum Kembali** beserta tombol WhatsApp.
### 7.4 Transisi ke daftar “Sudah” dan arsip
1. Pelapor jenis terkait mengajukan konfirmasi status “Sudah” melalui **Akun**.
2. Untuk laporan temuan, konfirmasi memuat **nama pemilik** dan **foto bersama penemu–pemilik** yang privat, seperti alur yang sudah berlaku.
3. Untuk laporan kehilangan, field/bukti konfirmasi “Barang Sudah Ditemukan” **belum ditetapkan rincinya** pada A18 dan akan diputuskan sebelum tugas implementasi terkait; tidak boleh dianggap sudah final.
4. Admin memeriksa konfirmasi secara privat, lalu menyetujui atau menolak dengan alasan wajib.
5. Jika disetujui, laporan berpindah dari card “Belum” ke card “Sudah” **di menu yang sama**:
   - laporan kehilangan → **Barang Sudah Ditemukan**;
   - laporan temuan → **Barang Sudah Kembali**.
6. Jika ditolak, laporan tetap di card “Belum” dan dapat diperbaiki/dikirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
7. Daftar “Sudah” menampilkan item selama **30 hari sejak persetujuan Admin**, lalu diarsipkan dan disembunyikan dari publik secara otomatis. Data dan bukti tetap disimpan.
8. Admin dapat menghapus permanen laporan selesai/arsip setelah konfirmasi, termasuk foto dan bukti privatnya.
9. Laporan kehilangan dan laporan penemu tetap **terpisah**, walau barangnya sama; tidak ada pencocokan otomatis maupun penautan manual.
## 8. Fitur MVP
### Must Have
- Halaman publik/Guest.
- Daftar **Barang Hilang** untuk laporan kehilangan pemilik, dengan 2 card status dan pencarian.
- Daftar **Barang Ditemukan** untuk laporan temuan penemu, dengan 2 card status dan pencarian.
- Detail laporan dengan tombol WhatsApp tanpa eksposur nomor pemilik/penemu.
- Search dasar di dalam menu aktif.
- Registrasi dan login User; menu **Akun** untuk aktivitas User dengan daftar kedua jenis laporan, tanpa tombol buat laporan.
- Dropdown **Buat Laporan** di navbar berisi **Laporkan Kehilangan Barang** dan **Laporkan Barang Temuan**.
- Login Admin terpisah melalui jalur tersembunyi.
- Tabel laporan terpisah untuk kehilangan dan temuan; keduanya tidak ditautkan.
- Form **Laporkan Kehilangan Barang** dengan no. HP pemilik dan foto wajib.
- Form **Laporkan Barang Temuan** dengan no. HP penemu dan foto wajib.
- Persetujuan ketentuan pelaporan pada kedua formulir.
- Verifikasi kedua jenis laporan oleh Admin, termasuk edit laporan `Pending`.
- Dashboard/riwayat laporan milik User untuk kedua jenis.
- Konfirmasi status “Sudah” pada laporan temuan dengan nama pemilik dan foto bersama penemu–pemilik.
- Bukti/status privat; hanya User terkait dan Admin yang dapat melihatnya.
- Verifikasi konfirmasi oleh Admin dan perpindahan card “Belum” → “Sudah” di menu yang sama.
- Pengarsipan otomatis dari kedua daftar “Sudah” setelah 30 hari.
- Sidebar Admin 5 menu dan card status yang dapat diklik tanpa mengubah jenis laporan aktif.
- Penghapusan permanen manual oleh Admin untuk laporan selesai/arsip setelah konfirmasi.
- Pembatasan akses dan perlindungan data.
## 9. Data laporan (kedua jenis)
Kedua jenis laporan disimpan di tabel terpisah. Field wajib, format, batas ukuran, dan kategori umum sama; yang berbeda adalah sudut pandang pelapor, tanggal/lokasi, dan nomor HP yang dicatat.

### 9.1 Data laporan temuan (penemu)
Field formulir yang ditetapkan:
- Nama penemu (tercatat dari akun User yang login)
- **No. HP penemu (wajib)** — hanya dipakai untuk tombol WhatsApp, tidak ditampilkan sebagai teks di halaman publik maupun di HTML
- Nama barang (wajib)
- Kategori (wajib)
- Deskripsi umum (wajib; jangan memuat seluruh ciri unik yang dapat digunakan untuk verifikasi)
- **Tanggal ditemukan (wajib)**
- **Lokasi ditemukan (wajib)**
- Foto barang (wajib; maksimal 3 foto menurut keputusan P0.4)
- Format foto: JPG, JPEG, PNG, atau WebP
- Ukuran maksimal: 2 MB per foto
- Foto disimpan pada storage lokal Laravel dan hanya ditampilkan publik setelah laporan disetujui Admin
- Pernyataan persetujuan ketentuan pelaporan barang temuan (wajib)

**Catatan selisih implementasi (2026-11-22):** implementasi saat ini menerima satu file foto per laporan (`required|image|mimes:jpg,jpeg,png,webp|max:2048`) dan memakai lima pilihan kategori (Elektronik, Dokumen, Aksesori, Pakaian, Lainnya). Keputusan P0.4 tentang maksimal 3 foto dan delapan kategori tetap tercatat di bawah; penyelarasan implementasi memerlukan tugas tersendiri.

Kategori tetap:
- Elektronik
- Dompet & Uang
- Dokumen & Kartu Identitas
- Kunci
- Tas
- Pakaian & Aksesoris
- Buku & Alat Tulis
- Lainnya

### 9.2 Data laporan kehilangan (pemilik)
- Nama pemilik (tercatat dari akun User yang login)
- **No. HP pemilik (wajib)** — privat; hanya dipakai server untuk tautan `wa.me`, tidak ditampilkan sebagai teks/HTML publik
- Nama barang (wajib)
- Kategori (wajib; pilihan sama dengan 9.1)
- Deskripsi umum (wajib; hindari ciri unik yang dipakai untuk verifikasi)
- **Tanggal hilang (wajib)**
- **Lokasi hilang (wajib)**
- Foto barang (wajib; format JPG/JPEG/PNG/WebP, maksimal 2 MB per file menurut keputusan yang berlaku)
- Foto disimpan pada storage privat Laravel dan hanya tampil publik setelah disetujui Admin
- Pernyataan persetujuan ketentuan pelaporan kehilangan (wajib)
- Tidak ada field penautan ke laporan temuan; kedua kanal berdiri sendiri
## 10. Data konfirmasi pengembalian
- **Nama pemilik (wajib)** — privat, hanya untuk verifikasi User terkait dan Admin
- **Foto bersama penemu–pemilik (wajib, 1–3 file)** — JPG/JPEG/PNG/WebP, maksimal 2 MB per file, disimpan pada storage privat Laravel
- Catatan singkat pengembalian (opsional; jangan memuat kontak pribadi)
- Waktu pengembalian dicatat otomatis saat Admin menyetujui
- Checkbox pernyataan bahwa barang sudah diterima oleh pemiliknya (wajib)
- Status verifikasi Admin, Admin peninjau, dan waktu peninjauan
- Alasan Admin (wajib jika ditolak)
- Penyelesaian langsung oleh Admin dengan catatan wajib setelah batas 3 pengiriman tercapai
## 11. Aturan bisnis
- BR-01: Sistem berlaku untuk seluruh lingkungan kampus.
- BR-02: Guest dapat melihat data publik dan menghubungi pemilik/penemu lewat WhatsApp tanpa login.
- BR-03: User wajib login untuk membuat laporan kehilangan, membuat laporan temuan, dan mengonfirmasi status; verifikasi email tidak diperlukan.
- BR-04: Laporan kehilangan dan laporan temuan wajib memiliki foto, no. HP pelapor, dan data wajib lengkap.
- BR-05: Laporan baru menunggu verifikasi dan tidak tampil publik. Laporan yang disetujui tampil pada card “Belum” di menu sesuai jenisnya; status lain tidak tampil.
- BR-06: Nomor HP pemilik maupun penemu tidak pernah ditampilkan sebagai teks pada halaman atau respons publik; kontak hanya melalui redirect `wa.me` yang dibuat server.
- BR-07: Hanya User pembuat laporan yang dapat mengajukan konfirmasi status untuk laporannya.
- BR-08: Konfirmasi status pada laporan temuan wajib memiliki nama pemilik, foto bersama penemu–pemilik, dan persetujuan pernyataan. Field/bukti konfirmasi laporan kehilangan belum ditetapkan rincinya pada A18.
- BR-09: Nama pemilik dan bukti/status privat tidak ditampilkan kepada publik; keduanya dapat diakses oleh User terkait dan Admin.
- BR-10: Konfirmasi status menunggu pemeriksaan Admin.
- BR-11: Setelah konfirmasi disetujui, laporan kehilangan berpindah ke **Barang Sudah Ditemukan** dan laporan temuan ke **Barang Sudah Kembali**, tetap di menu yang sama.
- BR-12: Kedua daftar “Sudah” ditampilkan selama 30 hari sejak Admin menyetujui dan mempublikasikannya, menggunakan zona waktu Asia/Makassar (WITA). Setelah batas itu, laporan tidak tampil di publik dan berstatus arsip.
- BR-13: Pengarsipan 30 hari tidak menghapus data secara permanen. Data laporan tetap disimpan di database dan bukti tetap privat.
- BR-14: Admin wajib menyertakan alasan saat menolak laporan maupun konfirmasi status.
- BR-15: Pembuat laporan dapat mengedit atau membatalkan laporan pada status menunggu verifikasi; laporan yang sudah disetujui tidak dapat diedit langsung.
- BR-16: Admin dapat mengedit langsung laporan pada status menunggu verifikasi; perubahan Admin dicatat pada riwayat status dengan peninjau Admin.
- BR-17: Penolakan laporan wajib memiliki alasan. Pembuat laporan dapat memperbaiki dan mengirim ulang laporan yang ditolak.
- BR-18: Penolakan konfirmasi status wajib memiliki alasan. Pembuat laporan dapat memperbaiki dan mengirim ulang maksimal 3 kali; setelah batas tercapai, penyelesaian dilakukan melalui Admin.
- BR-19: Data laporan, data konfirmasi, dan riwayat status tidak dihapus permanen, kecuali Admin melakukan penghapusan manual sesuai BR-20.
- BR-20: Admin dapat menghapus permanen laporan yang sudah selesai/arsip setelah konfirmasi; penghapusan mencakup data konfirmasi, riwayat status, foto laporan, dan bukti privat terkait. Status lain tidak dapat dihapus.
- BR-21: Akun Admin terpisah dari akun User, disimpan di tabel `admins`, dan masuk melalui jalur tersembunyi yang tidak ditautkan pada navigasi publik. Beberapa akun Admin dapat berdiri sendiri.
- BR-22: Laporan kehilangan dan laporan temuan disimpan di **tabel terpisah** dan tidak ditautkan/dicocokkan, meskipun keduanya merujuk barang yang sama.
- BR-23: Klik card status pada menu publik membawa ke tampilan/state lain **di dalam jenis laporan aktif**; tidak mengganti menu atau jenis laporan.
- BR-24: Navbar User memakai dropdown **Buat Laporan** berisi dua formulir; **Akun** menampilkan daftar tanpa tombol buat laporan. Teks “Halo…” di navbar publik dihapus, sedangkan pada sidebar Admin dipertahankan untuk sementara. Kolom status di tabel **Akun** dirata-tengahkan; tautan **Detail** di daftar pengelolaan berbentuk tombol dengan rute sesuai jenis.
- BR-25: Sidebar Admin terdiri dari 5 menu — Dashboard, Cari laporan, Barang Hilang, Barang Ditemukan, Keluar Admin di bawah. Status tidak masuk sidebar; card status dapat diklik dan tetap dalam jenis aktif. Menu Barang Hilang/Barang Ditemukan difilter menurut jenis laporan; Cari laporan lintas jenis dan status.
## 12. Status dan transisi
### 12.1 Status dan label publik
Kedua tabel memakai siklus yang setara — menunggu verifikasi → aktif (“Belum”) → konfirmasi “Sudah” → arsip. Konstanta internal laporan kehilangan (A19): `pending`, `approved`, `rejected`, `found_pending`, `found`, `archived`, `cancelled`; label yang dilihat publik mengikuti jenis laporan:

| Jenis laporan | Menu | Card “Belum” | Card “Sudah” |
|---|---|---|---|
| Kehilangan (pemilik) | Barang Hilang | **Barang Belum Ditemukan** | **Barang Sudah Ditemukan** |
| Temuan (penemu) | Barang Ditemukan | **Barang Belum Kembali** | **Barang Sudah Kembali** |

Status umum:
- **Menunggu verifikasi** — laporan baru, belum tampil publik.
- **Aktif / Belum** — disetujui Admin, tampil pada card “Belum” menu terkait.
- **Konfirmasi Sudah** — pelapor mengajukan konfirmasi, masih menunggu Admin; item tetap pada card “Belum”.
- **Sudah** — konfirmasi disetujui Admin, tampil pada card “Sudah” selama 30 hari.
- **Arsip** — batas 30 hari berakhir; tidak tampil publik; data dan riwayat tetap disimpan.
- **Ditolak** — Admin menolak dengan alasan wajib; dapat diperbaiki dan dikirim ulang.
- **Dibatalkan** — laporan pada status menunggu verifikasi dibatalkan oleh pembuatnya.
### 12.2 Alur transisi dasar
`Menunggu verifikasi → Aktif/Belum → Konfirmasi Sudah → Sudah → Arsip`
Penolakan laporan: `Menunggu verifikasi → Ditolak`, dengan alasan wajib.
Penolakan konfirmasi: `Konfirmasi Sudah → Aktif/Belum`; alasan wajib dicatat dan pengiriman ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
Pembatalan: `Menunggu verifikasi → Dibatalkan` oleh pembuat laporan.
Kedua alur di atas berjalan sendiri-sendiri di tabel masing-masing; tidak ada transisi yang memindahkan laporan antarmenu.
## 13. Informasi publik dan privasi
### Publik dapat melihat
- Nama barang, kategori, deskripsi yang telah diverifikasi.
- Foto laporan yang disetujui dan aman ditampilkan.
- Tanggal dan lokasi **hilang** (pada Barang Hilang) atau **ditemukan** (pada Barang Ditemukan); tingkat detail lokasi masih terbuka.
- Status laporan (“Belum”/“Sudah”) dan tombol WhatsApp untuk menghubungi pelapor.
### Tidak ditampilkan publik
- NIM/nomor identitas mahasiswa.
- Email pribadi, alamat, dan data akun.
- **Nomor HP pemilik maupun penemu** — tidak muncul sebagai teks/HTML; hanya dipakai server untuk membuat tautan `wa.me`.
- **Nama pemilik** dan **foto bersama penemu–pemilik** — hanya untuk verifikasi User terkait dan Admin.
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
- Guest dapat melihat dan mencari Barang Hilang (laporan kehilangan) dan Barang Ditemukan (laporan temuan) tanpa login.
- Tiap menu publik menampilkan 2 card status yang dapat diklik dan tetap dalam jenis laporan aktif.
- Guest dapat menghubungi pemilik/penemu lewat tombol WhatsApp tanpa melihat nomor HP di halaman publik.
- Guest tidak dapat membuat laporan atau konfirmasi status.
- User dapat mendaftar, login, dan mengirim kedua jenis laporan lengkap dengan no. HP pelapor dan foto.
- Laporan tidak tampil sebelum persetujuan Admin.
- Admin terpisah masuk lewat jalur tersembunyi dan dapat menyetujui/menolak laporan dengan alasan wajib saat menolak.
- Admin dapat mengedit laporan `Pending`, tetapi tidak mengedit langsung laporan `Approved`.
- User hanya dapat mengirim konfirmasi untuk laporan miliknya.
- Konfirmasi pengembalian wajib memuat nama pemilik, foto bersama penemu–pemilik, dan pernyataan.
- Nama pemilik dan foto bersama tidak terbuka untuk Guest.
- Admin dapat menyetujui/menolak konfirmasi pengembalian.
- Laporan yang disetujui konfirmasinya berpindah dari card “Belum” ke card “Sudah” di menu yang sama.
- Item pada kedua daftar “Sudah” tidak lagi tampil publik setelah 30 hari, dengan data tetap tersimpan.
- Laporan kehilangan dan laporan temuan tersimpan terpisah serta tidak ditautkan.
- Admin dapat menghapus permanen laporan `returned`/`archived` setelah konfirmasi; status lain tidak dapat dihapus.
## 16. Risiko dan mitigasi
| Risiko | Mitigasi |
|---|---|
| Laporan palsu/spam | Login, validasi, verifikasi Admin, batas frekuensi jika diperlukan |
| Foto atau data tidak pantas | Verifikasi dan ketentuan upload |
| Bukti pengembalian palsu | Admin memeriksa bukti; keputusan status tidak otomatis |
| Informasi sensitif terpublikasi | Minimalkan field publik dan batasi akses file |
| Admin lambat memeriksa | Dashboard antrean dan penanda pending |
| Data hilang | Backup database dan file foto |
| Postingan tetap muncul melewati 30 hari | Job terjadwal untuk mengubah visibilitas/arsip, bukan mengandalkan penghapusan manual |
## 17. Pertanyaan non-P0 yang terbuka
Keputusan P0 yang telah disetujui berlaku. Pertanyaan yang masih terbuka:
1. Pengelolaan kategori oleh Admin, atau kategori tetap saja.
2. Tingkat detail lokasi ditemukan yang ditampilkan publik.
3. Tanggal pengembalian dicatat otomatis dan catatan pengembalian opsional (keputusan implementasi yang sudah diterapkan).
4. Batas foto bukti pengembalian: 1–3 file JPG/JPEG/PNG/WebP, maksimal 2 MB per file (sudah diterapkan).
5. Teks ketentuan pelaporan yang wajib disetujui User.
6. Apakah notifikasi status termasuk MVP.
7. **Terbuka A18:** field dan bukti untuk konfirmasi transisi laporan kehilangan ke **Barang Sudah Ditemukan** belum ditetapkan; perlu diputuskan sebelum tugas implementasi terkait.
8. **Terbuka A18:** rute publik (`/barang/lost` dan `/barang/found`) untuk sementara tetap dipakai agar tautan lama tidak rusak; mengganti URI memerlukan keputusan terpisah.

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
| 2.3 | P0.3 ditetapkan: field pendaftaran lengkap dan verifikasi email wajib sebelum pelaporan; identitas kampus dicatat tanpa pencocokan database resmi. Ketentuan verifikasi email kemudian dicabut pada A11 (2026-11-22). |
| 2.4 | P0.4 ditetapkan: field laporan, delapan kategori tetap, maksimal 3 foto JPG/JPEG/PNG/WebP, maksimal 2 MB per foto, storage lokal Laravel, dan publikasi foto setelah persetujuan Admin. |
| 2.5 | P0.5 ditetapkan: kontak privat, edit/batal Pending, Approved tidak diedit langsung, alasan penolakan wajib, pengiriman ulang, batas 3 kali konfirmasi, serta retensi data dan riwayat. |
| 2.6 | P0.8 selesai: seluruh keputusan P0 diterapkan, versi/status diselaraskan, aturan draft yang sudah diputuskan difinalkan, dan daftar pertanyaan terbuka non-P0 disamakan dengan Requirement serta ARCHITECTURE. |
| 2.7 | 2026-11-22: diselaraskan dengan alur yang disetujui pemilik — User melaporkan barang temuan, Admin terpisah dengan login tersembunyi, kontak WhatsApp tanpa eksposur nomor, konfirmasi dengan nama pemilik dan foto bersama, edit Admin hanya pada `Pending`, penghapusan permanen manual untuk `returned`/`archived`, serta catatan selisih implementasi (jumlah foto dan kategori). |
| 2.8 | 2026-11-22 (A11): verifikasi email dihapus; eyebrow hanya tersisa di beranda; subtitle **Cari laporan** dihapus; notifikasi menjadi toast mengambang yang hilang setelah 3 detik; menu **Akun** dimulai dari kartu profil. |
| 2.9 | 2026-11-22 (A12): no. HP opsional dan privat ditambahkan pada pendaftaran dan biodata **Akun**; label **Program studi (wajib diisi mahasiswa)** diselaraskan; teks bantuan di bawah pemilihan foto profil dihapus. |
| 2.10 | 2026-11-22 (A13): no. HP Akun diubah menjadi wajib pada pendaftaran dan biodata; keterangan `(opsional)` dihapus sehingga label hanya **No. HP**. |
| 2.11 | 2026-11-22 (A18): baseline dua alur pelaporan — **Barang Hilang = laporan kehilangan pemilik**, **Barang Ditemukan = laporan temuan penemu**. Tabel terpisah, dua card status per menu (Belum/Sudah), 30 hari pada daftar “Sudah”, foto dan no. HP wajib tetapi privat, tanpa penautan antarlaporan, dropdown **Buat Laporan**, sidebar Admin 5 menu, dan card status yang tetap dalam jenis aktif. Keputusan lama sebelum A18 dicabut. |


## Pertanyaan konfirmasi kelompok
1. Apakah “Barang Ditemukan” benar-benar hanya berisi laporan yang sudah dikembalikan, atau juga menerima laporan barang temuan baru? **Keputusan lama 2026-11-22: Barang Ditemukan hanya berisi laporan yang pengembaliannya disetujui Admin; laporan temuan tampil di Barang Hilang. Keputusan ini DICABUT oleh A18 (2026-11-22): Barang Ditemukan adalah kanal laporan temuan penemu, dan Barang Hilang adalah kanal laporan kehilangan pemilik.**
2. Siapa saja yang boleh mendaftar: mahasiswa saja, dosen/staf, atau seluruh civitas akademika? **Sudah diputuskan: mahasiswa, dosen, dan staf aktif kampus.**
3. Apakah pendaftaran harus menggunakan email kampus/NIM dan apakah perlu verifikasi email? **Sudah diputuskan: pendaftaran meminta nama lengkap, NIM/NIP, email, password, fakultas, dan program studi khusus mahasiswa; identitas tidak diverifikasi melalui database resmi kampus. Sesuai A11 (2026-11-22), email tidak perlu diverifikasi sebelum pelaporan.**
4. Form laporan barang temuan: field apa yang wajib? **Keputusan 2026-11-22: no. HP penemu, nama barang, kategori tetap, deskripsi, tanggal dan lokasi ditemukan, foto, dan ketentuan.**
5. Berapa foto yang boleh diunggah, format, dan ukuran maksimal? **Sudah diputuskan: maksimal 3 foto; JPG/JPEG/PNG/WebP; maksimal 2 MB per foto.**
6. Apa ketentuan laporan yang harus dicentang user? Apakah perlu teks persetujuan yang akan disusun?
7. Informasi kontak penemu apakah ditampilkan publik, disembunyikan, atau komunikasi hanya melalui admin? **Keputusan 2026-11-22: nomor HP penemu tidak ditampilkan secara publik. Guest menghubungi penemu lewat tombol WhatsApp yang mengarahkan ke `wa.me`; sistem tidak memakai WhatsApp API, kredensial eksternal, atau layanan berbayar.**
8. Pada konfirmasi barang kembali, foto bukti harus memperlihatkan barang, pemilik, proses penyerahan, atau cukup barang saja? **Keputusan 2026-11-22: wajib foto bersama penemu–pemilik, disertai nama pemilik; keduanya bersifat privat dan hanya dapat dilihat User terkait serta Admin.**
9. Apakah User boleh membatalkan laporan atau mengubahnya setelah disetujui? **Sudah diputuskan: pemilik boleh mengedit/membatalkan saat `Pending`; `Approved` tidak dapat diedit langsung.**
10. Jika Admin menolak bukti pengembalian, apakah laporan tetap berada di Barang Hilang dan User boleh mengirim ulang bukti? **Sudah diputuskan: alasan wajib, perbaikan dan pengiriman ulang maksimal 3 kali; setelah itu melalui Admin.**
11. Hitungan 30 hari pada Barang Ditemukan dimulai dari waktu Admin menyetujui pengembalian, atau dari tanggal user menyatakan barang kembali? **Sudah diputuskan: sejak Admin menyetujui dan mempublikasikan, zona waktu Asia/Makassar.**
12. Setelah 30 hari, apakah postingan hanya disembunyikan/diarsipkan atau benar-benar dihapus dari database? **Sudah diputuskan: diarsipkan dan disembunyikan dari publik; data tetap disimpan, foto bukti tetap privat.**
13. Apakah Admin boleh menghapus laporan, atau hanya menolak/arsipkan? **Keputusan 2026-11-22: Admin dapat menghapus permanen laporan berstatus `returned` atau `archived` setelah konfirmasi. Arsip otomatis 30 hari tetap berlaku dan data lain tidak dihapus; penghapusan manual mencakup foto dan bukti privat terkait.**
14. Lingkup final TitipTemu: fakultas tertentu, program studi, atau seluruh kampus? **Sudah diputuskan: seluruh lingkungan kampus.**
15. Apakah pengembalian boleh diajukan sebelum Admin memeriksa bukti? **Keputusan 2026-11-22: User mengirim konfirmasi terlebih dahulu dengan nama pemilik dan foto bersama, lalu Admin memeriksa dan menyetujui atau menolak. Bagian lama yang menyatakan laporan temuan masuk ke Barang Hilang DICABUT oleh A18: konfirmasi kini memindahkan laporan ke card “Sudah” di menu yang sama.**
