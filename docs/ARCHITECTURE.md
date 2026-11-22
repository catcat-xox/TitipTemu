# ARCHITECTURE.md — TitipTemu

**Status:** Dasar teknis MVP dan keputusan produk P0 telah disetujui; alur kerja diselaraskan dengan keputusan pemilik 2026-11-22; pertanyaan non-P0 yang tersisa tetap terbuka.

## 1. Tujuan arsitektur
- Menjaga MVP tetap mudah dipahami dan realistis untuk tim mahasiswa pengembang open source.
- Mengutamakan kemudahan pemeliharaan dan penerapan daripada kompleksitas yang belum diperlukan.
- Menegakkan hak akses peran, kepemilikan, privasi, dan verifikasi di sisi server.
- Membuat perubahan status laporan barang temuan dan konfirmasi pengembalian jelas.

## 2. Arsitektur yang disetujui
Gunakan **satu aplikasi web monolitik Laravel** dengan pemisahan MVC sesuai konvensi Laravel. Jangan menambahkan microservices untuk MVP.
- **Web routes:** definisi rute dan middleware.
- **Controllers:** koordinasi HTTP, proses awal validasi, dan pemilihan respons; jangan menumpuk logika bisnis dalam controller yang terlalu besar.
- **Form Requests** (atau objek permintaan tervalidasi yang setara): validasi permintaan dan aturan otorisasi jika sesuai.
- **Models:** relasi penyimpanan dan akses data dengan cakupan yang tepat.
- **Services/actions:** operasi bisnis seperti pengiriman laporan, verifikasi, konfirmasi pengembalian, dan pengarsipan jika kompleksitasnya membutuhkan.
- **Policies/middleware:** otorisasi peran, kepemilikan, dan Admin.
- **Views:** Laravel Blade + Bootstrap yang dirender di server (disetujui). Bangun sistem desain modern bergaya startup di atas Bootstrap; hindari tampilan yang hanya menggunakan gaya bawaan Bootstrap.
- **MySQL:** penyimpanan relasional melalui migration dan ORM Laravel.

## 3. Modul logis
1. **Penjelajahan publik** — beranda, Barang Hilang, Barang Ditemukan, pencarian/filter, detail publik, panduan.
2. **Autentikasi dan akun** — registrasi/login User, menu **Akun**, dan akun Admin terpisah dengan login tersembunyi.
3. **Pelaporan barang temuan** — membuat dan memvalidasi laporan dengan no. HP penemu dan tanggal/lokasi ditemukan, unggah foto, daftar/detail laporan milik User, status laporan.
4. **Kontak WhatsApp** — membuat tautan `wa.me` di server tanpa menampilkan nomor HP penemu pada halaman/respons publik.
5. **Verifikasi Admin** — antrean laporan, pencarian laporan lintas status, persetujuan/penolakan beralasan, edit laporan `Pending`, catatan verifikasi, riwayat, dan penghapusan permanen terbatas.
6. **Konfirmasi pengembalian** — pengiriman hanya oleh User pemilik dengan nama pemilik dan foto bersama penemu–pemilik, unggah bukti privat, tinjauan Admin, persetujuan/penolakan.
7. **Siklus arsip publik** — laporan yang pengembaliannya disetujui Admin tampil di Barang Ditemukan selama 30 hari, lalu dijadwalkan untuk diarsipkan/disembunyikan; data tetap disimpan.
8. **Jejak audit** — catat keputusan penting Admin, edit, penghapusan, dan perubahan status.

