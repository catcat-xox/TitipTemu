# architecture.md — TitipTemu

**Status:** Dasar teknis MVP dan keputusan produk P0 telah disetujui; alur kerja diselaraskan dengan keputusan pemilik A18 (2026-11-22); keputusan lama sebelum A18 dicabut; pertanyaan non-P0 yang tersisa tetap terbuka.

## 1. Tujuan arsitektur
- Menjaga MVP tetap mudah dipahami dan realistis untuk tim mahasiswa pengembang open source.
- Mengutamakan kemudahan pemeliharaan dan penerapan daripada kompleksitas yang belum diperlukan.
- Menegakkan hak akses peran, kepemilikan, privasi, dan verifikasi di sisi server.
- Membuat perubahan status pada dua jenis laporan — kehilangan dan temuan — jelas dan terpisah.

## 2. Arsitektur yang disetujui
Gunakan **satu aplikasi web monolitik Laravel** dengan pemisahan MVC sesuai konvensi Laravel. Jangan menambahkan microservices untuk MVP.
- **Web routes:** definisi rute dan middleware.
- **Controllers:** koordinasi HTTP, proses awal validasi, dan pemilihan respons; jangan menumpuk logika bisnis dalam controller yang terlalu besar.
- **Form Requests** (atau objek permintaan tervalidasi yang setara): validasi permintaan dan aturan otorisasi jika sesuai.
- **Models:** relasi penyimpanan dan akses data dengan cakupan yang tepat.
- **Services/actions:** operasi bisnis seperti pengiriman laporan kehilangan/temuan, verifikasi, konfirmasi status “Sudah”, dan pengarsipan jika kompleksitasnya membutuhkan. Sejak A28, `MissingReportOwnerService` menangani pembatalan dan konfirmasi **Barang ditemukan** oleh pemilik; `MissingReportModerationService` tetap menangani verifikasi Admin dan audit edit.
- **Policies/middleware:** otorisasi peran, kepemilikan, dan Admin.
- **Views:** Laravel Blade + Bootstrap yang dirender di server (disetujui). Bangun sistem desain modern bergaya startup di atas Bootstrap; hindari tampilan yang hanya menggunakan gaya bawaan Bootstrap.
- **MySQL:** penyimpanan relasional melalui migration dan ORM Laravel.

