# ARCHITECTURE.md — TitipTemu

**Status:** Dasar teknis MVP telah disetujui; rincian produk yang belum diputuskan tetap terbuka.

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
4. **Moderasi Admin** — antrean laporan, persetujuan/penolakan (permintaan revisi hanya jika telah disetujui), catatan moderasi, riwayat.
5. **Konfirmasi pengembalian** — pengiriman hanya oleh pemilik, unggah bukti privat, tinjauan Admin, persetujuan/penolakan.
6. **Siklus arsip publik** — barang yang dikembalikan tampil selama 30 hari, lalu dijadwalkan untuk diarsipkan/disembunyikan; data tetap disimpan kecuali kebijakan retensi menetapkan lain.
7. **Jejak audit** — catat keputusan penting Admin dan perubahan status.

## 3.1 Frontend dan arah visual
- Aplikasi Laravel berada di `backend/`; tampilan Blade dan aset sumber (CSS, JavaScript, gambar) berada di `frontend/`.
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
- `users`: data akun dan peran (atau implementasi peran jika disetujui tim).
- `lost_reports`: pemilik, judul barang, kategori, deskripsi publik, tanggal kehilangan, lokasi, foto publik, status, timestamp.
- `return_confirmations`: referensi laporan, pemilik pengirim, tanggal/waktu pengembalian jika diperlukan, catatan jika diperlukan, file bukti privat, status peninjauan, Admin peninjau, catatan keputusan, timestamp.
- `categories`: kategori yang dapat diatur jika pengelolaan kategori oleh Admin tetap masuk MVP.
- `report_status_histories` atau catatan audit: status sebelumnya/baru, pelaku, alasan/catatan, waktu.

Hindari menyimpan data sensitif yang tidak diperlukan. Jangan tampilkan identitas pemilik laporan, detail kontak pribadi, nomor mahasiswa, atau bukti pengembalian pada halaman/endpoint publik.
## 6. Siklus status (draf)
Alur utama yang diharapkan:
`Pending → Approved/Active (Barang Hilang) → Return Pending → Returned (Barang Ditemukan) → Archived`
- Penolakan Admin atas laporan baru: `Pending → Rejected`.
- Jika konfirmasi pengembalian ditolak, laporan harus tetap aktif di Barang Hilang dan dapat dikirim ulang **hanya setelah aturan ini dikonfirmasi**.
- Aturan pembatalan/edit dan status tambahan belum ditentukan; jangan mengarang perilakunya.
- Perubahan status harus dilakukan melalui operasi aplikasi yang eksplisit, divalidasi berdasarkan status saat ini, dan dicatat.
- Jangan pernah menampilkan laporan yang sama secara bersamaan pada kedua daftar publik.
## 7. Penyimpanan berkas
- Validasi berkas yang diunggah berdasarkan MIME/jenis yang diizinkan, ekstensi, ukuran, dan nama penyimpanan yang aman.
- Foto laporan publik dan bukti pengembalian privat harus disimpan/diakses dengan kebijakan visibilitas terpisah.
- Jangan simpan bukti privat di direktori yang dapat diakses publik atau membuat URL publik untuknya.
- Batas unggahan dan format gambar yang diperbolehkan harus mengikuti kebutuhan yang telah dikonfirmasi. Nilai draf belum final sebelum disetujui.
## 8. Arsip 30 hari
- Gunakan scheduler atau perintah terjadwal yang sesuai dengan deployment, bukan proses manual saja.
- Catat timestamp resmi ketika Admin menyetujui pengembalian.
- Hitung masa kedaluwarsa menggunakan definisi yang telah disepakati (30 hari kalender atau 30×24 jam; zona waktu masih perlu diputuskan).
- Arsipkan/sembunyikan barang yang telah dikembalikan setelah masa tampil publik; secara default pertahankan riwayat basis data.
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
- Detail lingkungan lokal (versi PHP, Composer, Node, basis data, container opsional) harus didokumentasikan setelah dikonfirmasi tim.
- Perbarui `.env.example` dengan nama variabel dan nilai contoh yang aman saja.
- Sediakan migration dan seeder agar setup dapat diulang; jangan membutuhkan data produksi untuk pengembangan.
## 12. Keputusan yang menunggu konfirmasi
- Versi Laravel dan starter kit autentikasi.
- Apakah pendaftaran memerlukan verifikasi email/identitas kampus.
- Cakupan fakultas/kampus dan pengelolaan kategori.
- Field laporan, jumlah/ukuran/format foto, dan tingkat detail lokasi yang ditampilkan publik.
- Cara komunikasi antara penemu dan pelapor.
- Aturan edit/batal/revisi dan penolakan/pengiriman ulang konfirmasi pengembalian.
- Perhitungan 30 hari, zona waktu, serta kebijakan retensi/penghapusan data.
- Apakah notifikasi termasuk MVP.
