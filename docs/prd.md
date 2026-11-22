# PRODUCT REQUIREMENTS DOCUMENT (PRD) — TitipTemu

**Versi:** 2.10 (alur pelaporan barang temuan, Admin terpisah, privasi kontak, penyempurnaan UI A11, dan no. HP Akun yang wajib diselaraskan; verifikasi email dihapus)

**Status:** Disetujui untuk MVP; pertanyaan non-P0 yang tersisa dicatat terbuka

**Produk:** Website TitipTemu

**Tujuan:** Pelaporan barang temuan oleh User, pencarian publik oleh Guest, dan dokumentasi barang yang telah dikembalikan

**Lingkup:** Seluruh lingkungan kampus

**Stack acuan:** Laravel, MySQL, Blade, Bootstrap


## 1. Ringkasan Produk

TitipTemu adalah website yang membantu civitas akademika melaporkan barang yang ditemukan dan mencari barang yang sedang dicari di lingkungan institusi. User yang menemukan barang menyampaikan laporan lengkap dengan data penemu dan foto. Informasi yang telah disetujui Admin ditampilkan kepada publik agar pemilik dapat menemukannya dan menghubungi penemu.

TitipTemu menyediakan dua halaman publik utama: **Barang Hilang** dan **Barang Ditemukan**. Barang Hilang berisi laporan barang temuan yang telah disetujui Admin dan masih menunggu diambil pemiliknya. Barang Ditemukan hanya berisi laporan yang pengembaliannya telah disetujui Admin setelah dikonfirmasi oleh User. Barang Ditemukan bukan daftar pelaporan barang temuan terpisah. Item pada halaman Barang Ditemukan ditampilkan selama 30 hari, kemudian tidak lagi tampil secara publik.

Guest dapat melihat dan mencari informasi tanpa login, serta menghubungi penemu lewat tombol WhatsApp yang mengarahkan ke `wa.me` tanpa menampilkan nomor HP. User harus memiliki akun dan login untuk melaporkan barang temuan serta mengirim konfirmasi pengembalian; verifikasi email tidak diperlukan. Admin memakai akun terpisah dan jalur login tersembunyi; Admin memeriksa laporan dan konfirmasi sebelum mengubah status/publikasi.

## 2. Masalah yang ingin diselesaikan
- Informasi barang temuan sering tersebar di grup percakapan, media sosial, atau pengumuman.
- Penemu kesulitan menjangkau pemilik barang, dan pemilik kesulitan menemukan informasi yang tersebar.
- Informasi barang temuan tidak tersimpan dalam daftar terpusat dan mudah ditelusuri.
- Informasi publik perlu diverifikasi agar laporan tidak menyesatkan atau mengandung data pribadi berlebihan.
- Status barang dan proses pengembaliannya perlu tercatat dengan jelas tanpa membocorkan bukti privat.

## 3. Tujuan Produk
- Memusatkan laporan barang temuan dalam satu website untuk seluruh lingkungan kampus.
- Memudahkan mahasiswa, dosen, dan staf aktif melaporkan barang temuan dengan data penemu, format terstruktur, dan foto.
- Memungkinkan publik melihat, mencari, dan menghubungi penemu lewat WhatsApp tanpa akun dan tanpa melihat nomor HP.
- Memastikan laporan dipublikasikan setelah persetujuan Admin.
- Menyediakan proses konfirmasi pengembalian dengan nama pemilik dan foto bersama yang privat, serta pemeriksaan Admin.
- Menyediakan akun Admin terpisah dengan jalur masuk tersembunyi dan beberapa akun Admin yang berdiri sendiri.
- Menyediakan riwayat status bagi User dan Admin.

