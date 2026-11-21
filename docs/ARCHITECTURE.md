# ARCHITECTURE.md — TitipTemu

**Status:** Dasar teknis MVP dan keputusan produk P0 telah disetujui; pertanyaan non-P0 yang tersisa tetap terbuka.

## 1. Tujuan arsitektur
- Menjaga MVP tetap mudah dipahami dan realistis untuk tim mahasiswa pengembang open source.
- Mengutamakan kemudahan pemeliharaan dan penerapan daripada kompleksitas yang belum diperlukan.
- Menegakkan hak akses peran, kepemilikan, privasi, dan moderasi di sisi server.
- Membuat perubahan status laporan kehilangan dan konfirmasi pengembalian jelas.

## 2. Arsitektur yang disetujui
Gunakan **satu aplikasi web monolitik Laravel** dengan pemisahan MVC sesuai konvensi Laravel. Jangan menambahkan microservices untuk MVP.
- **Web routes:** definisi rute dan middleware.
- **Controllers:** koordinasi HTTP, proses awal validasi, dan pemilihan respons; jangan menumpuk logika bisnis dalam controller yang terlalu besar.
- **Form Requests** (atau objek permintaan tervalidasi yang setara): validasi permintaan dan aturan otorisasi jika sesuai.
- **Models:** relasi penyimpanan dan akses data dengan cakupan yang tepat.
- **Services/actions:** operasi bisnis seperti pengiriman laporan, moderasi, konfirmasi pengembalian, dan pengarsipan jika kompleksitasnya membutuhkan.
- **Policies/middleware:** otorisasi peran, kepemilikan, dan Admin.
- **Views:** Laravel Blade + Bootstrap yang dirender di server (disetujui). Bangun sistem desain modern bergaya startup di atas Bootstrap; hindari tampilan yang hanya menggunakan gaya bawaan Bootstrap.
- **MySQL:** penyimpanan relasional melalui migration dan ORM Laravel.

## 3. Modul logis
1. **Penjelajahan publik** — beranda, Barang Hilang, Barang Ditemukan, pencarian/filter, detail publik, panduan.
2. **Autentikasi dan akun** — registrasi, login/logout, dasar profil, perlindungan akun.
3. **Laporan kehilangan** — membuat dan memvalidasi laporan, unggah foto, daftar/detail laporan milik pengguna, status laporan.
4. **Moderasi Admin** — antrean laporan, persetujuan/penolakan dengan alasan wajib, catatan moderasi, riwayat.
5. **Konfirmasi pengembalian** — pengiriman hanya oleh pemilik, unggah bukti privat, tinjauan Admin, persetujuan/penolakan.
6. **Siklus arsip publik** — laporan kehilangan yang berhasil dikembalikan setelah konfirmasi disetujui Admin tampil di Barang Ditemukan selama 30 hari, lalu dijadwalkan untuk diarsipkan/disembunyikan; data tetap disimpan.
7. **Jejak audit** — catat keputusan penting Admin dan perubahan status.

## 3.1 Frontend dan arah visual
- Aplikasi Laravel berada di `backend/`; tampilan Blade dan aset sumber (CSS, JavaScript, gambar) berada di `frontend/`.
- Layout utama: `frontend/views/layouts/app.blade.php`. Partial bersama berada di `frontend/views/partials/`, halaman di `frontend/views/pages/`.
- Token desain (`--tt-*`) dan gaya komponen berada di `frontend/css/app.css`. Bootstrap 5.3 dimuat melalui Vite.
- Tautan navigasi memakai named route dan hanya ditampilkan jika rutenya sudah tersedia.
- Gunakan Blade untuk halaman yang dirender server dan Bootstrap untuk tata letak/komponen responsif.
- Terapkan estetika produk startup modern: hierarki jelas, ruang kosong cukup, jarak konsisten, tipografi masa kini, warna tidak berlebihan, kartu/formulir rapi, dan ikon yang fungsional.
- Buat komponen/layout Blade yang dapat digunakan kembali untuk elemen berulang (navigasi, tombol, notifikasi, input formulir, kartu, badge, pagination, tampilan kosong).
- Desain responsif dan pendekatan mobile-first wajib. Jangan biarkan UI tampak seperti Bootstrap tanpa modifikasi.
- Pastikan aksesibilitas: markup semantik, fokus terlihat, label, dukungan keyboard, dan kontras teks yang terbaca.
## 4. Peran dan otorisasi
- **Guest:** hanya membaca konten publik yang telah disetujui dan memenuhi syarat; tidak dapat mengirim laporan.
- **User:** memiliki kemampuan Guest, serta membuat dan mengelola laporan miliknya sesuai status yang diizinkan, dan mengirim konfirmasi pengembalian hanya untuk laporan miliknya yang memenuhi syarat.
- **Admin:** mendukung beberapa akun Admin; dapat melakukan moderasi dan pengelolaan setelah autentikasi. File bukti hanya dapat diakses Admin yang berwenang dan pemilik laporan sesuai kebutuhan. Jangan menerapkan asumsi satu Admin.
- Guest adalah pengunjung anonim, bukan peran basis data.
- Otorisasi wajib ditegakkan di server; menyembunyikan tombol di UI saja tidak cukup.
## 5. Entitas inti domain (usulan awal)
ERD dan field final harus divalidasi dalam tugas tersendiri. Entitas yang mungkin diperlukan:
- `users`: data akun dan peran (`user` atau `admin`).
- `lost_reports`: pemilik, nama barang, kategori tetap, deskripsi publik, tanggal kehilangan, lokasi, foto, status, timestamp.
- `return_confirmations`: referensi laporan, pemilik pengirim, tanggal/waktu pengembalian jika diperlukan, catatan jika diperlukan, file bukti privat, status peninjauan, Admin peninjau, alasan penolakan wajib jika ditolak, timestamp.
- Kategori laporan bersifat tetap: Elektronik, Dompet & Uang, Dokumen & Kartu Identitas, Kunci, Tas, Pakaian & Aksesoris, Buku & Alat Tulis, dan Lainnya.
- `report_status_histories` atau catatan audit: status sebelumnya/baru, pelaku, alasan/catatan, waktu.

