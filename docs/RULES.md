# RULES.md — Aturan Pengembangan TitipTemu

Aturan ini berlaku untuk semua tugas implementasi. Ikuti `AGENTS.md`, `ARCHITECTURE.md`, PRD, dan Requirement. Jika dokumen saling bertentangan, berhenti dan laporkan; jangan menebak.

## 1. Stack dan arsitektur
- Laravel monolith + MySQL.
- Laravel Blade yang dirender server + Bootstrap.
- UI harus memiliki tampilan startup modern; Bootstrap adalah perangkat bantu, bukan template visual yang dibiarkan apa adanya.
- Jangan menambahkan React, Vue, arsitektur SPA, microservices, Docker, Redis, queue, atau infrastruktur besar lainnya kecuali disetujui atau diwajibkan oleh tugas yang telah disetujui.
- Utamakan konvensi Laravel dan komponen kecil yang mudah dirawat.
## 2. Desain/UI
- Terapkan desain mobile-first dan responsif.
- Tetapkan dan gunakan kembali token desain untuk warna, tipografi, jarak, radius, bayangan, dan keadaan komponen.
- Jaga hierarki visual agar jelas; halaman harus bersih, modern, profesional, ramah, dan konsisten.
- Gunakan komponen/layout Blade yang dapat digunakan kembali untuk navigasi, kartu, badge, formulir, notifikasi, tombol, serta tampilan kosong/memuat/error jika sesuai.
- Hindari tampilan terlalu ramai, dekorasi berlebihan, warna acak, jarak tidak konsisten, dan tampilan Bootstrap polos tanpa styling.
- Gunakan teks antarmuka Bahasa Indonesia secara konsisten dan alami. Hindari istilah internal seperti “User” atau “Guest” pada situs utama; sebut pengunjung atau pemilik barang dengan kata yang akrab.
- Jangan membuat dua tautan navigasi aktif bersamaan. Bedakan state aktif berdasarkan parameter yang relevan, misalnya `type` pada daftar Barang Hilang/Barang Ditemukan.
- Beranda cukup menampilkan ringkasan singkat dan empat kartu **Barang Hilang** terbaru dengan tombol “Lihat detail”, bukan daftar panjang atau penjelasan peran. Di panel Admin, sidebar tetap penuh tinggi, hanya konten yang digulir, kartu status muat satu baris pada layar lebar, dan tombol panah kembali sejajar dengan judul Dashboard Admin serta **Tinjau laporan**. Judul Dashboard Admin tidak disertai subjudul.
- Label kartu status Admin cukup ringkas (**MENUNGGU**, **PENGEMBALIAN**); filter dan badge tetap memakai label lengkap. Pagination memakai view Bootstrap 5 dan gaya token desain, jangan membiarkan markup Tailwind bawaan Laravel tanpa CSS pendukung.
- Gunakan istilah **verifikasi**, bukan “moderasi”, pada teks antarmuka, URI aksi Admin, dan dokumentasi. Nama kelas internal PHP yang sudah ada tidak perlu diganti hanya karena terminologi.
- Teks kecil di atas judul (`eyebrow`) hanya dipakai di beranda. Halaman lain tidak memakainya; halaman **Cari laporan** tidak memakai subjudul, dan menu **Akun** harus dimulai dari kartu profil tanpa hero atau judul halaman.
- Pemberitahuan hasil aksi wajib berupa notifikasi mengambang di bawah navbar dan hilang otomatis setelah 3 detik. Gunakan partial flash yang sama di seluruh layout, bukan alert inline yang menetap.
- Gunakan elemen semantik yang aksesibel, label formulir, navigasi keyboard, fokus yang terlihat, dan kontras yang memadai.
- Jangan menambah navigasi, cakupan fitur, atau alur UX tanpa persetujuan.
## 3. Konvensi kode
- Ikuti konvensi penamaan Laravel, format yang kompatibel dengan PSR, dan nama yang jelas.
- Jaga controller tetap fokus; pindahkan operasi domain yang kompleks ke service/action jika memang diperlukan.
- Hindari logika bisnis berulang serta method/class yang terlalu besar.
- Gunakan named route dan helper route, bukan path yang ditulis langsung berulang kali.
- Komentar harus berguna; jangan menambahkan komentar yang sekadar mengulang kode.
- Jangan menambah dependensi jika masalah dapat ditangani dengan baik oleh framework atau stack yang sudah ada.
## 4. Basis data
- Migration adalah sumber utama definisi skema.
- Gunakan foreign key, indeks, constraint, nullability, dan relasi dengan benar.
- Gunakan snake_case untuk field basis data dan konvensi Laravel yang sesuai.
- Jangan menjalankan migration destruktif atau reset/drop data tanpa persetujuan eksplisit.
- Gunakan data fiktif untuk seeder/demo; jangan masukkan data pribadi mahasiswa sungguhan. Seeder demo menyediakan 10 laporan Barang Hilang fiktif dan harus tetap idempoten jika dijalankan ulang.
## 5. Autentikasi, otorisasi, dan Admin
- Gunakan autentikasi yang didukung framework serta hashing kata sandi.
- User dan Admin adalah akun terpisah. Admin disimpan di tabel `admins` dengan guard tersendiri, bukan sekadar kolom `role` pada `users`.
- Dukung beberapa akun Admin, masing-masing dengan akun sendiri. Jangan mengasumsikan hanya ada satu Admin.
- Jalur login Admin dibaca dari `ADMIN_LOGIN_URI` dan tidak boleh ditautkan dari navigasi, footer, atau halaman publik lain.
- Lindungi rute dan tindakan Admin di sisi server melalui middleware/policy/gate; menyembunyikan kontrol UI bukan otorisasi.
- User hanya boleh mengakses/mengelola laporan miliknya dan konfirmasi pengembalian yang memenuhi syarat.
- Jangan menjadikan verifikasi email sebagai syarat akses, pelaporan, atau konfirmasi pengembalian (A11).
- Guest adalah pengunjung anonim, bukan peran akun.
- Akun User lama ber-`role = admin` tidak boleh dipakai sebagai jalur masuk Admin.
- Halaman situs utama (Guest/User) dan panel Admin harus memakai layout serta navigasi yang terpisah. Layout Admin dilarang menampilkan navbar/footer publik; gunakan guard `guest`/`user`/`admin` dan middleware `main-site` untuk menjaga alur tetap terpisah.
## 6. Unggahan dan privasi
- Validasi jenis/konten berkas, ekstensi, ukuran, dan kesalahan unggah. Jangan percaya nama file dari klien.
- Gunakan nama file yang dibuat secara aman dan cegah path traversal.
- Bukti konfirmasi pengembalian berupa **foto bersama penemu–pemilik** bersifat privat; jangan letakkan di penyimpanan publik atau buka URL publik. Bukti hanya dapat diakses User terkait dan Admin.
- **Foto profil** opsional bersifat privat: simpan pada disk `local` di `profile-photos/`, validasi jenis/ukurannya, dan sajikan hanya melalui rute pemilik `/akun/foto-profil`. Jangan mempublikasikan URL foto profil atau menautkannya dari halaman publik.
- **Nama pemilik**, **no. HP penemu**, dan **foto bersama penemu–pemilik** tidak boleh tampil sebagai teks/HTML pada halaman atau respons publik.
- Kontak Guest ke penemu hanya lewat redirect server ke `wa.me`. Jangan memakai WhatsApp API, kredensial eksternal, atau layanan berbayar untuk kebutuhan ini.
- No. HP Akun bersifat wajib dan privat. Simpan pada kolom `users.phone`, gunakan validasi yang sama dengan no. HP penemu, dan tampilkan hanya pada formulir daftar/ubah profil serta kartu profil pemiliknya.
- Jangan tampilkan nomor identitas, email, no. HP Akun, detail kontak pribadi, bukti privat, atau catatan verifikasi di halaman publik.
- Tampilkan informasi barang secukupnya untuk membantu pencarian tanpa mempermudah klaim palsu.
## 7. Logika bisnis dan status
- Aturan bisnis dan perubahan status harus sesuai persyaratan yang disetujui.
- Validasi perubahan status di logika aplikasi/server; tolak perubahan yang tidak sah.
- Satu laporan tidak boleh muncul di kedua daftar publik sekaligus.
- Visibilitas publik harus ditentukan oleh status yang disetujui dan tanggal siklus, bukan kondisi template yang tersebar.
- Arsipkan/sembunyikan barang yang telah dikembalikan pada batas 30 hari; arsip bukan penghapusan permanen. Data laporan, konfirmasi, dan riwayat status pada umumnya tidak dihapus.
- Pengecualian: Admin dapat menghapus permanen laporan berstatus `returned` atau `archived` setelah konfirmasi. Status lain tidak boleh dihapus; penghapusan mencakup data konfirmasi, riwayat, foto laporan, dan bukti privat terkait.
- User dapat mengedit atau membatalkan laporan `Pending`; Admin juga dapat mengedit laporan `Pending`. Laporan `Approved` tidak dapat diedit langsung oleh User.
- Penolakan laporan atau konfirmasi wajib beralasan; pengiriman ulang konfirmasi maksimal 3 kali, lalu penyelesaian melalui Admin.
- Catat keputusan Admin, edit `Pending`, penghapusan permanen, dan perubahan status penting (peninjau Admin, waktu, alasan penolakan wajib, catatan lain jika diperlukan).
## 8. Pengujian dan kualitas
- Tambahkan/perbarui tes untuk setiap perubahan perilaku, khususnya akses peran, kepemilikan laporan, unggahan, verifikasi, perubahan status, privasi, dan waktu pengarsipan.
- Jalankan tes terkait serta pemeriksaan format, lint, atau build yang tersedia. Laporkan hasil nyata; jangan mengaku pemeriksaan berhasil jika tidak dijalankan.
- Jangan menutupi error, melewati/menonaktifkan tes, atau melemahkan assertion hanya demi hasil hijau. Jelaskan dan minta persetujuan jika tes tidak dapat diperbaiki secara wajar dalam tugas tersebut.
- Hindari tes otomatis yang bergantung pada layanan eksternal aktif.
## 9. Git dan batas tugas
- Periksa `git status` sebelum mengubah apa pun; lindungi perubahan yang sudah ada/belum di-commit.
- Kerjakan hanya tugas yang secara eksplisit diminta pemilik. Jangan memulai tugas berikutnya otomatis.
- Jangan commit/push/merge/rebase/reset/force-push atau berpindah branch tanpa instruksi.
- Jaga perubahan tetap terfokus; jelaskan migration, dependensi baru, dan risiko.
- Perbarui `TASKS.md` hanya sesuai progres nyata tugas aktif.
- Setelah tugas selesai, berikan laporan lalu BERHENTI menunggu persetujuan pemilik.
## 10. Dokumentasi
- Perbarui README/setup jika perubahan berdampak pada instalasi, konfigurasi, perintah, atau alur kerja.
- Jaga `README.md`, `project_brief.md`, `prd.md`, `requirement.md`, `ARCHITECTURE.md`, `RULES.md`, dan `TASKS.md` tetap selaras dengan alur yang disetujui. Jangan mengubah keputusan produk secara sepihak.
- Catat keputusan baru beserta tanggal dan sumbernya di `TASKS.md`. Tandai selisih antara keputusan lama dan implementasi sebagai catatan terbuka, bukan perubahan diam-diam.
- Perlakukan `CHANGELOG_ANGGOTA_2.md` sebagai log historis; jangan menimpa riwayatnya tanpa alasan yang jelas.
- Catat asumsi yang belum dipastikan dan hambatan dengan jelas.
## 11. Berhenti dan minta persetujuan
Hentikan bagian pekerjaan yang terdampak dan minta persetujuan pemilik sebelum mengubah perilaku produk, persyaratan, peran/hak akses, privasi, retensi, perubahan status, teknologi/dependensi utama, infrastruktur/deployment, layanan eksternal, operasi destruktif pada data/file, atau aturan agent. Perbaikan bug dan detail implementasi rutin dalam tugas aktif boleh dilakukan mandiri.