## 4. Pengguna dan peran
### 4.1 Guest
- Melihat beranda, Barang Hilang, Barang Ditemukan, detail publik, pencarian/filter, dan informasi panduan.
- Menghubungi penemu lewat tombol WhatsApp pada detail publik tanpa melihat nomor HP penemu.
- Tidak dapat membuat laporan atau mengonfirmasi pengembalian.
### 4.2 User (penemu/pelapor)
- User adalah mahasiswa, dosen, atau staf aktif kampus yang memiliki akun.
- Mendaftar dan login.
- Melaporkan barang yang ditemukan: mengisi no. HP penemu, data barang, tanggal dan lokasi ditemukan, mengunggah foto, menyetujui ketentuan.
- Melihat laporan dan status miliknya melalui menu **Akun**.
- Memperbaiki dan mengirim ulang laporan yang ditolak setelah membaca alasan Admin.
- Mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung.
- Mengajukan konfirmasi barang telah kembali pada laporan miliknya dengan mengisi **nama pemilik** dan mengunggah **foto bersama penemu–pemilik**.
- Melihat status pemeriksaan konfirmasi pengembalian.
### 4.3 Admin
- Sistem mendukung beberapa akun Admin, masing-masing dengan akun sendiri di tabel `admins`, terpisah dari akun User.
- Login ke area Admin melalui jalur tersembunyi yang ditentukan oleh `ADMIN_LOGIN_URI`; tidak ada tautan Admin pada navigasi publik.
- Memeriksa, menyetujui, atau menolak laporan dengan alasan wajib saat menolak.
- Mengedit langsung laporan berstatus `Pending` dan mencatat perubahan pada riwayat.
- Memeriksa bukti pengembalian yang privat, menyetujui atau menolak konfirmasi dengan alasan wajib saat menolak.
- Mengelola publikasi, arsip, dan aktivitas verifikasi.
- Menghapus permanen laporan berstatus `returned` atau `archived` setelah konfirmasi; data lain tidak dihapus.

### 4.4 Data pendaftaran User
- Nama lengkap — wajib.
- NIM/NIP — wajib dan privat.
- Email — wajib, unik, dan tidak memerlukan verifikasi (ketentuan lama dicabut pada A11).
- No. HP — wajib dan privat; diisi saat pendaftaran dan dapat diubah di menu **Akun**, tidak ditampilkan pada halaman publik. Label formulir cukup **No. HP** tanpa keterangan tambahan.
- Password — wajib dan disimpan dengan hashing aman.
- Fakultas — wajib dan privat.
- Program studi — wajib untuk mahasiswa; tidak berlaku untuk dosen dan staf.
- Sistem mencatat identitas kampus, tetapi tidak melakukan pencocokan dengan database resmi kampus.

## 5. Prinsip produk
1. Publik dapat menemukan informasi tanpa harus membuat akun.
2. Laporan baru tidak otomatis dipublikasikan.
3. Informasi yang ditampilkan cukup membantu pencarian, tetapi tidak membocorkan detail sensitif yang dapat memicu klaim palsu.
4. Foto dan data pribadi hanya digunakan sesuai tujuan sistem dan aksesnya dibatasi.
5. Perubahan status penting dicatat.
6. Pengembalian barang harus dikonfirmasi oleh pelapor dan diperiksa Admin.

## 6. Navigasi
### 6.1 Publik
- Beranda
- Barang Hilang
- Barang Ditemukan (riwayat laporan yang pengembaliannya disetujui)
- Detail laporan dengan tombol WhatsApp
- Cari/filter
- Panduan / Ketentuan
- Login
- Daftar
### 6.2 User
- Beranda
- Barang Hilang
- Barang Ditemukan
- Laporkan Barang Temuan
- **Akun** (aktivitas, laporan, dan status milik User)
- Detail Laporan Saya
- Konfirmasi Barang Telah Kembali
- Logout
### 6.3 Admin
- Login tersembunyi melalui `ADMIN_LOGIN_URI` (tidak ditautkan di navigasi publik)
- Dashboard dengan filter status
- Laporan menunggu verifikasi
- Laporan aktif / Barang Hilang
- Konfirmasi pengembalian menunggu verifikasi
- Barang Ditemukan / riwayat pengembalian
- Detail laporan, edit `Pending`, bukti privat, dan riwayat aktivitas
- Penghapusan permanen untuk laporan `returned`/`archived`
- Logout