## 3.1 Frontend dan arah visual
- Aplikasi Laravel berada di `backend/`; tampilan Blade dan aset sumber (CSS, JavaScript, gambar) berada di `frontend/`.
- Layout terpisah: `layouts/public.blade.php` untuk Guest/User, `layouts/admin.blade.php` untuk panel Admin, dan `layouts/admin-auth.blade.php` untuk login Admin. `layouts/app.blade.php` serta `partials/navbar.blade.php`/`footer.blade.php` hanya alias kompatibilitas dan tidak dipakai halaman baru. Partial publik dan Admin berada di `frontend/views/partials/`, halaman di `frontend/views/pages/`.
- Token desain (`--tt-*`) dan gaya komponen berada di `frontend/css/app.css`. Bootstrap 5.3 dimuat melalui Vite.
- Tautan navigasi memakai named route dan hanya ditampilkan jika rutenya sudah tersedia. State aktif Barang Hilang/Barang Ditemukan dibedakan dari parameter `type`, bukan hanya nama route.
- Beranda sengaja ringkas: daftar publik dan penjelasan peran dihapus, diganti empat kartu **Barang Hilang** terbaru dalam satu baris, masing-masing dengan tombol “Lihat detail” dan tautan “Lihat selengkapnya” ke daftar Barang Hilang.
- Kartu item di beranda dan daftar publik memakai tinggi penuh kolom Bootstrap serta `.report-card`/`.report-card-body` sebagai flex column. Tombol **Lihat detail** dibungkus `mt-auto pt-2` sehingga sejajar di bagian bawah semua kartu dalam satu baris, mengikuti kartu terpanjang.
- Dashboard Admin tidak memakai topbar: judul “Dashboard Admin” menjadi konten teratas tanpa subjudul, tombol panah kembali berada di kanan atas sejajar dengan judul, kartu status tersusun dalam satu baris pada layar lebar, dan filter status menyediakan opsi **Semua**. Label kartu status disingkat menjadi **MENUNGGU** dan **PENGEMBALIAN**, sementara filter/badge memakai label lengkap. Halaman **Tinjau laporan** memakai pola judul + tombol panah yang sama tanpa “Area Admin”.
- Sidebar Admin menyediakan menu **Cari laporan** ke halaman pencarian lintas status. Pagination Laravel dikonfigurasi memakai Bootstrap 5 di `AppServiceProvider` dan diberi gaya token desain agar Previous/Next serta nomor halaman konsisten dengan komponen lain. Panel Admin memakai sidebar penuh tinggi; hanya area konten yang digulir dan tombol keluar Admin berada di bawah.
- Teks kecil di atas judul (`eyebrow`) hanya dipakai di beranda. Halaman lain tidak memakai eyebrow; halaman **Cari laporan** tidak memakai subjudul; menu **Akun** dimulai dari kartu profil tanpa hero, judul halaman, atau peringatan verifikasi email. Halaman **Barang Hilang** dan **Barang Ditemukan** juga tidak memakai hero/judul/kalimat pembuka: elemen pertama yang terlihat adalah panel pencarian, dengan label **Cari Barang Hilang** atau **Cari Barang Ditemukan** sesuai `type`. Judul halaman tetap tersedia sebagai `<h1 class="visually-hidden">` untuk pembaca layar.
- Semua pemberitahuan hasil aksi dirender `partials/flash.blade.php` sebagai notifikasi mengambang `.tt-toast` yang tetap berada di bawah navbar. `frontend/js/app.js` menghapus tiap notifikasi setelah 3000 ms; gaya dan animasinya berada di `frontend/css/app.css` memakai token desain yang sama di ketiga layout.
- Gunakan Blade untuk halaman yang dirender server dan Bootstrap untuk tata letak/komponen responsif.
- Terapkan estetika produk startup modern: hierarki jelas, ruang kosong cukup, jarak konsisten, tipografi masa kini, warna tidak berlebihan, kartu/formulir rapi, dan ikon yang fungsional.
- Buat komponen/layout Blade yang dapat digunakan kembali untuk elemen berulang (navigasi, tombol, notifikasi, input formulir, kartu, badge, pagination, tampilan kosong).
- Desain responsif dan pendekatan mobile-first wajib. Jangan biarkan UI tampak seperti Bootstrap tanpa modifikasi.
- Pastikan aksesibilitas: markup semantik, fokus terlihat, label, dukungan keyboard, dan kontras teks yang terbaca.
## 4. Peran dan otorisasi
- **Guest:** hanya membaca konten publik yang telah disetujui dan memenuhi syarat, serta menghubungi penemu lewat tombol WhatsApp; tidak dapat mengirim laporan.
- **User:** memiliki kemampuan Guest, serta melaporkan barang temuan dan mengelola laporan miliknya lewat menu **Akun** sesuai status yang diizinkan, dan mengirim konfirmasi pengembalian hanya untuk laporan miliknya yang memenuhi syarat.
- **Admin:** akun terpisah di tabel `admins`, masuk lewat jalur tersembunyi, dan mendukung beberapa akun yang berdiri sendiri. Dapat melakukan verifikasi, mengedit `Pending`, dan pengelolaan setelah autentikasi. Nama pemilik, foto bersama, dan berkas bukti hanya dapat diakses Admin yang berwenang dan User terkait. Jangan menerapkan asumsi satu Admin.
- Guest adalah pengunjung anonim, bukan peran basis data.
- Otorisasi wajib ditegakkan di server; menyembunyikan tombol di UI saja tidak cukup. Jangan menautkan jalur login Admin dari navigasi publik.
- Pemisahan UI ditegakkan oleh rute dan middleware: situs utama memakai `main-site` (`role:guest` untuk halaman auth), halaman User memakai `role:user`, dan panel Admin memakai `role:admin`. Layout Admin tidak memuat navbar/footer publik; Admin diarahkan ke `/admin/dashboard` saat membuka halaman situs utama, sedangkan User/Guest tidak dapat masuk ke `/admin/...`.
## 5. Entitas inti domain (usulan awal)
ERD dan field final harus divalidasi dalam tugas tersendiri. Entitas yang mungkin diperlukan:
- `users`: data akun User (penemu/pelapor); kolom `role` lama tetap ada untuk kompatibilitas, tetapi bukan jalur masuk Admin.
- `admins`: akun Admin terpisah (nama, email, password ter-hash) dengan guard `admin`; beberapa akun dapat berdiri sendiri.
- `lost_reports`: User pelapor, nama barang, kategori, deskripsi publik, tanggal dan lokasi **ditemukan**, foto, **no. HP penemu**, status, timestamp pengembalian/arsip.
- `return_confirmations`: referensi laporan, User pengirim, **nama pemilik**, **foto bersama penemu–pemilik** pada storage privat, catatan opsional, status peninjauan, **Admin peninjau**, alasan penolakan wajib jika ditolak, timestamp.
- Kategori laporan menurut P0.4: Elektronik, Dompet & Uang, Dokumen & Kartu Identitas, Kunci, Tas, Pakaian & Aksesoris, Buku & Alat Tulis, dan Lainnya. (Lihat catatan selisih implementasi di bawah.)
- `report_status_histories` atau catatan audit: status sebelumnya/baru, pelaku User, **pelaku Admin**, alasan/catatan, waktu.

