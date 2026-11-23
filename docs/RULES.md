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
- Beranda cukup menampilkan ringkasan singkat dan empat kartu laporan terbaru dari **Barang Hilang** maupun **Barang Ditemukan** dengan tombol “Lihat detail”, bukan daftar panjang atau penjelasan peran. Urutkan dari yang paling baru; sejak A18, **Barang Hilang = laporan kehilangan pemilik** dan **Barang Ditemukan = laporan temuan penemu**.
- Tiap menu publik memakai urutan: **2 card status → pencarian → card item**. Kedua card status dapat diklik dan membawa ke state/status lain di dalam menu yang sama; jangan mengganti jenis laporan saat card diklik. **A18 mengubah sebagian A15** yang menjadikan pencarian elemen pertama; card status kini mendahului pencarian.
- Navbar User memakai tombol/ikon **Akun** lebih dulu, lalu satu dropdown **Buat Laporan** berisi **Laporkan Kehilangan Barang** dan **Laporkan Barang Temuan** di pinggir kanan. Tombol **Keluar** tidak ditampilkan di navbar; tombol tersebut berada di halaman **Akun** bersebelahan dengan **Edit profil** dalam satu baris profil/biodata. Teks “Halo…” pada navbar publik dihapus; form profil memakai halaman `user.profile.edit`. Bagian **Laporan saya** di **Akun** tidak memuat tombol buat laporan. Gunakan `aria-label`/`title` dan fokus keyboard yang terlihat pada tombol ikon.
- Kolom status, **Tanggal hilang**, dan **Tanggal ditemukan** pada tabel menu **Akun** dirata-tengahkan. Tautan **Detail** di daftar pengelolaan laporan harus berbentuk tombol dan memakai rute publik sesuai jenis: `hilang` untuk laporan kehilangan, `ditemukan` untuk laporan temuan.
- Header semua tabel dirata-tengahkan, tetapi isi sel mengikuti penjajaran semula (kiri/kanan); pengecualian A26/A27 di atas tetap berlaku. Jika satu baris memiliki lebih dari satu aksi, kumpulkan dalam satu dropdown **Aksi** agar tabel tetap muat tanpa gulir horizontal pada layar desktop.
- Pada tabel **Laporan kehilangan** di halaman **Akun**, baris `approved` memakai model campuran: **Batalkan** dan **Lihat semua** berada dalam dropdown **Aksi**, sedangkan **Barang ditemukan** tetap menjadi tombol utama terlihat di paling kanan sesuai A28. Baris dengan satu aksi tidak dibuatkan dropdown. Tabel **Laporan temuan** di **Akun** serta halaman kelola laporan tidak mengikuti pola ini.
- Di panel Admin, sidebar tetap penuh tinggi, hanya konten yang digulir, kartu status muat satu baris pada layar lebar, dan tombol panah kembali sejajar dengan judul Dashboard Admin serta **Tinjau laporan**. Judul Dashboard Admin tidak disertai subjudul. **A18:** sidebar terdiri dari 5 menu — Dashboard, Cari laporan, Barang Hilang, Barang Ditemukan, Keluar Admin di bawah. Status tidak menjadi menu; card status dapat diklik dan filternya tetap dalam jenis laporan aktif. Menu Barang Hilang/Barang Ditemukan difilter menurut jenis laporan, sedangkan **Cari laporan** tetap lintas jenis dan status. Teks “Halo, …” di sidebar dipertahankan untuk sementara; hapus hanya jika hasil akhir tampilan terasa penuh dan pemilik menyetujui.
- Label kartu status Admin cukup ringkas (**MENUNGGU**, **PENGEMBALIAN**); filter dan badge tetap memakai label lengkap. Pagination memakai view Bootstrap 5 dan gaya token desain, jangan membiarkan markup Tailwind bawaan Laravel tanpa CSS pendukung. Untuk daftar publik, pakai label sesuai A18: **Barang Belum Ditemukan / Barang Sudah Ditemukan** pada Barang Hilang, **Barang Belum Kembali / Barang Sudah Kembali** pada Barang Ditemukan.
- Gunakan istilah **verifikasi**, bukan “moderasi”, pada teks antarmuka, URI aksi Admin, dan dokumentasi. Nama kelas internal PHP yang sudah ada tidak perlu diganti hanya karena terminologi.
- Teks kecil di atas judul (`eyebrow`) hanya dipakai di beranda. Halaman lain tidak memakainya; halaman **Cari laporan** tidak memakai subjudul, dan menu **Akun** harus dimulai dari kartu profil tanpa hero atau judul halaman.
- Pemberitahuan hasil aksi wajib berupa notifikasi mengambang di bawah navbar dan hilang otomatis setelah 3 detik. Gunakan partial flash yang sama di seluruh layout, bukan alert inline yang menetap. Konfirmasi sebelum aksi juga memakai modal dalam situs (`partials.confirm-modal`), bukan dialog bawaan browser seperti `confirm()`.
- Gunakan elemen semantik yang aksesibel, label formulir, navigasi keyboard, fokus yang terlihat, dan kontras yang memadai.
- Jangan menambah navigasi, cakupan fitur, atau alur UX tanpa persetujuan.
## 3. Konvensi kode
- Ikuti konvensi penamaan Laravel, format yang kompatibel dengan PSR, dan nama yang jelas.
- Jaga controller tetap fokus; pindahkan operasi domain yang kompleks ke service/action jika memang diperlukan.
- Hindari logika bisnis berulang serta method/class yang terlalu besar.
- Gunakan named route dan helper route, bukan path yang ditulis langsung berulang kali. URI publik kanonik adalah `/barang/hilang` dan `/barang/ditemukan`; `/barang/lost` dan `/barang/found` hanya dipertahankan sebagai redirect 301 untuk tautan lama.
- Formulir laporan menerima satu foto per laporan (JPG/JPEG/PNG/WebP, maksimal 2 MB) dan lima kategori tetap: Elektronik, Dokumen, Aksesori, Pakaian, Lainnya.
- Arsip otomatis 30 hari untuk kedua jenis laporan dijalankan lewat command yang terdaftar pada scheduler Laravel; perubahan jadwal atau penambahan command harus mengikuti pola yang sama dan tetap mencatat riwayat status, bukan menghapus data.
- Komentar harus berguna; jangan menambahkan komentar yang sekadar mengulang kode.
- Jangan menambah dependensi jika masalah dapat ditangani dengan baik oleh framework atau stack yang sudah ada.
## 4. Basis data
- Migration adalah sumber utama definisi skema.
- Gunakan foreign key, indeks, constraint, nullability, dan relasi dengan benar.
- Gunakan snake_case untuk field basis data dan konvensi Laravel yang sesuai.
- Jangan menjalankan migration destruktif atau reset/drop data tanpa persetujuan eksplisit.
- Gunakan data fiktif untuk seeder/demo; jangan masukkan data pribadi mahasiswa sungguhan. Seeder demo menyediakan 10 laporan temuan fiktif dan semua seeder harus tetap idempoten jika dijalankan ulang. Untuk laporan kehilangan, `MissingReportDemoSeeder` menambah 8 data, sedangkan `WandiMissingStatusDemoSeeder` menambah 6 data per status aktif (`pending`, `approved`, `rejected`, `found`, `archived`, `cancelled`) milik akun wandi/wawan.
## 5. Autentikasi, otorisasi, dan Admin
- Gunakan autentikasi yang didukung framework serta hashing kata sandi.
- User dan Admin adalah akun terpisah. Admin disimpan di tabel `admins` dengan guard tersendiri, bukan sekadar kolom `role` pada `users`.
- Dukung beberapa akun Admin, masing-masing dengan akun sendiri. Jangan mengasumsikan hanya ada satu Admin.
- Jalur login Admin dibaca dari `ADMIN_LOGIN_URI` dan tidak boleh ditautkan dari navigasi, footer, atau halaman publik lain.
- Lindungi rute dan tindakan Admin di sisi server melalui middleware/policy/gate; menyembunyikan kontrol UI bukan otorisasi.
- User hanya boleh mengakses/mengelola laporan kehilangan dan laporan temuan miliknya, serta konfirmasi status yang memenuhi syarat.
- Jangan menjadikan verifikasi email sebagai syarat akses, pelaporan kedua jenis laporan, atau konfirmasi status (A11).
- Guest adalah pengunjung anonim, bukan peran akun.
- Akun User lama ber-`role = admin` tidak boleh dipakai sebagai jalur masuk Admin.
- Halaman situs utama (Guest/User) dan panel Admin harus memakai layout serta navigasi yang terpisah. Layout Admin dilarang menampilkan navbar/footer publik; gunakan guard `guest`/`user`/`admin` dan middleware `main-site` untuk menjaga alur tetap terpisah.
## 6. Unggahan dan privasi
- Validasi jenis/konten berkas, ekstensi, ukuran, dan kesalahan unggah. Jangan percaya nama file dari klien.
- Gunakan nama file yang dibuat secara aman dan cegah path traversal.
- Bukti konfirmasi status pada laporan temuan berupa **foto bersama penemu–pemilik** bersifat privat; jangan letakkan di penyimpanan publik atau buka URL publik. Bukti hanya dapat diakses User terkait dan Admin. **A28:** konfirmasi laporan kehilangan tidak memakai bukti/form tersendiri — User pemilik cukup menekan **Barang ditemukan** saat status `approved`.
- **Foto profil** opsional bersifat privat: simpan pada disk `local` di `profile-photos/`, validasi jenis/ukurannya, dan sajikan hanya melalui rute pemilik `/akun/foto-profil`. Jangan mempublikasikan URL foto profil atau menautkannya dari halaman publik. Gunakan version query dari `profile_photo_path` pada `<img>` dan header anti-cache pada respons gambar agar unggahan terbaru langsung tampil di **Akun**.
- **Nama pemilik**, **no. HP pemilik maupun penemu**, dan **foto bersama penemu–pemilik** tidak boleh tampil sebagai teks/HTML pada halaman atau respons publik.
- Kontak Guest ke pemilik/penemu hanya lewat redirect server ke `wa.me`. Jangan memakai WhatsApp API, kredensial eksternal, atau layanan berbayar untuk kebutuhan ini.
- No. HP Akun bersifat wajib dan privat. Simpan pada kolom `users.phone`, gunakan validasi yang sama dengan no. HP penemu, dan tampilkan hanya pada formulir daftar/ubah profil serta kartu profil pemiliknya.
- Jangan tampilkan nomor identitas, email, no. HP Akun, detail kontak pribadi, bukti privat, atau catatan verifikasi di halaman publik.
- Tampilkan informasi barang secukupnya untuk membantu pencarian tanpa mempermudah klaim palsu.
## 7. Logika bisnis dan status
- Aturan bisnis dan perubahan status harus sesuai persyaratan yang disetujui.
- Validasi perubahan status di logika aplikasi/server; tolak perubahan yang tidak sah.
- Satu laporan tidak boleh muncul di kedua daftar publik sekaligus. Laporan kehilangan dan laporan temuan disimpan di **tabel terpisah** dan tidak ditautkan/dicocokkan (A18).
- Visibilitas publik harus ditentukan oleh status yang disetujui dan tanggal siklus, bukan kondisi template yang tersebar. Klik card status hanya mengubah state/status dalam jenis laporan aktif.
- Arsipkan/sembunyikan barang pada daftar **“Sudah”** setelah 30 hari sejak persetujuan Admin. **A28:** untuk laporan kehilangan, 30 hari dihitung sejak User pemilik menekan **Barang ditemukan** (`found_at`), bukan sejak persetujuan Admin. Arsip bukan penghapusan permanen; data laporan, konfirmasi, dan riwayat status pada umumnya tidak dihapus.
- **A28:** Admin dapat mengedit dan menghapus permanen laporan **kehilangan pada semua status** setelah konfirmasi, termasuk foto privat dan riwayat status. Aturan lama “hapus hanya selesai/`archived`” tetap berlaku untuk laporan temuan; status laporan temuan lain tidak boleh dihapus.
- Pembuat laporan dapat mengedit atau membatalkan laporan pada status menunggu verifikasi. **A28:** pemilik laporan kehilangan juga dapat membatalkan status `approved` dan menandai `approved` sebagai **Barang ditemukan** secara langsung; Admin tidak perlu memeriksa konfirmasi ini. Laporan temuan yang sudah disetujui tetap tidak dapat diedit langsung oleh User.
- Admin tetap memverifikasi publikasi awal kedua jenis laporan. Admin dapat mengedit laporan kehilangan pada status apa pun; edit Admin pada laporan temuan masih dibatasi `Pending`. Penolakan laporan atau konfirmasi wajib beralasan; pengiriman ulang konfirmasi temuan maksimal 3 kali, lalu penyelesaian melalui Admin.
- Catat keputusan Admin, edit Admin beserta status asalnya, pembatalan/penemuan oleh pemilik, penghapusan permanen, dan perubahan status penting (peninjau Admin, waktu, alasan penolakan wajib, catatan lain jika diperlukan).
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
- Perlakukan **A18** sebagai baseline aktif untuk pemetaan menu, jenis laporan, tabel terpisah, card status, dan navigasi. Semua pernyataan lama yang menyatakan Barang Hilang berisi laporan temuan atau Barang Ditemukan hanya riwayat pengembalian adalah catatan historis yang sudah digantikan; tandai, jangan dibiarkan seolah masih berlaku.
- Perlakukan `CHANGELOG_ANGGOTA_2.md` sebagai log historis; jangan menimpa riwayatnya tanpa alasan yang jelas.
- Catat asumsi yang belum dipastikan dan hambatan dengan jelas.
## 11. Berhenti dan minta persetujuan
Hentikan bagian pekerjaan yang terdampak dan minta persetujuan pemilik sebelum mengubah perilaku produk, persyaratan, peran/hak akses, privasi, retensi, perubahan status, teknologi/dependensi utama, infrastruktur/deployment, layanan eksternal, operasi destruktif pada data/file, atau aturan agent. Perbaikan bug dan detail implementasi rutin dalam tugas aktif boleh dilakukan mandiri.
