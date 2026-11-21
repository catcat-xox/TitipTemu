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
- Gunakan teks antarmuka Bahasa Indonesia secara konsisten.
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
- Gunakan data fiktif untuk seeder/demo; jangan masukkan data pribadi mahasiswa sungguhan.
## 5. Autentikasi, otorisasi, dan Admin
- Gunakan autentikasi yang didukung framework serta hashing kata sandi.
- Dukung beberapa akun Admin, masing-masing dengan akun sendiri. Jangan mengasumsikan hanya ada satu Admin.
- Lindungi rute dan tindakan Admin di sisi server melalui middleware/policy/gate; menyembunyikan kontrol UI bukan otorisasi.
- User hanya boleh mengakses/mengelola laporan miliknya dan konfirmasi pengembalian yang memenuhi syarat.
- Guest adalah pengunjung anonim, bukan peran akun.
## 6. Unggahan dan privasi
- Validasi jenis/konten berkas, ekstensi, ukuran, dan kesalahan unggah. Jangan percaya nama file dari klien.
- Gunakan nama file yang dibuat secara aman dan cegah path traversal.
- Bukti konfirmasi pengembalian bersifat privat; jangan letakkan di penyimpanan publik atau buka URL publik.
- Jangan tampilkan nomor identitas, detail kontak pribadi, bukti privat, atau catatan moderasi di halaman publik.
- Tampilkan informasi barang secukupnya untuk membantu pencarian tanpa mempermudah klaim palsu.
## 7. Logika bisnis dan status
- Aturan bisnis dan perubahan status harus sesuai persyaratan yang disetujui.
- Validasi perubahan status di logika aplikasi/server; tolak perubahan yang tidak sah.
- Satu laporan tidak boleh muncul di kedua daftar publik sekaligus.
- Visibilitas publik harus ditentukan oleh status yang disetujui dan tanggal siklus, bukan kondisi template yang tersebar.
- Arsipkan/sembunyikan barang yang telah dikembalikan pada batas 30 hari yang telah disetujui; arsip bukan penghapusan permanen kecuali disetujui.
- Catat keputusan Admin dan perubahan status penting (peninjau, waktu, catatan jika diperlukan).
## 8. Pengujian dan kualitas
- Tambahkan/perbarui tes untuk setiap perubahan perilaku, khususnya akses peran, kepemilikan laporan, unggahan, moderasi, perubahan status, privasi, dan waktu pengarsipan.
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
- Jaga dokumentasi arsitektur dan produk tetap selaras. Jangan mengubah keputusan produk secara sepihak.
- Catat asumsi yang belum dipastikan dan hambatan dengan jelas.
## 11. Berhenti dan minta persetujuan
Hentikan bagian pekerjaan yang terdampak dan minta persetujuan pemilik sebelum mengubah perilaku produk, persyaratan, peran/hak akses, privasi, retensi, perubahan status, teknologi/dependensi utama, infrastruktur/deployment, layanan eksternal, operasi destruktif pada data/file, atau aturan agent. Perbaikan bug dan detail implementasi rutin dalam tugas aktif boleh dilakukan mandiri.