## 7. Alur utama
### 7.1 Browsing sebagai Guest
1. Guest membuka TitipTemu.
2. Guest melihat dua navigasi utama: Barang Hilang dan Barang Ditemukan.
3. Guest membuka daftar, melakukan pencarian/filter, lalu membuka detail.
4. Guest menekan tombol WhatsApp; sistem mengarahkan ke `wa.me` tanpa menampilkan nomor penemu di halaman publik.
5. Jika ingin melaporkan barang temuan, sistem mengarahkan Guest ke login/registrasi.
### 7.2 Membuat laporan barang temuan
1. User login.
2. User membuka formulir **Laporkan Barang Temuan**.
3. User mengisi no. HP penemu, data barang, tanggal dan lokasi ditemukan, mengunggah foto, dan menyetujui ketentuan.
4. Sistem memvalidasi input.
5. Laporan dibuat berstatus `Pending` dan belum tampil publik.
6. Admin meninjau laporan; Admin dapat mengedit laporan `Pending` sebelum memutuskan.
7. Admin menyetujui atau menolak dengan alasan yang wajib dicatat; User dapat memperbaiki dan mengirim ulang laporan yang ditolak.
8. Laporan yang disetujui tampil pada Barang Hilang beserta tombol WhatsApp.
### 7.3 Konfirmasi pengembalian
1. User pemilik laporan membuka laporan miliknya yang masih aktif melalui menu **Akun**.
2. User memilih konfirmasi barang telah kembali.
3. User mengisi **nama pemilik**, mengunggah **foto bersama penemu–pemilik**, dan menyetujui pernyataan pengembalian; catatan bersifat opsional.
4. Sistem menyimpan permintaan konfirmasi dengan status `Return Pending`; data tersebut tidak publik.
5. Admin memeriksa informasi dan bukti melalui halaman privat.
6. Admin menyetujui atau menolak dengan alasan wajib saat menolak.
7. Jika disetujui, laporan tidak lagi muncul pada Barang Hilang dan masuk Barang Ditemukan.
8. Jika ditolak, laporan tetap aktif di Barang Hilang; User dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
9. Barang Ditemukan ditampilkan selama 30 hari sejak Admin menyetujui pengembalian dan mempublikasikannya, lalu diarsipkan/tidak ditampilkan kepada publik secara otomatis. Data tetap disimpan dan foto bukti pengembalian tetap privat.
10. Admin dapat menghapus permanen laporan `returned`/`archived` setelah konfirmasi, termasuk foto dan bukti privatnya.
## 8. Fitur MVP
### Must Have
- Halaman publik/Guest.
- Daftar Barang Hilang.
- Daftar Barang Ditemukan berdasarkan laporan yang pengembaliannya disetujui Admin.
- Detail laporan dengan tombol WhatsApp tanpa eksposur nomor penemu.
- Search dasar.
- Registrasi dan login User; menu **Akun** untuk aktivitas User.
- Login Admin terpisah melalui jalur tersembunyi.
- Form **Laporkan Barang Temuan** dengan no. HP penemu dan foto wajib.
- Persetujuan ketentuan pelaporan.
- Verifikasi laporan oleh Admin, termasuk edit laporan `Pending`.
- Dashboard/riwayat laporan milik User.
- Form konfirmasi pengembalian dengan nama pemilik dan foto bersama penemu–pemilik.
- Bukti pengembalian privat; hanya User terkait dan Admin yang dapat melihatnya.
- Verifikasi konfirmasi pengembalian oleh Admin.
- Perpindahan status/list setelah konfirmasi disetujui.
- Pengarsipan otomatis dari daftar publik Barang Ditemukan setelah 30 hari.
- Penghapusan permanen manual oleh Admin untuk laporan `returned`/`archived` setelah konfirmasi.
- Pembatasan akses dan perlindungan data.
## 9. Data laporan barang temuan
Field formulir yang ditetapkan:
- Nama penemu (tercatat dari akun User yang login)
- **No. HP penemu (wajib)** — hanya dipakai untuk tombol WhatsApp, tidak ditampilkan sebagai teks di halaman publik maupun di HTML
- Nama barang (wajib)
- Kategori (wajib)
- Deskripsi umum (wajib; jangan memuat seluruh ciri unik yang dapat digunakan untuk verifikasi)
- **Tanggal ditemukan (wajib)**
- **Lokasi ditemukan (wajib)**
- Foto barang (wajib; maksimal 3 foto menurut keputusan P0.4)
- Format foto: JPG, JPEG, PNG, atau WebP
- Ukuran maksimal: 2 MB per foto
- Foto disimpan pada storage lokal Laravel dan hanya ditampilkan publik setelah laporan disetujui Admin
- Pernyataan persetujuan ketentuan pelaporan barang temuan (wajib)