Hindari menyimpan data sensitif yang tidak diperlukan. NIM/NIP, email, no. HP Akun, dan data fakultas/program studi bersifat privat. Jangan tampilkan nomor HP penemu, no. HP Akun, nama pemilik, ciri unik verifikasi, atau bukti pengembalian pada halaman/endpoint publik. Nomor HP penemu hanya dipakai server untuk membuat tautan `wa.me`.
## 6. Siklus status
Alur utama yang diharapkan:
`Pending → Approved/Active (Barang Hilang) → Return Pending → Returned (Barang Ditemukan) → Archived`
- Laporan barang temuan dibuat User dan masuk **Barang Hilang** setelah disetujui Admin. Barang Ditemukan bukan alur pelaporan terpisah; hanya laporan yang status pengembaliannya disetujui Admin yang masuk daftar tersebut.
- Penolakan Admin atas laporan baru: `Pending → Rejected`, dengan alasan wajib dan kesempatan perbaikan/pengiriman ulang.
- Pembatalan oleh pemilik: `Pending → Cancelled`.
- Jika konfirmasi pengembalian ditolak, laporan tetap aktif di Barang Hilang; alasan wajib dicatat dan pemilik dapat memperbaiki serta mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
- Pemilik dapat mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung oleh pemilik. Admin dapat mengedit laporan `Pending` dan mencatat perubahannya.
- Data dan riwayat status tidak dihapus permanen, kecuali Admin melakukan penghapusan manual pada laporan `returned` atau `archived` setelah konfirmasi.
- Perubahan status harus dilakukan melalui operasi aplikasi yang eksplisit, divalidasi berdasarkan status saat ini, dan dicatat.
- Jangan pernah menampilkan laporan yang sama secara bersamaan pada kedua daftar publik.
## 7. Penyimpanan berkas
- Validasi berkas yang diunggah berdasarkan MIME/jenis yang diizinkan, ekstensi, ukuran, dan nama penyimpanan yang aman.
- Foto laporan dan bukti pengembalian disimpan pada storage lokal Laravel dengan kebijakan visibilitas terpisah.
- Foto laporan wajib 1–3 file menurut P0.4, format JPG/JPEG/PNG/WebP, maksimal 2 MB per file. Implementasi saat ini menerima satu file per laporan.
- Foto laporan tidak boleh ditampilkan publik sebelum laporan disetujui Admin.
- **Foto bersama penemu–pemilik** wajib 1–3 file JPG/JPEG/PNG/WebP, maksimal 2 MB per file, dan disimpan pada storage privat.
- Jangan simpan bukti privat di direktori yang dapat diakses publik atau membuat URL publik untuknya.
- Penghapusan permanen oleh Admin juga menghapus berkas foto laporan dan bukti privat terkait, dengan validasi path untuk mencegah path traversal.
## 8. Arsip 30 hari
- Gunakan scheduler atau perintah terjadwal yang sesuai dengan deployment, bukan proses manual saja.
- Catat timestamp resmi saat Admin menyetujui pengembalian dan laporan dipublikasikan pada Barang Ditemukan.
- Hitung batas arsip dari timestamp tersebut ditambah 30 hari. Zona waktu aplikasi: Asia/Makassar (WITA).
- Setelah batas lewat, ubah status menjadi `Archived` dan sembunyikan laporan dari daftar publik Barang Ditemukan.
- Pertahankan data laporan dan foto bukti pengembalian di database; arsip bukan penghapusan permanen.
- Pastikan proses pengarsipan aman dijalankan berulang (idempotent) dan kegagalannya dicatat.
## 9. Dasar keamanan
- Hash kata sandi menggunakan mekanisme hashing aman bawaan Laravel.
- Validasi seluruh input pengguna di server; lakukan escaping output pada template.
- Gunakan perlindungan CSRF untuk formulir web.
- Terapkan policy/middleware otorisasi dan pemeriksaan kepemilikan.
- Batasi laju permintaan (rate limit) pada autentikasi dan endpoint pengiriman yang rentan disalahgunakan jika sesuai.
- Validasi unggahan dan cegah path traversal serta penggunaan nama file dari klien secara langsung.
- Jauhkan `.env`, rahasia, bukti privat, dan cadangan data dari Git serta akses web publik.
- Terapkan hak Admin seminimal mungkin; catat tindakan verifikasi tanpa mencatat rahasia atau data pribadi yang tidak perlu.
- Jalur login Admin berasal dari konfigurasi `ADMIN_LOGIN_URI` dan tidak ditautkan pada navigasi, footer, atau halaman publik lain.
- Nomor HP penemu tidak boleh muncul pada HTML/respons publik. Kontak dibuat sebagai redirect `wa.me` di server; jangan memakai WhatsApp API, kredensial eksternal, atau layanan berbayar.
- Nama pemilik dan foto bersama hanya melalui endpoint privat yang memeriksa peran Admin atau kepemilikan User.
- Penghapusan permanen hanya untuk `returned`/`archived` dan harus melalui konfirmasi struktural di sisi server.
## 10. Strategi pengujian
- Unit test untuk aturan perubahan status/logika bisnis dan perhitungan tanggal arsip.
- Feature test untuk visibilitas Guest, autentikasi User dan Admin terpisah, jalur login tersembunyi, kepemilikan, pengiriman laporan barang temuan, kontak WhatsApp tanpa eksposur nomor, verifikasi Admin, edit `Pending`, pengiriman konfirmasi pengembalian dengan data privat, penghapusan permanen terbatas, dan visibilitas arsip.
- Pengujian unggahan untuk berkas kosong, tidak valid, terlalu besar, dan valid.
- Pengujian otorisasi untuk percobaan URL/API langsung oleh pengguna yang salah atau Guest.
- Uji proses arsip terjadwal menggunakan timestamp yang dikendalikan, tanpa menunggu 30 hari nyata.
## 11. Lingkungan dan persiapan pengembang
- Detail lingkungan lokal (versi PHP, Composer, Node, basis data) didokumentasikan di README; jangan menambahkan container atau infrastruktur lain tanpa persetujuan.
- Perbarui `.env.example` dengan nama variabel dan nilai contoh yang aman saja.
- Sediakan migration dan seeder agar setup dapat diulang; jangan membutuhkan data produksi untuk pengembangan.

