# TASKS.md — Peta Jalan Pengembangan TitipTemu


**Status:** Dasar alur kerja telah disetujui pemilik. Tugas belum dianggap selesai sampai implementasi dan pemeriksaan terverifikasi. Kerjakan satu tugas untuk setiap instruksi pemilik; berhenti di setiap titik pemeriksaan untuk menunggu persetujuan.

## Keterangan status
- `[ ]` Belum dimulai
- `[~]` Sedang dikerjakan
- `[x]` Selesai (implementasi dan pemeriksaan telah diverifikasi)
- `[!]` Terhambat / membutuhkan keputusan

## Tahap 0 — Keputusan produk dan persiapan proyek
- [ ] P0.1 Pastikan makna Barang Ditemukan (hanya barang yang dikembalikan atau laporan barang temuan terpisah).
- [ ] P0.2 Pastikan kampus/fakultas/program sasaran dan pengguna akun yang diperbolehkan.
- [ ] P0.3 Pastikan field pendaftaran dan verifikasi email/identitas kampus.
- [ ] P0.4 Pastikan field formulir laporan, kategori, batas foto, format, dan ukuran.
- [ ] P0.5 Pastikan cara kontak/privasi, edit/batal, penolakan/revisi, dan aturan pengiriman ulang konfirmasi pengembalian.
- [ ] P0.6 Pastikan cara menghitung 30 hari, zona waktu, serta kebijakan arsip/retensi data.
- [x] P0.7 Arah stack frontend dikonfirmasi: Laravel Blade + Bootstrap; tampilan modern bergaya startup. Versi Laravel dan implementasi autentikasi dipilih pada tugas setup yang sesuai berdasarkan kompatibilitas dan kemudahan pemeliharaan, tanpa mengganti stack yang disetujui.
- [~] P0.8 Tinjau/persetujuan PRD, Requirement, ARCHITECTURE, RULES, dan peta jalan ini; dasar teknis telah disetujui, pertanyaan produk yang tersisa masih terbuka.
- [x] P0.9 Periksa keadaan repositori; pertahankan riwayat Git dan dokumentasi yang ada.
- [x] P0.10 Akun Admin: beberapa Admin dapat mengelola website secara terpisah.
- [x] P0.11 Alur OpenCode: satu tugas per instruksi; agent menyelesaikan, menguji, memperbaiki tugas aktif, melaporkan, lalu berhenti dan menunggu persetujuan eksplisit pemilik sebelum tugas selanjutnya.
- [ ] P0.12 Tetapkan alur branch/PR tim yang disepakati.
## Tahap 1 — Fondasi
- [x] P1.1 Inisialisasi Laravel di repositori tanpa menimpa dokumentasi/license/konfigurasi Git yang sudah ada. Laravel 13 (framework 13.43.0) diinisialisasi; README, LICENSE, `.gitignore` lama, `AGENTS.md`, dan `docs/` dipertahankan. Struktur dua folder utama: `backend/` untuk Laravel dan `frontend/` untuk Blade serta aset sumber.
- [ ] P1.2 Konfigurasikan contoh environment, pengaturan aplikasi, dan petunjuk koneksi basis data.
- [ ] P1.3 Siapkan layout dasar, gaya responsif, navigasi, dan konvensi UI.
- [ ] P1.4 Siapkan framework pengujian dan tes dasar untuk memastikan aplikasi berjalan.
- [ ] P1.5 Tetapkan konvensi migration/seeder dan data awal pengembangan.
- [ ] P1.6 Dokumentasikan setup dan perintah pengembang yang umum di README.
## Tahap 2 — Autentikasi dan otorisasi
- [ ] P2.1 Implementasikan registrasi/login/logout dengan metode autentikasi yang disetujui.
- [ ] P2.2 Implementasikan otorisasi User/Admin dengan aman.
- [ ] P2.3 Lindungi rute Admin dan tindakan khusus User.
- [ ] P2.4 Implementasikan validasi/verifikasi akun sesuai keputusan.
- [ ] P2.5 Tambahkan tes untuk Guest, User, Admin, dan akses tanpa izin.
## Tahap 3 — Penjelajahan publik
- [ ] P3.1 Implementasikan halaman beranda publik.
- [ ] P3.2 Implementasikan daftar Barang Hilang dengan pagination dan tampilan saat data kosong.
- [ ] P3.3 Implementasikan daftar Barang Ditemukan dengan aturan tampil 30 hari (setelah kebijakan dikonfirmasi).
- [ ] P3.4 Implementasikan halaman detail laporan publik dengan field yang aman bagi privasi.
- [ ] P3.5 Tambahkan pencarian kata kunci dan filter yang disetujui.
- [ ] P3.6 Tambahkan tes rute publik dan privasi.
## Tahap 4 — Pelaporan barang hilang
- [ ] P4.1 Rancang dan implementasikan skema/migration laporan serta pengelolaan kategori.
- [ ] P4.2 Implementasikan formulir laporan dengan validasi, persetujuan ketentuan, dan unggahan gambar yang aman.
- [ ] P4.3 Pastikan laporan terkirim dengan status Pending dan tidak tampil ke publik sebelum waktunya.
- [ ] P4.4 Implementasikan daftar laporan milik User dan detail statusnya.
- [ ] P4.5 Implementasikan edit/batal/revisi yang diizinkan (setelah keputusan ditetapkan).
- [ ] P4.6 Tambahkan feature test untuk validasi, unggahan, kepemilikan, dan visibilitas.
## Tahap 5 — Moderasi laporan oleh Admin
- [ ] P5.1 Implementasikan dashboard/antrean moderasi Admin.
- [ ] P5.2 Implementasikan detail laporan dan pratinjau foto yang aman.
- [ ] P5.3 Implementasikan persetujuan/penolakan (dan permintaan revisi jika disetujui produk).
- [ ] P5.4 Catat Admin, waktu, keputusan, dan catatan/riwayat moderasi.
- [ ] P5.5 Tambahkan tes otorisasi moderasi dan perubahan status.
## Tahap 6 — Konfirmasi pengembalian
- [ ] P6.1 Rancang model data konfirmasi pengembalian dan penyimpanan bukti privat.
- [ ] P6.2 Implementasikan formulir konfirmasi pengembalian khusus pemilik dan unggah bukti yang diwajibkan.
- [ ] P6.3 Terapkan status Return Pending tanpa memindahkan laporan ke daftar publik lain sebelum waktunya.
- [ ] P6.4 Implementasikan peninjauan bukti pengembalian privat oleh Admin.
- [ ] P6.5 Setujui pengembalian: ubah laporan menjadi Returned dan pindahkan ke Barang Ditemukan.
- [ ] P6.6 Tolak pengembalian: implementasikan kebijakan penolakan/pengiriman ulang yang telah disetujui dan tampilkan umpan balik.
- [ ] P6.7 Tambahkan tes privasi, kepemilikan, unggahan, dan perubahan status.
## Tahap 7 — Siklus arsip dan audit
- [ ] P7.1 Implementasikan perhitungan kedaluwarsa 30 hari berdasarkan kebijakan yang disetujui.
- [ ] P7.2 Implementasikan job/perintah pengarsipan terjadwal dan konfigurasi scheduler.
- [ ] P7.3 Pastikan operasi arsip aman dijalankan berulang dan tercatat.
- [ ] P7.4 Pastikan data yang diarsipkan tidak tampil untuk publik, tetapi tetap disimpan sesuai kebijakan.
- [ ] P7.5 Tambahkan tes tanggal batas, zona waktu, eksekusi berulang, dan visibilitas publik.
## Tahap 8 — Penguatan dan kesiapan rilis
- [ ] P8.1 Tinjau keamanan, otorisasi, CSRF, validasi, rate limit, dan penanganan unggahan.
- [ ] P8.2 Tinjau kemudahan penggunaan responsif, aksesibilitas, dan teks Bahasa Indonesia.
- [ ] P8.3 Jalankan seluruh test suite otomatis dan perbaiki regresi.
- [ ] P8.4 Jalankan pemeriksaan format/lint/build dan dokumentasikan hasil.
- [ ] P8.5 Siapkan konfigurasi deployment dan panduan environment (tanpa memasukkan rahasia ke repositori).
- [ ] P8.6 Siapkan panduan User/Admin dan data demo.
- [ ] P8.7 Tinjau lisensi repositori dan kepatuhan lisensi aset pihak ketiga.
- [ ] P8.8 Tinjau penerimaan MVP berdasarkan PRD dan Requirement.
## Titik pemeriksaan saat ini
- Repositori berisi dokumentasi perencanaan, README, lisensi, dan metadata Git; kerangka aplikasi Laravel 13 sudah diinisialisasi pada P1.1 tanpa menimpa dokumentasi atau lisensi.
- Arah teknis yang disetujui: Laravel monolith + MySQL + Blade + Bootstrap; UI responsif bergaya startup modern; beberapa akun Admin.
- OpenCode boleh mengimplementasikan, menguji, dan memperbaiki masalah dalam tugas aktif secara mandiri, tetapi tidak boleh memulai tugas berikutnya. Pada setiap penyelesaian tugas, agent harus melapor lalu BERHENTI menunggu persetujuan pemilik.
- Keputusan produk pada Tahap 0 masih terbuka sampai pemilik mengonfirmasi. Keputusan stack/desain/Admin/alur kerja yang sudah dikonfirmasi telah ditandai selesai di atas.
## Catatan keputusan / hambatan
| Tanggal | Tugas | Keputusan atau hambatan | Penanggung jawab | Status |
|---|---|---|---|
| Belum ditentukan | P0 | Pilihan produk/teknis menunggu konfirmasi tim | Tim | Terbuka |
| 2026-11-21 | P1.1 | Pemilik menetapkan dua folder utama: `backend/` untuk aplikasi Laravel dan `frontend/` untuk view Blade serta aset sumber; Vite membangun aset ke `backend/public/build`. | Pemilik | Disetujui |
## Riwayat perubahan
| Versi | Catatan |
|---|---|
| 0.1 | Peta jalan awal agent disusun berdasarkan dokumen repositori; keputusan yang belum selesai tetap dicatat sebagai pertanyaan. |
| 0.2 | Pemilik mengonfirmasi Blade + Bootstrap, arah visual startup modern, beberapa akun Admin, dan alur OpenCode satu tugas per instruksi dengan checkpoint persetujuan eksplisit. |
| 0.3 | Seluruh isi dokumen agent diterjemahkan ke Bahasa Indonesia; nama teknis dan status tugas dipertahankan. |
| 0.4 | P1.1 selesai: kerangka Laravel 13 diinisialisasi, dokumentasi/lisensi/konfigurasi Git lama dipertahankan, dan pemeriksaan aplikasi lulus. |