**Catatan selisih implementasi (2026-11-22):** implementasi saat ini menerima satu file foto per laporan (`required|image|mimes:jpg,jpeg,png,webp|max:2048`) dan memakai lima pilihan kategori (Elektronik, Dokumen, Aksesori, Pakaian, Lainnya). Keputusan P0.4 tentang maksimal 3 foto dan delapan kategori tetap tercatat di bawah; penyelarasan implementasi memerlukan tugas tersendiri.

Kategori tetap:
- Elektronik
- Dompet & Uang
- Dokumen & Kartu Identitas
- Kunci
- Tas
- Pakaian & Aksesoris
- Buku & Alat Tulis
- Lainnya
## 10. Data konfirmasi pengembalian
- **Nama pemilik (wajib)** — privat, hanya untuk verifikasi User terkait dan Admin
- **Foto bersama penemu–pemilik (wajib, 1–3 file)** — JPG/JPEG/PNG/WebP, maksimal 2 MB per file, disimpan pada storage privat Laravel
- Catatan singkat pengembalian (opsional; jangan memuat kontak pribadi)
- Waktu pengembalian dicatat otomatis saat Admin menyetujui
- Checkbox pernyataan bahwa barang sudah diterima oleh pemiliknya (wajib)
- Status verifikasi Admin, Admin peninjau, dan waktu peninjauan
- Alasan Admin (wajib jika ditolak)
- Penyelesaian langsung oleh Admin dengan catatan wajib setelah batas 3 pengiriman tercapai
## 11. Aturan bisnis
- BR-01: Sistem berlaku untuk seluruh lingkungan kampus.
- BR-02: Guest dapat melihat data publik dan menghubungi penemu lewat WhatsApp tanpa login.
- BR-03: User wajib login untuk melaporkan barang temuan dan mengonfirmasi pengembalian; verifikasi email tidak diperlukan.
- BR-04: Laporan barang temuan wajib memiliki foto, no. HP penemu, dan data wajib lengkap.
- BR-05: Laporan baru berstatus `Pending` dan tidak tampil publik. Laporan `Approved` dan `Return Pending` dapat tampil di Barang Hilang; status lain tidak.
- BR-06: Nomor HP penemu tidak pernah ditampilkan sebagai teks pada halaman atau respons publik; kontak hanya melalui redirect `wa.me` yang dibuat server.
- BR-07: Hanya User pemilik laporan yang dapat mengajukan konfirmasi pengembalian untuk laporannya.
- BR-08: Konfirmasi pengembalian wajib memiliki nama pemilik, foto bersama penemu–pemilik, dan persetujuan pernyataan.
- BR-09: Nama pemilik dan foto bersama tidak ditampilkan kepada publik; keduanya dapat diakses oleh User terkait dan Admin.
- BR-10: Konfirmasi pengembalian menunggu pemeriksaan Admin.
- BR-11: Setelah konfirmasi disetujui, laporan berpindah dari Barang Hilang ke Barang Ditemukan.
- BR-12: Barang Ditemukan ditampilkan selama 30 hari sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya pada daftar tersebut, menggunakan zona waktu Asia/Makassar (WITA). Setelah batas itu, laporan tidak tampil di daftar publik dan berstatus `Archived`.
- BR-13: Pengarsipan 30 hari tidak menghapus data secara permanen. Data laporan tetap disimpan di database dan foto bukti pengembalian tetap privat.
- BR-14: Admin wajib menyertakan alasan saat menolak laporan maupun konfirmasi pengembalian.
- BR-15: Pemilik dapat mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung oleh pemilik.
- BR-16: Admin dapat mengedit langsung laporan berstatus `Pending`; perubahan Admin dicatat pada riwayat status dengan peninjau Admin.
- BR-17: Penolakan laporan wajib memiliki alasan. Pemilik dapat memperbaiki dan mengirim ulang laporan yang ditolak.
- BR-18: Penolakan konfirmasi pengembalian wajib memiliki alasan. Pemilik dapat memperbaiki dan mengirim ulang maksimal 3 kali; setelah batas tercapai, penyelesaian dilakukan melalui Admin.
- BR-19: Data laporan, data konfirmasi, dan riwayat status tidak dihapus permanen, kecuali Admin melakukan penghapusan manual sesuai BR-20.
- BR-20: Admin dapat menghapus permanen laporan berstatus `returned` atau `archived` setelah konfirmasi; penghapusan mencakup data konfirmasi, riwayat status, foto laporan, dan bukti privat terkait. Status lain tidak dapat dihapus.
- BR-21: Akun Admin terpisah dari akun User, disimpan di tabel `admins`, dan masuk melalui jalur tersembunyi yang tidak ditautkan pada navigasi publik. Beberapa akun Admin dapat berdiri sendiri.
## 12. Status dan transisi
### 12.1 Status laporan
- `Pending`: menunggu pemeriksaan Admin.
- `Approved / Lost Active`: disetujui dan ditampilkan pada Barang Hilang.
- `Rejected`: ditolak Admin.
- `Return Pending`: User mengirim konfirmasi pengembalian, menunggu Admin; laporan tetap tampil di Barang Hilang sampai keputusan Admin.
- `Returned`: pengembalian disetujui; ditampilkan pada Barang Ditemukan.
- `Archived`: masa tampil 30 hari berakhir; tidak tampil publik; data dan riwayat tetap disimpan.
- `Cancelled`: laporan berstatus `Pending` dibatalkan oleh pemilik.
### 12.2 Alur transisi dasar
`Pending → Approved/Lost Active → Return Pending → Returned → Archived`
Penolakan laporan: `Pending → Rejected`, dengan alasan wajib.