## 11.1 Konvensi migration dan seeder
- Migration memakai timestamp Laravel dan satu perubahan skema yang fokus per file.
- Migration wajib memiliki `up()` dan `down()` yang dapat dibalik tanpa menghapus data di luar objek yang dikelolanya.
- Gunakan `snake_case`, foreign key, indeks, nullability, dan constraint sesuai kebutuhan relasi.
- Jangan mengubah migration yang sudah berjalan pada environment bersama. Buat migration baru untuk perubahan berikutnya.
- Seeder hanya berisi data fiktif untuk pengembangan dan harus aman dijalankan ulang.
- Gunakan `updateOrCreate` atau kunci deterministik untuk mencegah data demo berulang.
- Seeder tidak boleh memuat data produksi, rahasia, password nyata, atau foto pribadi.
- Role dan data domain baru ditambahkan bersama task yang menetapkan skema serta perilakunya.
- Seeder dapat membuat satu User demo dan satu Admin demo fiktif secara idempoten; keduanya memakai password pengembangan, bukan kredensial nyata.
## 12. Pertanyaan non-P0 yang terbuka
Keputusan P0 yang telah disetujui berlaku. Pertanyaan non-P0 yang masih terbuka:
- Pengelolaan kategori oleh Admin, atau kategori tetap saja.
- Tingkat detail lokasi ditemukan yang ditampilkan publik.
- Tanggal pengembalian dicatat otomatis dan catatan pengembalian opsional (sudah diterapkan).
- Batas foto bukti pengembalian: 1–3 file JPG/JPEG/PNG/WebP, maksimal 2 MB per file (sudah diterapkan).
- Teks ketentuan pelaporan yang wajib disetujui User.
- Apakah notifikasi status termasuk MVP.