Hindari menyimpan data sensitif yang tidak diperlukan. NIM/NIP, email, dan data fakultas/program studi bersifat privat. Jangan tampilkan identitas pemilik laporan, detail kontak pribadi, nomor mahasiswa, atau bukti pengembalian pada halaman/endpoint publik.
## 6. Siklus status
Alur utama yang diharapkan:
`Pending → Approved/Active (Barang Hilang) → Return Pending → Returned (Barang Ditemukan) → Archived`
- Barang Ditemukan bukan alur laporan barang temuan baru. Hanya laporan kehilangan yang status pengembaliannya disetujui Admin yang masuk daftar tersebut.
- Penolakan Admin atas laporan baru: `Pending → Rejected`, dengan alasan wajib dan kesempatan perbaikan/pengiriman ulang.
- Pembatalan oleh pemilik: `Pending → Cancelled`.
- Jika konfirmasi pengembalian ditolak, laporan tetap aktif di Barang Hilang; alasan wajib dicatat dan pemilik dapat memperbaiki serta mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
- Pemilik dapat mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung. Data dan riwayat status tidak dihapus permanen.
- Perubahan status harus dilakukan melalui operasi aplikasi yang eksplisit, divalidasi berdasarkan status saat ini, dan dicatat.
- Jangan pernah menampilkan laporan yang sama secara bersamaan pada kedua daftar publik.
## 7. Penyimpanan berkas
- Validasi berkas yang diunggah berdasarkan MIME/jenis yang diizinkan, ekstensi, ukuran, dan nama penyimpanan yang aman.
- Foto laporan dan bukti pengembalian disimpan pada storage lokal Laravel dengan kebijakan visibilitas terpisah.
- Foto laporan wajib 1–3 file, format JPG/JPEG/PNG/WebP, maksimal 2 MB per file.
- Foto laporan tidak boleh ditampilkan publik sebelum laporan disetujui Admin.
- Jangan simpan bukti privat di direktori yang dapat diakses publik atau membuat URL publik untuknya.
- Batas unggahan dan format foto laporan mengikuti keputusan P0.4; batas foto bukti pengembalian masih terbuka.
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
- Terapkan hak Admin seminimal mungkin; catat tindakan moderasi tanpa mencatat rahasia atau data pribadi yang tidak perlu.
## 10. Strategi pengujian
- Unit test untuk aturan perubahan status/logika bisnis dan perhitungan tanggal arsip.
- Feature test untuk visibilitas Guest, autentikasi, kepemilikan, pengiriman laporan, moderasi Admin, pengiriman konfirmasi pengembalian, dan visibilitas arsip.
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
## 12. Pertanyaan non-P0 yang terbuka
Keputusan P0 yang telah disetujui berlaku. Pertanyaan non-P0 yang masih terbuka:
- Pengelolaan kategori oleh Admin, atau kategori tetap saja.
- Tingkat detail lokasi yang ditampilkan publik.
- Tanggal pengembalian wajib diisi atau dicatat otomatis, serta catatan pengembalian wajib atau opsional.
- Batas format dan ukuran foto bukti pengembalian.
- Teks ketentuan pelaporan yang wajib disetujui User.
- Apakah notifikasi status termasuk MVP.

## 13. Autentikasi yang disetujui
- P2.1 memakai autentikasi manual Laravel berbasis session guard `web`.
- Registrasi, login, dan logout menggunakan controller, Form Request, middleware bawaan, CSRF, dan hashing bawaan Laravel.
- Tidak ada starter kit atau dependensi autentikasi tambahan.
- P2.1 awalnya memakai field registrasi nama, email, password, dan konfirmasi password.
- P0.3 menetapkan field registrasi nama lengkap, NIM/NIP, email, password, fakultas, dan program studi khusus mahasiswa; implementasi P2.4 menggantikan field awal tersebut.
- Email wajib diverifikasi sebelum User dapat membuat laporan. Identitas kampus dicatat, tetapi tidak diverifikasi melalui database resmi kampus.
- P2.2 memakai kolom `users.role` dengan nilai `user` atau `admin`; nilai default adalah `user`.
- Gate `user` dan `admin` memeriksa role di server melalui `User::isUser()` dan `User::isAdmin()`.
- Setiap akun Admin berdiri sendiri. Sistem tidak memakai asumsi satu akun Admin.
- P2.3 memakai middleware alias `role:user` dan `role:admin` pada route group.
- Guest diarahkan ke login oleh middleware `auth`; akun dengan role yang salah menerima `403`.