Penolakan konfirmasi pengembalian: `Return Pending → Approved/Lost Active`; alasan wajib dicatat dan pengiriman ulang maksimal 3 kali, lalu penyelesaian melalui Admin.

Pembatalan: `Pending → Cancelled` oleh pemilik.
## 13. Informasi publik dan privasi
### Publik dapat melihat
- Nama barang, kategori, deskripsi yang telah diverifikasi.
- Foto laporan yang disetujui dan aman ditampilkan.
- Tanggal dan lokasi ditemukan; tingkat detail lokasi masih terbuka.
- Status laporan dan tombol WhatsApp untuk menghubungi penemu.
### Tidak ditampilkan publik
- NIM/nomor identitas mahasiswa.
- Email pribadi, alamat, dan data akun.
- **Nomor HP penemu** — tidak muncul sebagai teks/HTML; hanya dipakai server untuk membuat tautan `wa.me`.
- **Nama pemilik** dan **foto bersama penemu–pemilik** — hanya untuk verifikasi User terkait dan Admin.
- Detail unik barang yang sengaja disimpan sebagai bahan verifikasi.
## 14. Kebutuhan nonfungsional
- NFR-01: Halaman publik responsif pada desktop dan mobile.
- NFR-02: Password disimpan dengan hashing yang aman.
- NFR-03: Input divalidasi di server.
- NFR-04: Akses User dan Admin dibatasi dengan autentikasi dan otorisasi.
- NFR-05: File upload divalidasi MIME/format, ukuran, dan akses.
- NFR-06: Bukti pengembalian tidak dapat diakses publik secara langsung.
- NFR-07: Perubahan status laporan dan keputusan Admin tercatat.
- NFR-08: Sistem menyediakan backup database yang dapat dijalankan pengelola.
- NFR-09: Antarmuka mudah dipahami dan navigasi utama konsisten.
- NFR-10: Struktur Laravel mudah dipelihara dan dikembangkan.
- NFR-11: Target awal waktu muat beranda ≤3 detik dan pencarian ≤2 detik pada kondisi uji yang disepakati (target, bukan jaminan produksi).
## 15. KPI / kriteria keberhasilan MVP
- Guest dapat melihat dan mencari Barang Hilang dan Barang Ditemukan tanpa login.
- Guest dapat menghubungi penemu lewat tombol WhatsApp tanpa melihat nomor HP di halaman publik.
- Guest tidak dapat membuat laporan atau konfirmasi pengembalian.
- User dapat mendaftar, login, dan mengirim laporan barang temuan lengkap dengan no. HP penemu dan foto.
- Laporan tidak tampil sebelum persetujuan Admin.
- Admin terpisah masuk lewat jalur tersembunyi dan dapat menyetujui/menolak laporan dengan alasan wajib saat menolak.
- Admin dapat mengedit laporan `Pending`, tetapi tidak mengedit langsung laporan `Approved`.
- User hanya dapat mengirim konfirmasi untuk laporan miliknya.
- Konfirmasi pengembalian wajib memuat nama pemilik, foto bersama penemu–pemilik, dan pernyataan.
- Nama pemilik dan foto bersama tidak terbuka untuk Guest.
- Admin dapat menyetujui/menolak konfirmasi pengembalian.
- Laporan yang disetujui pengembaliannya berpindah daftar dengan benar.
- Item Barang Ditemukan tidak lagi tampil publik setelah 30 hari, dengan data tetap tersimpan.
- Admin dapat menghapus permanen laporan `returned`/`archived` setelah konfirmasi; status lain tidak dapat dihapus.
## 16. Risiko dan mitigasi
| Risiko | Mitigasi |
|---|---|
| Laporan palsu/spam | Login, validasi, verifikasi Admin, batas frekuensi jika diperlukan |
| Foto atau data tidak pantas | Verifikasi dan ketentuan upload |
| Bukti pengembalian palsu | Admin memeriksa bukti; keputusan status tidak otomatis |
| Informasi sensitif terpublikasi | Minimalkan field publik dan batasi akses file |
| Admin lambat memeriksa | Dashboard antrean dan penanda pending |
| Data hilang | Backup database dan file foto |
| Postingan tetap muncul melewati 30 hari | Job terjadwal untuk mengubah visibilitas/arsip, bukan mengandalkan penghapusan manual |
## 17. Pertanyaan non-P0 yang terbuka
Keputusan P0 yang telah disetujui berlaku. Pertanyaan yang masih terbuka:
1. Pengelolaan kategori oleh Admin, atau kategori tetap saja.
2. Tingkat detail lokasi ditemukan yang ditampilkan publik.
3. Tanggal pengembalian dicatat otomatis dan catatan pengembalian opsional (keputusan implementasi yang sudah diterapkan).
4. Batas foto bukti pengembalian: 1–3 file JPG/JPEG/PNG/WebP, maksimal 2 MB per file (sudah diterapkan).
5. Teks ketentuan pelaporan yang wajib disetujui User.
6. Apakah notifikasi status termasuk MVP.