## 13. Autentikasi yang disetujui
- Autentikasi memakai session guard `web` untuk User dan guard `admin` terpisah untuk Admin; keduanya memakai controller, Form Request, CSRF, dan hashing bawaan Laravel.
- Tidak ada starter kit atau dependensi autentikasi tambahan.
- Field registrasi User mengikuti P0.3 dan A13: nama lengkap, no. HP wajib, NIM/NIP, email, password, fakultas, dan program studi khusus mahasiswa. No. HP Akun bersifat privat, divisualisasikan pada formulir daftar/ubah profil serta kartu profil, dan tidak dirender pada halaman publik. Foto profil bersifat opsional, dapat ditambahkan saat registrasi atau diubah lewat menu **Akun**, disimpan privat pada disk `local` di direktori `profile-photos/`, dan hanya disajikan kepada pemiliknya melalui `/akun/foto-profil`. Label pendukung memakai format kurung, misalnya **Program studi (wajib diisi mahasiswa)** dan **Foto profil (opsional)**, tanpa teks bantuan tambahan di bawah pemilihan file.
- Verifikasi email tidak dipakai sebagai syarat melaporkan barang temuan atau mengirim konfirmasi (A11). Kolom `email_verified_at` tetap ada sebagai kompatibilitas data, tetapi tidak mengatur akses. Identitas kampus dicatat, tetapi tidak diverifikasi melalui database resmi kampus.
- Akun Admin disimpan di tabel `admins` melalui model `App\Models\Admin` dan provider `admins`; kolom `users.role` lama tetap ada untuk kompatibilitas data, tetapi tidak dipakai sebagai jalur masuk Admin.
- Login Admin berada di jalur yang dibaca dari `ADMIN_LOGIN_URI` (nilai contoh `/admin-login`) dan tidak ditautkan dari halaman publik.
- Baris User lama dengan `role = admin` ditolak saat login melalui `/login`; Admin hanya dapat masuk melalui form Admin tersembunyi.
- Setiap akun Admin berdiri sendiri. Sistem tidak memakai asumsi satu akun Admin.
- Rute User dilindungi middleware `auth` + `role:user`; rute Admin memakai middleware `auth:admin` khusus. Guest diarahkan ke login yang sesuai; peran yang salah menerima `403` atau redirect ke login yang tepat.
- User dan Admin tidak dapat saling mengakses area masing-masing.

## 14. Catatan selisih implementasi (2026-11-22)
Dokumen ini mencatat keputusan produk yang disetujui. Saat dokumentasi diselaraskan, ditemukan selisih berikut antara keputusan lama dan kode yang berjalan. Selisih ini **tidak** diubah diam-diam dan memerlukan tugas tersendiri bila ingin diselaraskan:
- Jumlah foto laporan: keputusan P0.4 maksimal 3 foto; implementasi menerima satu file per laporan.
- Daftar kategori: keputusan P0.4 delapan kategori; implementasi memakai lima (Elektronik, Dokumen, Aksesori, Pakaian, Lainnya).

Selisih teks lama pada kartu peran User di beranda telah selesai pada A8: bagian “Peran pengguna” dihapus. Beranda kini menampilkan empat laporan **Barang Hilang** terbaru dengan tombol “Lihat detail” dan tautan “Lihat selengkapnya” (A9).
