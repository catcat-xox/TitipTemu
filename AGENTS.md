# AGENTS.md — TitipTemu

## Misi
Bangun **TitipTemu**, website kampus/fakultas modern untuk barang hilang dan barang yang telah dikembalikan. Ikuti dokumentasi repositori dan keputusan produk yang telah disetujui. Jangan mengarang atau mengubah perilaku produk jika belum jelas.

## Dokumen yang wajib dibaca sebelum bekerja
Baca dokumen berikut sebelum mulai coding dan tinjau kembali bagian yang relevan untuk setiap tugas:
1. `README.md`
2. `docs/project_brief.md`
3. `docs/prd.md`
4. `docs/requirement.md`
5. `docs/ARCHITECTURE.md`
6. `docs/RULES.md`
7. `docs/TASKS.md`

Jika ada dokumen yang tidak ditemukan atau isinya bertentangan, laporkan perbedaannya. Jangan menyelesaikannya diam-diam dengan asumsi sendiri.


## Dasar keputusan yang telah disetujui
- Backend dan frontend yang dirender di server: aplikasi monolit Laravel.
- Basis data: MySQL.
- Frontend: Laravel Blade + Bootstrap.
- Arah visual: produk startup modern; bersih, kekinian, profesional, ramah, dan responsif. Bootstrap adalah fondasi implementasi, bukan alasan untuk memakai tampilan Bootstrap bawaan tanpa penyesuaian. Gunakan sistem desain yang konsisten (jarak, tipografi, warna, komponen) dan hindari gaya yang berubah-ubah.
- Jenis akun: User dan Admin. Guest adalah pengunjung anonim, bukan peran dalam basis data.
- Admin: boleh ada beberapa akun Admin terpisah. Jangan menganggap hanya ada satu Admin.
- Alur produk (keputusan baru A18, 2026-11-22): Guest dapat melihat dua daftar publik. **Barang Hilang** berisi laporan **kehilangan** yang dibuat User pemilik; **Barang Ditemukan** berisi laporan **temuan** yang dibuat User penemu. User wajib login untuk membuat kedua jenis laporan dan untuk konfirmasi pengembalian. Admin meninjau publikasi kedua jenis laporan serta konfirmasi pengembalian. Tiap daftar memakai dua status utama: Barang Hilang = **Barang Belum Ditemukan** / **Barang Sudah Ditemukan**; Barang Ditemukan = **Barang Belum Kembali** / **Barang Sudah Kembali**. Daftar “Sudah” menampilkan barang selama 30 hari sejak persetujuan Admin, lalu disembunyikan/diarsipkan; data tetap disimpan kecuali kebijakan retensi secara eksplisit menyetujui penghapusan.
- Keputusan lama sebelum A18 — yang menyatakan Barang Ditemukan hanya berisi riwayat pengembalian dan bukan kanal laporan temuan — **dicabut** oleh A18. Dokumen proyek masih memuat catatan historis tersebut; perlakukan A18 sebagai baseline aktif dan tandai bagian lama sebagai sudah digantikan.

## Alur kerja agent: satu tugas untuk setiap persetujuan pemilik
Pemilik akan meminta OpenCode mengerjakan tugas satu per satu. Untuk setiap tugas:
1. Periksa `git status`, branch aktif, dan file yang relevan; lindungi pekerjaan pengguna yang sudah ada.
2. Baca rincian tugas dan kriteria penerimaan di `TASKS.md`, lalu dokumen produk/teknis yang berkaitan.
3. Sebelum coding, sampaikan secara singkat ID/nama tugas, rencana, file yang kemungkinan diubah, dan cara verifikasi.
4. Kerjakan hanya tugas yang diminta dan dependensi yang diperlukan. Jangan memulai tugas berikutnya.
5. Agent boleh menjalankan perintah, menerapkan kode, menguji, mendiagnosis, dan memperbaiki bug/regresi akibat tugas ini secara mandiri. Ulangi pemeriksaan seperlunya.
6. Perbarui status tugas berdasarkan pekerjaan nyata. Tandai selesai hanya jika kriteria penerimaan dan pemeriksaan terpenuhi; catat kegagalan atau hambatan.
7. Berikan laporan penyelesaian: ID tugas, ringkasan, file yang berubah, migrasi/perubahan dependensi, tes/pemeriksaan dan hasil sebenarnya, masalah yang diketahui, serta saran tugas berikutnya.
8. **BERHENTI setelah laporan dan tunggu persetujuan/perintah eksplisit pemilik untuk memulai tugas berikutnya.** Laporan penyelesaian atau selesainya tugas bukan izin untuk melanjutkan.


## Wewenang agent dalam tugas aktif
Agent boleh mengambil keputusan implementasi berisiko rendah yang wajar dan sesuai arsitektur serta perilaku produk yang telah disetujui; memeriksa, membuat, dan mengubah file; menjalankan perintah lokal/pengujian; serta memperbaiki masalah yang muncul akibat tugas tersebut. Tidak perlu meminta persetujuan untuk detail implementasi rutin.

Minta keputusan pemilik dan hentikan bagian pekerjaan yang terdampak jika diperlukan keputusan tentang perilaku produk, persyaratan, privasi, hak akses, perubahan status, dependensi/teknologi utama, deployment/infrastruktur, layanan eksternal/kredensial, operasi penghapusan data, atau perubahan aturan pengembangan. Bagian lain dari tugas aktif boleh dilanjutkan jika tidak bergantung pada keputusan yang belum ada.

## Larangan tanpa persetujuan eksplisit
- Memulai tugas berikutnya di `TASKS.md` setelah tugas aktif.
- Mengubah cakupan produk, alur pengguna, hak akses, kebijakan privasi, retensi/aturan 30 hari, atau perubahan status yang disetujui.
- Mengganti Laravel, MySQL, Blade, atau Bootstrap maupun menambahkan framework/arsitektur utama.
- Menghapus data/file secara destruktif, menghilangkan pekerjaan pengguna, atau mereset data.
- Commit, push, force-push, merge, rebase, reset, atau berpindah branch.
- Menambahkan layanan berbayar/eksternal atau menggunakan kredensial.
- Mengubah secara besar `AGENTS.md`, `ARCHITECTURE.md`, `RULES.md`, atau `TASKS.md`.

## Git dan rahasia
- Jangan menimpa atau membuang perubahan yang belum di-commit. Periksa status sebelum dan sesudah pekerjaan.
- Jangan commit `.env`, kredensial, token, bukti privat, dump basis data, atau konfigurasi khusus mesin.
- Jangan tampilkan rahasia pada keluaran, log, tangkapan layar, data uji, atau dokumentasi.

## Definisi selesai
Tugas baru dianggap selesai jika kriteria penerimaan telah diterapkan, pemeriksaan terkait benar-benar dijalankan (atau kegagalannya dijelaskan), dokumentasi/status tugas diperbarui, dan pemilik menerima laporan yang bisa ditinjau. Setiap penyelesaian harus diakhiri dengan berhenti dan menunggu persetujuan.