Lihat juga “Pertanyaan konfirmasi kelompok” di akhir dokumen.
## 18. Batasan teknis awal
- Website, bukan aplikasi mobile native.
- Laravel, MySQL, Blade, dan Bootstrap mengikuti keputusan P0.7 dan baseline teknis yang disetujui.
- Guest/public read-only.
- Akun User dan Admin.
- Belum ada SSO kampus.
- Foto laporan dan bukti pengembalian disimpan dengan kontrol akses.
## 19. Rencana kerja
1. Finalisasi PRD dan Requirement.
2. Tentukan flow, status, form, ketentuan dan wireframe.
3. Rancang ERD, skema database, dan API/route.
4. Implementasi autentikasi dan role.
5. Implementasi publikasi laporan dan dashboard Admin.
6. Implementasi konfirmasi pengembalian serta pengarsipan 30 hari.
7. Pengujian, dokumentasi, demo, dan evaluasi.
## 20. Dokumen proyek terkait
- `PRD.md`: tujuan, ruang lingkup, pengguna, fitur, alur produk.
- `REQUIREMENT.md`: requirement fungsional/nonfungsional, validasi, aturan status dan acceptance criteria.
- Use Case Diagram, Activity Diagram, ERD, Data Dictionary, wireframe, test case.
## 21. Riwayat revisi
| Versi | Perubahan |
|---|---|
| 1.1 | PRD sebelumnya masih memuat laporan penemuan terpisah, klaim digital, dan pertemuan langsung. |
| 2.0 | Draft disesuaikan dengan konsep TitipTemu: laporan kehilangan, persetujuan Admin, konfirmasi pengembalian oleh pelapor dengan foto, perpindahan ke Barang Ditemukan, dan masa tampil 30 hari. |
| 2.1 | P0.1 ditetapkan: Barang Ditemukan hanya berisi laporan kehilangan yang berhasil dikembalikan setelah konfirmasi disetujui Admin; laporan barang temuan baru tidak termasuk. |
| 2.2 | P0.2 ditetapkan: TitipTemu mencakup seluruh lingkungan kampus; mahasiswa, dosen, dan staf aktif dapat mendaftar; Guest tetap membaca publik tanpa akun. |
| 2.3 | P0.3 ditetapkan: field pendaftaran lengkap dan verifikasi email wajib sebelum pelaporan; identitas kampus dicatat tanpa pencocokan database resmi. Ketentuan verifikasi email kemudian dicabut pada A11 (2026-11-22). |
| 2.4 | P0.4 ditetapkan: field laporan, delapan kategori tetap, maksimal 3 foto JPG/JPEG/PNG/WebP, maksimal 2 MB per foto, storage lokal Laravel, dan publikasi foto setelah persetujuan Admin. |
| 2.5 | P0.5 ditetapkan: kontak privat, edit/batal Pending, Approved tidak diedit langsung, alasan penolakan wajib, pengiriman ulang, batas 3 kali konfirmasi, serta retensi data dan riwayat. |
| 2.6 | P0.8 selesai: seluruh keputusan P0 diterapkan, versi/status diselaraskan, aturan draft yang sudah diputuskan difinalkan, dan daftar pertanyaan terbuka non-P0 disamakan dengan Requirement serta ARCHITECTURE. |
| 2.7 | 2026-11-22: diselaraskan dengan alur yang disetujui pemilik — User melaporkan barang temuan, Admin terpisah dengan login tersembunyi, kontak WhatsApp tanpa eksposur nomor, konfirmasi dengan nama pemilik dan foto bersama, edit Admin hanya pada `Pending`, penghapusan permanen manual untuk `returned`/`archived`, serta catatan selisih implementasi (jumlah foto dan kategori). |
| 2.8 | 2026-11-22 (A11): verifikasi email dihapus; eyebrow hanya tersisa di beranda; subtitle **Cari laporan** dihapus; notifikasi menjadi toast mengambang yang hilang setelah 3 detik; menu **Akun** dimulai dari kartu profil. |
| 2.9 | 2026-11-22 (A12): no. HP opsional dan privat ditambahkan pada pendaftaran dan biodata **Akun**; label **Program studi (wajib diisi mahasiswa)** diselaraskan; teks bantuan di bawah pemilihan foto profil dihapus. |
| 2.10 | 2026-11-22 (A13): no. HP Akun diubah menjadi wajib pada pendaftaran dan biodata; keterangan `(opsional)` dihapus sehingga label hanya **No. HP**. |


