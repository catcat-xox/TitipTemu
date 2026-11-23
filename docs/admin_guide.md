# Panduan Admin TitipTemu

Panduan ini untuk akun Admin yang disimpan terpisah dari akun User. Sistem mendukung beberapa akun Admin; setiap akun berdiri sendiri.

## 1. Masuk ke panel Admin

1. Buka jalur login Admin yang ditetapkan melalui `ADMIN_LOGIN_URI` pada environment server. Jalur ini tidak ditautkan di navbar, footer, atau halaman publik lain.
2. Masukkan email dan kata sandi Admin.
3. Setelah masuk, Anda berada di panel Admin dengan sidebar terpisah dari situs publik.

Jangan menaruh jalur login Admin pada dokumentasi publik, pesan, atau konfigurasi klien. Akun User lama dengan kolom `role = admin` bukan akun Admin dan tidak dapat dipakai untuk masuk ke panel ini.

## 2. Sidebar dan dashboard

Sidebar terdiri dari:

- **Dashboard**
- **Cari laporan**
- **Barang Hilang**
- **Barang Ditemukan**
- **Keluar Admin** di bagian bawah

Status tidak menjadi menu tersendiri. Dashboard merangkum kedua jenis laporan dan kartu status dapat diklik; filter tetap berada pada jenis laporan aktif. Menu **Barang Hilang** dan **Barang Ditemukan** difilter menurut jenis, sedangkan **Cari laporan** mencari lintas jenis dan status.

## 3. Memeriksa laporan baru

1. Buka dashboard atau menu jenis terkait, lalu pilih kartu **MENUNGGU** atau filter status menunggu pemeriksaan.
2. Buka **Tinjau** pada laporan untuk melihat detail data dan foto privat.
3. Setujui laporan agar tampil publik, atau tolak dengan alasan wajib.

Laporan yang disetujui muncul sebagai:

- **Barang Hilang → Barang Belum Ditemukan** untuk laporan kehilangan.
- **Barang Ditemukan → Barang Belum Kembali** untuk laporan temuan.

Laporan yang ditolak tidak tampil publik. Pemilik atau penemu dapat melihat alasan penolakan, memperbaiki, lalu mengirim ulang.

## 4. Mengedit laporan

- Laporan **temuan** hanya dapat diedit Admin saat statusnya masih menunggu pemeriksaan.
- Laporan **kehilangan** dapat diedit Admin pada semua status sesuai A28.

Setiap perubahan tercatat pada riwayat status, termasuk Admin yang bertindak dan status asalnya. Gunakan edit dengan sengaja karena perubahan langsung diterapkan pada data laporan.

## 5. Konfirmasi status “Sudah”

### Laporan kehilangan

Sejak A28, konfirmasi **Barang ditemukan** dilakukan langsung oleh pemilik. Tidak ada antrean konfirmasi dan tidak ada bukti yang perlu Anda periksa untuk aksi ini. Anda tetap memverifikasi publikasi awal laporan kehilangan.

Saat pemilik menekan **Barang ditemukan**, status menjadi `found`, waktu pencatatan `found_at` disimpan, dan laporan tampil di **Barang Sudah Ditemukan** selama 30 hari. Riwayat mencatat bahwa aksi dilakukan pemilik.

### Laporan temuan

Penemu mengirim nama pemilik dan foto bersama penemu–pemilik. Konfirmasi tersebut masuk antrean dan harus Anda periksa:

1. Buka detail laporan atau antrean konfirmasi dari menu terkait.
2. Periksa nama pemilik dan galeri bukti privat.
3. Setujui untuk memindahkan laporan ke **Barang Ditemukan → Barang Sudah Kembali**, atau tolak dengan alasan wajib.

Penolakan mengembalikan laporan ke **Barang Belum Kembali**. Penemu dapat mengirim ulang maksimal tiga kali; setelah itu penyelesaian dilakukan bersama tim kami dengan catatan wajib.

## 6. Data privat

Data berikut hanya dapat diakses dari panel Admin atau oleh User yang berhak:

- nomor HP pemilik dan penemu;
- foto laporan sebelum disetujui;
- nama pemilik pada konfirmasi laporan temuan;
- foto bukti pengembalian;
- riwayat verifikasi dan catatan internal.

Jangan menyalin data tersebut ke dokumen publik, pesan, atau log yang tidak perlu. Endpoint foto dan bukti memakai pengecekan akses serta header anti-cache.

## 7. Pengarsipan 30 hari

Batas tampil 30 hari dihitung otomatis:

- laporan kehilangan: sejak `found_at` saat pemilik menekan **Barang ditemukan**;
- laporan temuan: sejak persetujuan Admin atas konfirmasi pengembalian.

Command `reports:archive-returned` dan `missing-reports:archive-found` dijalankan harian pukul `00:05` WITA oleh scheduler Laravel. Setelah lewat batas, status berubah menjadi arsip dan laporan tidak tampil publik. Data, bukti, dan riwayat tetap tersimpan. Pengarsipan bukan penghapusan permanen.

Jika cron server belum dipasang, status publik tetap disembunyikan oleh batas waktu, tetapi status basis data baru berubah saat command dijalankan. Lihat `docs/deployment.md`.

## 8. Penghapusan permanen

Penghapusan permanen memerlukan konfirmasi melalui modal dalam situs dan tidak dapat dibatalkan:

- laporan **kehilangan**: dapat dihapus pada semua status setelah konfirmasi (A28);
- laporan **temuan**: hanya dapat dihapus setelah status `returned` atau `archived`.

Penghapusan juga membersihkan foto laporan, bukti pengembalian, dan riwayat status terkait. Pastikan cadangan tersedia sesuai prosedur deployment sebelum melakukan penghapusan.

## 9. Riwayat dan akuntabilitas

Sistem mencatat keputusan verifikasi, alasan penolakan, edit Admin, pembatalan atau penemuan oleh pemilik, pengarsipan, dan penghapusan. Gunakan riwayat untuk menelusuri perubahan tanpa menambah catatan pribadi yang tidak perlu.

## 10. Keluar

Pilih **Keluar Admin** pada sidebar untuk mengakhiri sesi Admin. Sesi Admin terpisah dari sesi User.