## 3. Modul logis
1. **Penjelajahan publik** — beranda, Barang Hilang (laporan kehilangan), Barang Ditemukan (laporan temuan), dua card status per menu, pencarian/filter di dalam menu aktif, detail publik, panduan.
2. **Autentikasi dan akun** — registrasi/login User, menu **Akun** untuk memantau kedua jenis laporan (pembuatan lewat dropdown **Buat Laporan** di navbar), dan akun Admin terpisah dengan login tersembunyi.
3. **Pelaporan kehilangan** — membuat dan memvalidasi laporan pemilik dengan no. HP pemilik dan tanggal/lokasi hilang, unggah foto, disimpan di tabel laporan kehilangan yang terpisah.
4. **Pelaporan temuan** — membuat dan memvalidasi laporan penemu dengan no. HP penemu dan tanggal/lokasi ditemukan, unggah foto, disimpan di tabel `lost_reports` yang sudah ada.
5. **Kontak WhatsApp** — membuat tautan `wa.me` di server tanpa menampilkan nomor HP pemilik/penemu pada halaman/respons publik.
6. **Verifikasi Admin** — Dashboard dengan card status yang dapat diklik, menu **Barang Hilang**/**Barang Ditemukan** yang difilter menurut jenis, **Cari laporan** lintas jenis dan status, persetujuan/penolakan beralasan, edit laporan pada status menunggu verifikasi, catatan verifikasi, riwayat, dan penghapusan permanen terbatas.
7. **Konfirmasi status “Sudah”** — pengiriman oleh pembuat laporan; untuk laporan temuan memakai nama pemilik dan foto bersama penemu–pemilik yang privat, tinjauan Admin, persetujuan/penolakan. Untuk laporan kehilangan, A28 menghapus bukti/form terpisah: pemilik menandai **Barang ditemukan** langsung dan Admin tidak memverifikasi konfirmasi ini.
8. **Siklus arsip publik** — daftar “Sudah” pada kedua menu menampilkan item selama 30 hari sejak persetujuan Admin, lalu dijadwalkan untuk diarsipkan/disembunyikan; data tetap disimpan.
9. **Jejak audit** — catat keputusan penting Admin, edit, penghapusan, dan perubahan status pada kedua tabel.

## 3.1 Frontend dan arah visual
- Aplikasi Laravel berada di `backend/`; tampilan Blade dan aset sumber (CSS, JavaScript, gambar) berada di `frontend/`.
- Layout terpisah: `layouts/public.blade.php` untuk Guest/User, `layouts/admin.blade.php` untuk panel Admin, dan `layouts/admin-auth.blade.php` untuk login Admin. `layouts/app.blade.php` serta `partials/navbar.blade.php`/`footer.blade.php` hanya alias kompatibilitas dan tidak dipakai halaman baru. Partial publik dan Admin berada di `frontend/views/partials/`, halaman di `frontend/views/pages/`.
- Token desain (`--tt-*`) dan gaya komponen berada di `frontend/css/app.css`. Bootstrap 5.3 dimuat melalui Vite.
- Tautan navigasi memakai named route dan hanya ditampilkan jika rutenya sudah tersedia. State aktif Barang Hilang/Barang Ditemukan dibedakan dari parameter `type`, bukan hanya nama route. Pemetaan A18: `hilang` = **Barang Hilang** (laporan kehilangan pemilik), `ditemukan` = **Barang Ditemukan** (laporan temuan penemu). URI publik kanonik adalah `/barang/hilang` dan `/barang/ditemukan`; `/barang/lost` dan `/barang/found` melayani redirect permanen (301) disertai query string agar tautan lama tetap berfungsi.
- Beranda sengaja ringkas: daftar publik dan penjelasan peran dihapus, diganti empat kartu laporan terbaru dalam satu baris, masing-masing dengan tombol “Lihat detail”. Sejak A25, keempat kartu diambil dari laporan **kehilangan** maupun **temuan** yang sudah diverifikasi dan diurutkan dari yang paling baru, dengan tautan ke daftar masing-masing. Setelah A18, **Barang Hilang** berarti laporan **kehilangan** pemilik dan **Barang Ditemukan** berarti laporan **temuan** penemu.
- Kartu item di beranda dan daftar publik memakai tinggi penuh kolom Bootstrap serta `.report-card`/`.report-card-body` sebagai flex column. Tombol **Lihat detail** dibungkus `mt-auto pt-2` sehingga sejajar di bagian bawah semua kartu dalam satu baris, mengikuti kartu terpanjang.
- Dashboard Admin tidak memakai topbar: judul “Dashboard Admin” menjadi konten teratas tanpa subjudul, tombol panah kembali berada di kanan atas sejajar dengan judul, kartu status tersusun dalam satu baris pada layar lebar, dan filter status menyediakan opsi **Semua**. Label kartu status disingkat menjadi **MENUNGGU** dan **PENGEMBALIAN**, sementara filter/badge memakai label lengkap. Halaman **Tinjau laporan** memakai pola judul + tombol panah yang sama tanpa “Area Admin”. **A18:** kartu status dapat diklik dan filternya tetap berada dalam jenis laporan aktif; status tidak menjadi menu sidebar.
- Sidebar Admin menyediakan menu **Cari laporan** ke halaman pencarian lintas status. Pagination Laravel dikonfigurasi memakai Bootstrap 5 di `AppServiceProvider` dan diberi gaya token desain agar Previous/Next serta nomor halaman konsisten dengan komponen lain. Panel Admin memakai sidebar penuh tinggi; hanya area konten yang digulir dan tombol keluar Admin berada di bawah. **A18:** sidebar menjadi 5 menu — Dashboard, Cari laporan, Barang Hilang, Barang Ditemukan, Keluar Admin di bawah. Menu Barang Hilang/Barang Ditemukan difilter menurut jenis laporan; teks “Halo, …” di sidebar dipertahankan untuk sementara.
- Teks kecil di atas judul (`eyebrow`) hanya dipakai di beranda. Halaman lain tidak memakai eyebrow; halaman **Cari laporan** tidak memakai subjudul; menu **Akun** dimulai dari kartu profil tanpa hero, judul halaman, atau peringatan verifikasi email. Halaman **Barang Hilang** dan **Barang Ditemukan** juga tidak memakai hero/judul/kalimat pembuka. Setelah A18, elemen pertama yang terlihat adalah **2 card status** yang dapat diklik, lalu panel pencarian dengan label **Cari Barang Hilang** atau **Cari Barang Ditemukan** sesuai `type`, lalu card item. Judul halaman tetap tersedia sebagai `<h1 class="visually-hidden">` untuk pembaca layar. **A18 mengubah sebagian keputusan A15** yang menjadikan pencarian elemen pertama; card status kini mendahului pencarian.
- Navbar User memakai tombol ikon **Akun**, lalu dropdown **Buat Laporan** berisi **Laporkan Kehilangan Barang** dan **Laporkan Barang Temuan** sebagai elemen paling kanan. Tombol **Keluar** tidak ada di navbar; tombol tersebut berada di halaman **Akun** bersama **Edit profil**. Teks “Halo…” pada navbar publik dihapus. Form profil dipindah ke halaman `user.profile.edit`; menu **Akun** memakai satu baris profil/biodata dan bagian **Laporan saya** tanpa tombol buat laporan. Kolom status pada tabel **Akun** dirata-tengahkan.
- Semua pemberitahuan hasil aksi dirender `partials/flash.blade.php` sebagai notifikasi mengambang `.tt-toast` yang tetap berada di bawah navbar. `frontend/js/app.js` menghapus tiap notifikasi setelah 3000 ms; gaya dan animasinya berada di `frontend/css/app.css` memakai token desain yang sama di ketiga layout.
- Gunakan Blade untuk halaman yang dirender server dan Bootstrap untuk tata letak/komponen responsif.
- Terapkan estetika produk startup modern: hierarki jelas, ruang kosong cukup, jarak konsisten, tipografi masa kini, warna tidak berlebihan, kartu/formulir rapi, dan ikon yang fungsional.
- Buat komponen/layout Blade yang dapat digunakan kembali untuk elemen berulang (navigasi, tombol, notifikasi, input formulir, kartu, badge, pagination, tampilan kosong).
- Desain responsif dan pendekatan mobile-first wajib. Jangan biarkan UI tampak seperti Bootstrap tanpa modifikasi.
- Pastikan aksesibilitas: markup semantik, fokus terlihat, label, dukungan keyboard, dan kontras teks yang terbaca.
## 4. Peran dan otorisasi
- **Guest:** hanya membaca konten publik yang telah disetujui dan memenuhi syarat pada kedua menu, memilih card status, serta menghubungi pemilik/penemu lewat tombol WhatsApp; tidak dapat mengirim laporan.
- **User:** memiliki kemampuan Guest, serta membuat laporan **kehilangan** maupun **temuan** dan mengelola keduanya lewat menu **Akun** sesuai status yang diizinkan, dan mengirim konfirmasi status “Sudah” hanya untuk laporan miliknya yang memenuhi syarat.
- **Admin:** akun terpisah di tabel `admins`, masuk lewat jalur tersembunyi, dan mendukung beberapa akun yang berdiri sendiri. Dapat melakukan verifikasi kedua jenis laporan, mengedit laporan pada status menunggu verifikasi, dan pengelolaan setelah autentikasi. Nama pemilik, bukti privat, dan berkas terkait hanya dapat diakses Admin yang berwenang dan User terkait. Jangan menerapkan asumsi satu Admin.
- Guest adalah pengunjung anonim, bukan peran basis data.
- Otorisasi wajib ditegakkan di server; menyembunyikan tombol di UI saja tidak cukup. Jangan menautkan jalur login Admin dari navigasi publik.
- Pemisahan UI ditegakkan oleh rute dan middleware: situs utama memakai `main-site` (`role:guest` untuk halaman auth), halaman User memakai `role:user`, dan panel Admin memakai `role:admin`. Layout Admin tidak memuat navbar/footer publik; Admin diarahkan ke `/admin/dashboard` saat membuka halaman situs utama, sedangkan User/Guest tidak dapat masuk ke `/admin/...`.
## 5. Entitas inti domain (usulan awal)
ERD dan field final harus divalidasi dalam tugas tersendiri. Entitas yang mungkin diperlukan:
- `users`: data akun User (dapat bertindak sebagai pemilik atau penemu); kolom `role` lama tetap ada untuk kompatibilitas, tetapi bukan jalur masuk Admin.
- `admins`: akun Admin terpisah (nama, email, password ter-hash) dengan guard `admin`; beberapa akun dapat berdiri sendiri.
- `lost_reports`: **tabel laporan temuan yang sudah ada** — User penemu, nama barang, kategori, deskripsi publik, tanggal dan lokasi **ditemukan**, foto, **no. HP penemu**, status, timestamp konfirmasi/arsip. Dipetakan ke menu **Barang Ditemukan**.
- `missing_reports`: **tabel laporan kehilangan** (nama final ditetapkan A19): User pemilik, nama barang, kategori, deskripsi publik, tanggal dan lokasi **hilang**, foto, **no. HP pemilik (wajib)**, status, `found_at` sebagai awal 30 hari, dan `archived_at`. Dipetakan ke menu **Barang Hilang**. Tidak ada foreign key atau kolom penaut ke `lost_reports`. Riwayat statusnya memakai `missing_report_status_histories` yang juga terpisah dari `report_status_histories`.
- `return_confirmations`: bukti konfirmasi status “Sudah” untuk laporan temuan — referensi laporan, User pengirim, **nama pemilik**, **foto bersama penemu–pemilik** pada storage privat, catatan opsional, status peninjauan, **Admin peninjau**, alasan penolakan wajib jika ditolak, timestamp. **A28:** laporan kehilangan tidak memakai tabel/form konfirmasi terpisah; cukup aksi pemilik pada `missing_reports` ditambah `missing_report_status_histories`.
- Kategori laporan menurut P0.4: Elektronik, Dompet & Uang, Dokumen & Kartu Identitas, Kunci, Tas, Pakaian & Aksesoris, Buku & Alat Tulis, dan Lainnya. (Lihat catatan selisih implementasi di bawah.)
- `report_status_histories` atau catatan audit: status sebelumnya/baru, pelaku User, **pelaku Admin**, alasan/catatan, waktu; mencatat perubahan pada kedua jenis laporan.

Hindari menyimpan data sensitif yang tidak diperlukan. NIM/NIP, email, no. HP Akun, dan data fakultas/program studi bersifat privat. Jangan tampilkan nomor HP pemilik/penemu, no. HP Akun, nama pemilik, ciri unik verifikasi, atau bukti privat pada halaman/endpoint publik. Nomor HP pemilik/penemu hanya dipakai server untuk membuat tautan `wa.me`.
## 6. Siklus status
Kedua tabel memakai siklus yang setara: `Menunggu verifikasi → Aktif/Belum → Konfirmasi Sudah → Sudah → Arsip`. Konstanta internal laporan kehilangan (A19): `pending`, `approved`, `rejected`, `found_pending`, `found`, `archived`, `cancelled`; label publik tetap mengikuti A18. **A28:** untuk laporan kehilangan, transisi `approved → found` dilakukan langsung oleh pemilik, mencatat `found_at` dan riwayat `found_by_owner`, lalu 30 hari dihitung dari `found_at`; tidak ada `found_pending`, bukti, atau verifikasi Admin untuk transisi ini. Dua data demo `found_pending` lama tetap disimpan tetapi tidak dipakai untuk transisi baru dan tidak ditawarkan sebagai filter Admin jenis kehilangan. Laporan temuan tetap `approved → return_pending → returned` dengan bukti dan verifikasi Admin. Admin dapat mengedit/menghapus laporan kehilangan pada semua status (A28), sedangkan aturan lama tetap berlaku untuk laporan temuan.

| Jenis laporan | Menu | Card “Belum” | Card “Sudah” |
|---|---|---|---|
| Kehilangan (pemilik) | Barang Hilang | **Barang Belum Ditemukan** | **Barang Sudah Ditemukan** |
| Temuan (penemu) | Barang Ditemukan | **Barang Belum Kembali** | **Barang Sudah Kembali** |

- Laporan kehilangan yang disetujui masuk **Barang Hilang → Barang Belum Ditemukan**; laporan temuan yang disetujui masuk **Barang Ditemukan → Barang Belum Kembali**. Keputusan lama yang menyatakan Barang Ditemukan hanya riwayat pengembalian sudah dicabut A18.
- Klik card status mengarah ke state/status lain **di dalam menu yang sama**; tidak berpindah ke jenis laporan lain.
- Penolakan Admin atas laporan baru: `Menunggu verifikasi → Ditolak`, dengan alasan wajib dan kesempatan perbaikan/pengiriman ulang.
- Pembatalan oleh pembuat laporan: `Menunggu verifikasi → Dibatalkan`.
- Jika konfirmasi status “Sudah” ditolak, item tetap pada card “Belum” di menu yang sama; alasan wajib dicatat dan pembuat laporan dapat memperbaiki serta mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
- Pembuat laporan dapat mengedit atau membatalkan laporan pada status menunggu verifikasi; laporan yang sudah disetujui tidak dapat diedit langsung. Admin dapat mengedit laporan pada status menunggu verifikasi dan mencatat perubahannya.
- Data dan riwayat status tidak dihapus permanen, kecuali Admin melakukan penghapusan manual pada laporan selesai/arsip setelah konfirmasi.
- Perubahan status harus dilakukan melalui operasi aplikasi yang eksplisit, divalidasi berdasarkan status saat ini, dan dicatat.
- Jangan pernah menampilkan laporan yang sama secara bersamaan pada kedua daftar publik; kedua tabel tidak dicocokkan atau ditautkan.
## 7. Penyimpanan berkas
- Validasi berkas yang diunggah berdasarkan MIME/jenis yang diizinkan, ekstensi, ukuran, dan nama penyimpanan yang aman.
- Foto laporan kedua jenis dan bukti konfirmasi disimpan pada storage lokal Laravel dengan kebijakan visibilitas terpisah.
- Foto laporan wajib 1–3 file menurut P0.4, format JPG/JPEG/PNG/WebP, maksimal 2 MB per file. Implementasi saat ini menerima satu file per laporan.
- Foto laporan tidak boleh ditampilkan publik sebelum laporan disetujui Admin.
- **Foto bersama penemu–pemilik** wajib 1–3 file JPG/JPEG/PNG/WebP, maksimal 2 MB per file, dan disimpan pada storage privat. Ini berlaku untuk konfirmasi laporan temuan. Sesuai A28, laporan kehilangan tidak memakai bukti bersama; cukup aksi pemilik pada `missing_reports` dan catatan di `missing_report_status_histories`.
- Jangan simpan bukti privat di direktori yang dapat diakses publik atau membuat URL publik untuknya.
- Penghapusan permanen oleh Admin juga menghapus berkas foto laporan dan bukti privat terkait, dengan validasi path untuk mencegah path traversal.
## 8. Arsip 30 hari
- Gunakan scheduler atau perintah terjadwal yang sesuai dengan deployment, bukan proses manual saja.
- Catat timestamp resmi saat Admin menyetujui konfirmasi status “Sudah” dan laporan dipublikasikan pada daftar “Sudah” terkait.
- Hitung batas arsip dari timestamp tersebut ditambah 30 hari. Zona waktu aplikasi: Asia/Makassar (WITA).
- Berlaku untuk kedua daftar: **Barang Sudah Ditemukan** dan **Barang Sudah Kembali**.
- Setelah batas lewat, ubah status menjadi `Archived` dan sembunyikan laporan dari daftar publik; data tetap tersimpan.
- Pertahankan data laporan dan bukti privat di database; arsip bukan penghapusan permanen.
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
- Nomor HP pemilik maupun penemu tidak boleh muncul pada HTML/respons publik. Kontak dibuat sebagai redirect `wa.me` di server; jangan memakai WhatsApp API, kredensial eksternal, atau layanan berbayar.
- Nama pemilik dan bukti privat hanya melalui endpoint privat yang memeriksa peran Admin atau kepemilikan User.
- Penghapusan permanen laporan temuan hanya untuk status `returned`/`archived`; sesuai A28, laporan kehilangan dapat dihapus Admin pada semua status setelah konfirmasi. Semua penghapusan harus melalui konfirmasi struktural di sisi server.
## 10. Strategi pengujian
- Unit test untuk aturan perubahan status/logika bisnis dan perhitungan tanggal arsip.
- Feature test untuk visibilitas Guest pada kedua menu dan card status, autentikasi User dan Admin terpisah, jalur login tersembunyi, kepemilikan, pengiriman kedua jenis laporan pada tabel terpisah, kontak WhatsApp tanpa eksposur nomor, verifikasi Admin, edit laporan pada status menunggu verifikasi, pengiriman konfirmasi status dengan data privat, sidebar/menu Admin sesuai A18, penghapusan permanen terbatas, dan visibilitas arsip pada kedua daftar “Sudah”.
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
- **A28:** laporan kehilangan tidak memiliki field/bukti konfirmasi terpisah untuk transisi ke **Barang Sudah Ditemukan**; pemilik menandai langsung lewat aksi `found`, dengan batas tampil 30 hari sejak `found_at`.
- **A18, diselesaikan 2026-11-23:** URI publik kanonik `/barang/hilang` dan `/barang/ditemukan`; URI lama `/barang/lost` dan `/barang/found` menjadi redirect 301.
- **A18/A19:** nama tabel/model laporan kehilangan adalah `missing_reports` / `MissingReport`, riwayatnya `missing_report_status_histories`, dan konstanta status internal tercantum pada bagian 6.

## 13. Autentikasi yang disetujui
- Autentikasi memakai session guard `web` untuk User dan guard `admin` terpisah untuk Admin; keduanya memakai controller, Form Request, CSRF, dan hashing bawaan Laravel.
- Tidak ada starter kit atau dependensi autentikasi tambahan.
- Field registrasi User mengikuti P0.3 dan A13: nama lengkap, no. HP wajib, NIM/NIP, email, password, fakultas, dan program studi khusus mahasiswa. No. HP Akun bersifat privat, divisualisasikan pada formulir daftar/ubah profil serta kartu profil, dan tidak dirender pada halaman publik. Foto profil bersifat opsional, dapat ditambahkan saat registrasi atau diubah lewat menu **Akun**, disimpan privat pada disk `local` di direktori `profile-photos/`, dan hanya disajikan kepada pemiliknya melalui `/akun/foto-profil`. Sejak A27, URL gambar memakai version query dari `profile_photo_path` dan responsnya mengirim header anti-cache agar foto lama tidak tersimpan di browser. Label pendukung memakai format kurung, misalnya **Program studi (wajib diisi mahasiswa)** dan **Foto profil (opsional)**, tanpa teks bantuan tambahan di bawah pemilihan file.
- Verifikasi email tidak dipakai sebagai syarat melaporkan barang temuan atau mengirim konfirmasi (A11). Kolom `email_verified_at` tetap ada sebagai kompatibilitas data, tetapi tidak mengatur akses. Identitas kampus dicatat, tetapi tidak diverifikasi melalui database resmi kampus.
- Akun Admin disimpan di tabel `admins` melalui model `App\Models\Admin` dan provider `admins`; kolom `users.role` lama tetap ada untuk kompatibilitas data, tetapi tidak dipakai sebagai jalur masuk Admin.
- Login Admin berada di jalur yang dibaca dari `ADMIN_LOGIN_URI` (nilai contoh `/admin-login`) dan tidak ditautkan dari halaman publik.
- Baris User lama dengan `role = admin` ditolak saat login melalui `/login`; Admin hanya dapat masuk melalui form Admin tersembunyi.
- Setiap akun Admin berdiri sendiri. Sistem tidak memakai asumsi satu akun Admin.
- Rute User dilindungi middleware `auth` + `role:user`; rute Admin memakai middleware `auth:admin` khusus. Guest diarahkan ke login yang sesuai; peran yang salah menerima `403` atau redirect ke login yang tepat.
- User dan Admin tidak dapat saling mengakses area masing-masing.

## 14. Penyelarasan spesifikasi dan implementasi (2026-11-23)
Dokumen ini mencatat keputusan produk yang disetujui. Dua selisih lama antara keputusan P0.4 dan kode yang berjalan telah diselesaikan atas instruksi pemilik dengan memilih **dokumen mengikuti implementasi**:
- Jumlah foto laporan: **satu file per laporan**, format JPG/JPEG/PNG/WebP, maksimal 2 MB per file. Keputusan lama “maksimal 3 foto” digantikan.
- Daftar kategori: **lima pilihan tetap** — Elektronik, Dokumen, Aksesori, Pakaian, Lainnya. Keputusan lama “delapan kategori” digantikan.

Kedua aturan berlaku untuk formulir laporan kehilangan maupun laporan temuan, termasuk halaman tinjau/ubah Admin. Tidak ada perubahan kode, migrasi, atau perilaku aplikasi yang diperlukan untuk penyelarasan ini.

Selisih teks lama pada kartu peran User di beranda telah selesai pada A8: bagian “Peran pengguna” dihapus. Beranda kini menampilkan empat laporan terbaru dengan tombol “Lihat detail” (A9, disesuaikan A25). Setelah A18, “Barang Hilang” berarti laporan **kehilangan** pemilik; pemetaan lama catatan ini sudah digantikan. Sejak A25, keempat laporan beranda diambil dari kedua kanal dan diurutkan dari yang paling baru. Spesifikasi A18 telah diimplementasikan lewat A19–A28.