## Pertanyaan konfirmasi kelompok
1. Apakah “Barang Ditemukan” benar-benar hanya berisi laporan yang sudah dikembalikan, atau juga menerima laporan barang temuan baru? **Keputusan 2026-11-22: Barang Ditemukan tetap hanya berisi laporan yang pengembaliannya disetujui Admin. Laporan barang temuan masuk melalui alur pelaporan User dan tampil di Barang Hilang setelah disetujui; bukan daftar baru di Barang Ditemukan.**
2. Siapa saja yang boleh mendaftar: mahasiswa saja, dosen/staf, atau seluruh civitas akademika? **Sudah diputuskan: mahasiswa, dosen, dan staf aktif kampus.**
3. Apakah pendaftaran harus menggunakan email kampus/NIM dan apakah perlu verifikasi email? **Sudah diputuskan: pendaftaran meminta nama lengkap, NIM/NIP, email, password, fakultas, dan program studi khusus mahasiswa; identitas tidak diverifikasi melalui database resmi kampus. Sesuai A11 (2026-11-22), email tidak perlu diverifikasi sebelum pelaporan.**
4. Form laporan barang temuan: field apa yang wajib? **Keputusan 2026-11-22: no. HP penemu, nama barang, kategori tetap, deskripsi, tanggal dan lokasi ditemukan, foto, dan ketentuan.**
5. Berapa foto yang boleh diunggah, format, dan ukuran maksimal? **Sudah diputuskan: maksimal 3 foto; JPG/JPEG/PNG/WebP; maksimal 2 MB per foto.**
6. Apa ketentuan laporan yang harus dicentang user? Apakah perlu teks persetujuan yang akan disusun?
7. Informasi kontak penemu apakah ditampilkan publik, disembunyikan, atau komunikasi hanya melalui admin? **Keputusan 2026-11-22: nomor HP penemu tidak ditampilkan secara publik. Guest menghubungi penemu lewat tombol WhatsApp yang mengarahkan ke `wa.me`; sistem tidak memakai WhatsApp API, kredensial eksternal, atau layanan berbayar.**
8. Pada konfirmasi barang kembali, foto bukti harus memperlihatkan barang, pemilik, proses penyerahan, atau cukup barang saja? **Keputusan 2026-11-22: wajib foto bersama penemu–pemilik, disertai nama pemilik; keduanya bersifat privat dan hanya dapat dilihat User terkait serta Admin.**
9. Apakah User boleh membatalkan laporan atau mengubahnya setelah disetujui? **Sudah diputuskan: pemilik boleh mengedit/membatalkan saat `Pending`; `Approved` tidak dapat diedit langsung.**
10. Jika Admin menolak bukti pengembalian, apakah laporan tetap berada di Barang Hilang dan User boleh mengirim ulang bukti? **Sudah diputuskan: alasan wajib, perbaikan dan pengiriman ulang maksimal 3 kali; setelah itu melalui Admin.**
11. Hitungan 30 hari pada Barang Ditemukan dimulai dari waktu Admin menyetujui pengembalian, atau dari tanggal user menyatakan barang kembali? **Sudah diputuskan: sejak Admin menyetujui dan mempublikasikan, zona waktu Asia/Makassar.**
12. Setelah 30 hari, apakah postingan hanya disembunyikan/diarsipkan atau benar-benar dihapus dari database? **Sudah diputuskan: diarsipkan dan disembunyikan dari publik; data tetap disimpan, foto bukti tetap privat.**
13. Apakah Admin boleh menghapus laporan, atau hanya menolak/arsipkan? **Keputusan 2026-11-22: Admin dapat menghapus permanen laporan berstatus `returned` atau `archived` setelah konfirmasi. Arsip otomatis 30 hari tetap berlaku dan data lain tidak dihapus; penghapusan manual mencakup foto dan bukti privat terkait.**
14. Lingkup final TitipTemu: fakultas tertentu, program studi, atau seluruh kampus? **Sudah diputuskan: seluruh lingkungan kampus.**
15. Apakah pengembalian boleh diajukan sebelum Admin memeriksa bukti? **Keputusan 2026-11-22: User mengirim konfirmasi terlebih dahulu dengan nama pemilik dan foto bersama, lalu Admin memeriksa dan menyetujui atau menolak. Laporan barang temuan tetap masuk alur User ke Barang Hilang, bukan daftar Barang Ditemukan baru.**
