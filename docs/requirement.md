# SYSTEM REQUIREMENT — TitipTemu (Baseline v2.10)

**Status:** Disetujui untuk MVP; pertanyaan non-P0 yang tersisa dicatat terbuka

**Versi:** 2.10

**Platform:** Website

**Teknologi acuan:** Laravel + MySQL; Blade + Bootstrap untuk frontend

**Lingkup:** Seluruh lingkungan kampus

> Baseline ini menggantikan alur baseline lama yang menggunakan laporan barang ditemukan terpisah, klaim kepemilikan, dan penjadwalan pertemuan. Alur yang berlaku sejak 2026-11-22: User melaporkan **barang yang ditemukan**, Guest mencari dan menghubungi penemu lewat WhatsApp, dan Admin memverifikasi lewat akun terpisah dengan jalur masuk tersembunyi. Fokusnya adalah laporan barang temuan dan konfirmasi pengembalian. Pertanyaan non-P0 yang masih terbuka diberi label terbuka/rekomendasi dan belum menjadi aturan final.

## 1. Ringkasan keputusan dan asumsi
| ID | Ketentuan | Status |
|---|---|---|
| ASM-01 | TitipTemu berupa website | Ditetapkan dari konsep |
| ASM-02 | Backend Laravel dan database MySQL; frontend Blade + Bootstrap | Ditetapkan |
| ASM-03 | Guest dapat mengakses informasi publik tanpa login | Ditetapkan dari konsep |
| ASM-04 | User wajib login untuk melaporkan barang temuan dan konfirmasi pengembalian | Ditetapkan dari konsep; diperjelas 2026-11-22 |
| ASM-05 | Admin meverifikasi laporan dan konfirmasi pengembalian | Ditetapkan dari konsep |
| ASM-06 | MVP berfokus pada laporan barang temuan yang dibuat User; tidak ada daftar penemuan terpisah di luar laporan yang masuk ke Barang Hilang | Ditetapkan 2026-11-22 |
| ASM-07 | Dua menu publik: Barang Hilang (laporan temuan terverifikasi) dan Barang Ditemukan (riwayat pengembalian yang disetujui Admin) | Ditetapkan 2026-11-22 |
| ASM-13 | Kontak penemu hanya lewat redirect `wa.me`; nomor HP tidak ditampilkan sebagai teks di halaman/respons publik | Ditetapkan 2026-11-22 |
| ASM-14 | Akun Admin terpisah di tabel `admins`, login tersembunyi, dan beberapa akun Admin dapat berdiri sendiri | Ditetapkan 2026-11-22 |
| ASM-08 | Barang yang pengembaliannya disetujui masuk Barang Ditemukan selama 30 hari, dihitung sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya | Ditetapkan; zona waktu Asia/Makassar |
| ASM-09 | Barang yang lewat 30 hari disembunyikan dari publik dan berstatus `Archived`, bukan dihapus permanen dari database | Ditetapkan |
| ASM-10 | Sistem memiliki peran Guest, User, dan Admin; Guest bukan akun terdaftar; User mencakup mahasiswa, dosen, dan staf aktif kampus | Ditetapkan |
| ASM-11 | Satu laporan minimal memiliki satu foto, maksimal 3 foto; JPG/JPEG/PNG/WebP; maksimal 2 MB per foto | Ditetapkan |
| ASM-12 | Belum ada integrasi SSO akademik | Mengikuti baseline sebelumnya |

## 2. Aktor dan hak akses
### 2.1 Guest
- Membuka beranda.
- Melihat daftar Barang Hilang dan Barang Ditemukan yang dipublikasikan.
- Melihat detail publik.
- Melakukan pencarian/filter.
- Menghubungi penemu lewat tombol WhatsApp tanpa melihat nomor HP.
- Membaca panduan dan ketentuan.
- Tidak dapat membuat laporan, mengirim konfirmasi pengembalian, atau membuka area User/Admin.
### 2.2 User
- User adalah mahasiswa, dosen, atau staf aktif kampus yang memiliki akun dan bertindak sebagai **penemu/pelapor**.
- Mendaftar dan login.
- Membuat laporan barang temuan dengan **no. HP penemu**, tanggal dan lokasi ditemukan.
- Mengunggah foto laporan.
- Menyetujui ketentuan pelaporan barang temuan.
- Melihat daftar dan detail laporan miliknya serta statusnya melalui menu **Akun**.
- Mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung.
- Memperbaiki dan mengirim ulang laporan yang ditolak setelah membaca alasan Admin.
- Mengajukan konfirmasi barang kembali pada laporan miliknya dengan **nama pemilik** dan **foto bersama penemu–pemilik**.
- Melihat status pemeriksaan konfirmasi.
### 2.3 Admin
- Sistem mendukung beberapa akun Admin; tidak ada asumsi satu Admin.
- Akun Admin disimpan di tabel `admins`, terpisah dari tabel `users`.
- Login melalui jalur tersembunyi `ADMIN_LOGIN_URI`; tidak ada tautan Admin di navigasi publik.
- Melihat laporan masuk.
- Memeriksa dan menyetujui/menolak laporan dengan alasan wajib saat menolak.
- Mengedit langsung laporan berstatus `Pending` dan mencatat perubahan pada riwayat.
- Mengelola laporan dan publikasi.
- Melihat antrean konfirmasi pengembalian.
- Memeriksa bukti pengembalian yang privat.
- Menyetujui/menolak konfirmasi pengembalian dengan alasan wajib saat menolak.
- Menambahkan alasan penolakan.
- Melihat log aktivitas dan histori status.
- Menghapus permanen laporan berstatus `returned` atau `archived` setelah konfirmasi.

### 2.4 Data pendaftaran dan akun
- Nama lengkap wajib.
- NIM/NIP wajib dan bersifat privat.
- Email wajib dan unik; tidak memerlukan verifikasi.
- No. HP wajib dan bersifat privat; diisi saat pendaftaran dan dapat diubah pada menu **Akun**, serta tidak ditampilkan di halaman publik. Label formulir hanya **No. HP**.
- Password wajib dan disimpan dengan hashing aman.
- Fakultas wajib dan bersifat privat.
- Program studi wajib untuk mahasiswa dan tidak berlaku untuk dosen/staf.
- User dapat melaporkan barang temuan setelah login; email tidak perlu diverifikasi.
- Sistem mencatat identitas kampus, tetapi tidak mencocokkannya dengan database resmi kampus.
## 3. Navigasi dan halaman
### 3.1 Publik
- Beranda
- Barang Hilang
- Barang Ditemukan
- Detail Barang
- Pencarian
- Panduan/Ketentuan
- Login
- Registrasi
### 3.2 User
- Beranda
- Laporkan Barang Temuan
- **Akun** (aktivitas, laporan, dan status milik User)
- Detail laporan
- Konfirmasi barang telah kembali
- Logout
### 3.3 Admin
- Login tersembunyi melalui `ADMIN_LOGIN_URI`
- Dashboard Admin dengan filter status
- Cari laporan lintas status dari menu sidebar Admin
- Laporan menunggu verifikasi
- Laporan aktif
- Detail laporan/verifikasi, edit `Pending`, dan bukti privat
- Konfirmasi pengembalian menunggu verifikasi
- Barang Ditemukan/riwayat pengembalian
- Riwayat aktivitas
- Penghapusan permanen `returned`/`archived`
- Logout
## 4. Functional Requirements
| ID | Modul | Requirement | Prioritas |
|---|---|---|---|
| FR-01 | Public | Sistem menampilkan beranda tanpa login | Must |
| FR-02 | Public | Sistem menyediakan halaman Barang Hilang dan Barang Ditemukan | Must |
| FR-03 | Public | Guest dapat melihat detail laporan yang statusnya boleh dipublikasi | Must |
| FR-04 | Search | Sistem menyediakan pencarian kata kunci barang | Must |
| FR-05 | Search/filter | Sistem dapat memfilter kategori, tanggal, atau lokasi jika data/filter tersebut tersedia | Should |
| FR-06 | Auth | User dapat registrasi dan login | Must |
| FR-07 | Auth | Admin dapat login ke area Admin melalui jalur tersembunyi dengan akun terpisah dari User | Must |
| FR-08 | Authorization | Sistem membatasi akses berdasarkan Guest/User/Admin | Must |
| FR-09 | Report | User login dapat melaporkan barang yang ditemukan | Must |
| FR-10 | Report | Form laporan meminta no. HP penemu, data barang, tanggal dan lokasi ditemukan, serta foto | Must |
| FR-11 | Report | Sistem memvalidasi input dan file foto | Must |
| FR-12 | Report | Laporan baru disimpan sebagai Pending dan tidak dipublikasikan | Must |
| FR-13 | Admin | Admin dapat melihat antrean laporan Pending | Must |
| FR-14 | Admin | Admin dapat menyetujui atau menolak laporan | Must |
| FR-15 | Publication | Laporan Approved muncul di Barang Hilang | Must |
| FR-16 | User dashboard | User dapat melihat laporan yang dibuat sendiri dan statusnya | Must |
| FR-17 | User dashboard | User tidak dapat melihat atau mengubah laporan milik User lain | Must |
| FR-18 | Return confirmation | Pemilik laporan dapat mengirim konfirmasi barang telah kembali pada laporan miliknya | Must |
| FR-19 | Return confirmation | Form konfirmasi membutuhkan nama pemilik, foto bersama penemu–pemilik, dan persetujuan pernyataan | Must |
| FR-20 | Return confirmation | Konfirmasi yang dikirim memiliki status Return Pending | Must |
| FR-21 | Admin | Admin dapat melihat antrean konfirmasi pengembalian | Must |
| FR-22 | Admin | Admin dapat menyetujui atau menolak konfirmasi pengembalian | Must |
| FR-23 | Admin | Admin wajib menyertakan alasan saat menolak laporan atau konfirmasi pengembalian | Must |
| FR-24 | Status | Ketika konfirmasi disetujui, laporan tidak lagi tampil di Barang Hilang | Must |
| FR-25 | Status | Ketika konfirmasi disetujui, laporan tampil pada Barang Ditemukan | Must |
| FR-26 | Retention | Sistem menghentikan publikasi laporan pada Barang Ditemukan setelah 30 hari | Must |
| FR-27 | Retention | Pengarsipan otomatis dilakukan tanpa memerlukan tindakan Admin manual | Must |
| FR-28 | Privacy | Sistem tidak menampilkan data pribadi atau foto bukti privat ke Guest | Must |
| FR-29 | Audit | Sistem mencatat keputusan Admin dan perubahan status penting | Should/Must untuk status inti |
| FR-30 | Admin management | Admin dapat mengelola visibilitas/arsip laporan sesuai aturan status dan retensi yang disetujui | Must |
| FR-31 | Error handling | Sistem memberi pesan ketika data wajib kosong, foto tidak valid, atau upload gagal | Must |
| FR-32 | Rules | User diwajibkan menyetujui ketentuan pelaporan barang temuan sebelum mengirim laporan | Must |
| FR-33 | Rules | User diwajibkan menyetujui pernyataan pengembalian sebelum mengirim konfirmasi | Must |
| FR-34 | Auth | User wajib login sebelum melaporkan barang temuan atau mengirim konfirmasi pengembalian; verifikasi email tidak diperlukan | Must |
| FR-35 | Public contact | Detail publik menyediakan tombol WhatsApp yang mengarahkan ke `wa.me` tanpa menampilkan nomor HP penemu | Must |
| FR-36 | Admin | Admin dapat mengedit laporan berstatus `Pending`; perubahan tercatat pada riwayat dengan Admin peninjau | Must |
| FR-37 | Privacy | Nama pemilik dan foto bersama penemu–pemilik hanya dapat diakses oleh User terkait dan Admin | Must |
| FR-38 | Admin | Admin dapat menghapus permanen laporan `returned`/`archived` setelah konfirmasi, termasuk berkas privat terkait | Must |
| FR-39 | Auth | Admin terpisah tidak dapat mengakses area User dan sebaliknya; akun User lama ber-role admin tidak dapat login sebagai Admin | Must |
| FR-40 | Admin | Admin dapat mencari laporan lintas status melalui menu **Cari laporan** | Must |

## 5. Alur proses sistem
### 5.1 Membuat laporan barang temuan
1. User login.
2. User membuka form **Laporkan Barang Temuan**.
3. User mengisi no. HP penemu, nama/kategori/deskripsi barang, dan tanggal serta lokasi ditemukan.
4. User mengunggah foto.
5. User mencentang persetujuan ketentuan pelaporan temuan.
6. Sistem memvalidasi.
7. Sistem menyimpan laporan berstatus `Pending`.
8. Admin meninjau laporan dan dapat mengedit laporan `Pending` sebelum memutuskan.
9. Admin menyetujui atau menolak dengan alasan wajib saat menolak.
10. Jika disetujui, laporan tampil pada Barang Hilang beserta tombol WhatsApp. Jika ditolak, User dapat memperbaiki dan mengirim ulang laporan.
### 5.2 Guest menghubungi penemu
1. Guest membuka detail laporan pada Barang Hilang.
2. Guest menekan tombol WhatsApp.
3. Sistem membuat tautan `wa.me` dari nomor penemu yang disimpan dan mengarahkan browser ke sana.
4. Nomor HP penemu tidak pernah dikirim sebagai teks pada halaman atau respons publik.
### 5.3 Konfirmasi pengembalian barang
1. User membuka laporan miliknya yang berstatus aktif melalui menu **Akun**.
2. User memilih “Konfirmasi Barang Telah Kembali”.
3. User mengisi nama pemilik, mengunggah foto bersama penemu–pemilik, dan dapat menambah catatan opsional.
4. User menyetujui pernyataan bahwa barang telah diterima pemiliknya.
5. Sistem memvalidasi dan menyimpan konfirmasi berstatus `Return Pending`; data konfirmasi tidak publik.
6. Admin meninjau data dan bukti melalui halaman privat.
7. Admin menyetujui atau menolak.
8. Jika disetujui: status laporan `Returned`, daftar publik berpindah dari Barang Hilang ke Barang Ditemukan, dan timestamp persetujuan Admin dicatat sebagai awal periode 30 hari.
9. Jika ditolak: laporan tetap aktif di Barang Hilang; alasan Admin wajib dicatat; User dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian dilakukan melalui Admin.
10. Setelah 30 hari dari tanggal acuan, sistem mengubah visibilitas menjadi `Archived` dan menghilangkan item dari daftar Barang Ditemukan.
11. Admin dapat menghapus permanen laporan `returned`/`archived` setelah konfirmasi.
### 5.4 Admin meverifikasi laporan
1. Admin login melalui jalur tersembunyi.
2. Admin melihat antrean Pending.
3. Admin membuka detail, foto, dan data laporan.
4. Admin dapat mengedit laporan `Pending`; perubahan dicatat pada riwayat.
5. Admin menyetujui atau menolak dengan alasan wajib saat menolak.
6. Sistem menyimpan keputusan, waktu, Admin yang mengambil tindakan, dan alasan penolakan.
### 5.5 Admin meverifikasi pengembalian
1. Admin membuka antrean Return Pending.
2. Admin melihat data konfirmasi dan foto bukti yang aksesnya privat.
3. Admin menyetujui atau menolak.
4. Sistem mencatat keputusan, waktu, Admin, dan alasan penolakan wajib jika ditolak.
## 6. Aturan validasi form laporan
| Field | Validasi awal | Status |
|---|---|---|
| Nama barang | Wajib; 2–100 karakter | Rekomendasi |
| Kategori | Wajib dipilih dari daftar tetap yang disetujui | Ditetapkan |
| Deskripsi | Wajib; 10–1000 karakter | Rekomendasi |
| No. HP penemu | Wajib; format nomor dapat dinormalisasi menjadi `wa.me` | Ditetapkan 2026-11-22 |
| Tanggal ditemukan | Wajib; tidak boleh tanggal masa depan | Rekomendasi |
| Lokasi ditemukan | Wajib; 3–150 karakter | Rekomendasi |
| Foto barang | Wajib; minimal 1 dan maksimal 3 foto | Ditetapkan |
| Format foto | JPG/JPEG/PNG/WebP | Ditetapkan |
| Ukuran foto | Maksimal 2 MB per foto | Ditetapkan |
| Penyimpanan foto | Storage lokal Laravel; tidak publik sebelum laporan disetujui Admin | Ditetapkan |
| Ketentuan | Checkbox persetujuan ketentuan pelaporan barang temuan wajib | Ditetapkan dari konsep |
| Kontak | No. HP penemu tidak ditampilkan kepada publik; kontak lewat `wa.me` | Ditetapkan 2026-11-22 |

Kategori tetap sesuai P0.4: Elektronik; Dompet & Uang; Dokumen & Kartu Identitas; Kunci; Tas; Pakaian & Aksesoris; Buku & Alat Tulis; Lainnya.

**Catatan selisih implementasi (2026-11-22):** implementasi saat ini menerima satu foto per laporan dan memakai lima pilihan kategori (Elektronik, Dokumen, Aksesori, Pakaian, Lainnya). Keputusan P0.4 tetap tercatat; penyelarasan memerlukan tugas tersendiri.
## 7. Validasi form konfirmasi pengembalian
| Field | Validasi awal | Status |
|---|---|---|
| Laporan terkait | Wajib; harus milik User yang login dan berstatus `Approved` | Must |
| Nama pemilik | Wajib; 2–100 karakter; bersifat privat | Ditetapkan 2026-11-22 |
| Foto bersama penemu–pemilik | Wajib; 1–3 file JPG/JPEG/PNG/WebP, maksimal 2 MB per file, storage privat | Ditetapkan |
| Tanggal pengembalian | Dicatat otomatis saat Admin menyetujui | Ditetapkan |
| Catatan pengembalian | Opsional; maksimal 500 karakter; jangan memuat kontak pribadi | Ditetapkan |
| Pernyataan pengembalian | Checkbox wajib | Ditetapkan dari konsep |
| Catatan Admin | Wajib diisi ketika menolak | Ditetapkan |
## 8. Aturan bisnis
| ID | Aturan |
|---|---|
| BR-01 | Sistem berlaku untuk seluruh lingkungan kampus. |
| BR-02 | Guest dapat membaca konten publik tanpa login. |
| BR-03 | User wajib login untuk melaporkan barang temuan; verifikasi email tidak diperlukan. |
| BR-04 | User wajib login untuk mengirim konfirmasi pengembalian. |
| BR-05 | Laporan barang temuan harus menyertakan no. HP penemu, foto, dan data wajib lengkap. |
| BR-06 | Laporan baru berstatus Pending dan tidak dipublikasikan sebelum Admin menyetujui. |
| BR-07 | Laporan yang tampil di Barang Hilang adalah laporan berstatus `Approved` dan `Return Pending`; laporan `Pending`, `Rejected`, `Cancelled`, dan `Archived` tidak tampil. |
| BR-08 | Nomor HP penemu tidak ditampilkan sebagai teks pada halaman/respons publik; kontak hanya lewat redirect `wa.me`. |
| BR-09 | Konfirmasi pengembalian hanya dapat diajukan oleh User pemilik laporan. |
| BR-10 | Konfirmasi pengembalian wajib menyertakan nama pemilik, foto bersama penemu–pemilik, dan persetujuan pernyataan. |
| BR-11 | Nama pemilik dan foto bersama bersifat privat; hanya User terkait dan Admin yang dapat melihatnya. |
| BR-12 | Konfirmasi pengembalian harus disetujui Admin sebelum perpindahan daftar. |
| BR-13 | Setelah disetujui, laporan berpindah dari Barang Hilang ke Barang Ditemukan. |
| BR-14 | Barang Ditemukan tampil 30 hari sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya pada daftar tersebut, menggunakan zona waktu Asia/Makassar (WITA), kemudian tidak tampil publik dan berstatus `Archived`. |
| BR-15 | Pengarsipan 30 hari tidak menghapus data secara permanen; data tetap disimpan dan foto bukti pengembalian tetap privat. |
| BR-16 | Bukti foto konfirmasi, nama pemilik, data pribadi, dan nomor HP penemu bersifat terbatas dan bukan konten publik. |
| BR-17 | Admin wajib menyertakan alasan saat menolak laporan atau konfirmasi pengembalian. |
| BR-18 | Pemilik dapat mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung oleh pemilik. |
| BR-19 | Admin dapat mengedit langsung laporan berstatus `Pending`; perubahan dicatat pada riwayat dengan peninjau Admin. |
| BR-20 | Pemilik dapat memperbaiki dan mengirim ulang laporan yang ditolak setelah membaca alasan Admin. |
| BR-21 | Konfirmasi pengembalian yang ditolak wajib memiliki alasan; pemilik dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian dilakukan melalui Admin. |
| BR-22 | Data laporan, data konfirmasi, dan riwayat status tidak dihapus permanen, kecuali Admin melakukan penghapusan manual sesuai BR-23. |
| BR-23 | Admin dapat menghapus permanen laporan berstatus `returned` atau `archived` setelah konfirmasi; status lain tidak dapat dihapus. Penghapusan mencakup berkas foto dan bukti privat terkait. |
| BR-24 | Akun Admin terpisah dari akun User dan masuk melalui jalur tersembunyi; beberapa akun Admin dapat berdiri sendiri. |
## 9. Status laporan dan transisi
| Status | Deskripsi | Visibilitas publik |
|---|---|---|
| `Pending` | Menunggu verifikasi laporan | Tidak |
| `Approved` / `Lost Active` | Laporan barang temuan disetujui dan masih menunggu pemilik | Barang Hilang |
| `Rejected` | Laporan ditolak | Tidak |
| `Return Pending` | User mengirim konfirmasi pengembalian, menunggu Admin | Tetap Barang Hilang sampai keputusan Admin |
| `Returned` | Admin menyetujui konfirmasi pengembalian | Barang Ditemukan |
| `Archived` | Lewat masa 30 hari | Tidak |
| `Cancelled` | Laporan Pending dibatalkan oleh pemilik | Tidak |

Laporan `returned` dan `archived` dapat dihapus permanen oleh Admin setelah konfirmasi. Status lain tidak dapat dihapus.

**Transisi normal:** `Pending → Approved/Lost Active → Return Pending → Returned → Archived`.  
Penolakan laporan: `Pending → Rejected`, dengan alasan Admin; User dapat memperbaiki dan mengirim ulang.

Penolakan konfirmasi: `Return Pending → Approved/Lost Active`, dengan alasan Admin; User dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian dilakukan melalui Admin.

Pembatalan: `Pending → Cancelled` oleh pemilik.
## 10. Aturan 30 hari
- Masa tampil 30 hari hanya berlaku untuk daftar Barang Ditemukan.
- Awal hitungan adalah timestamp saat Admin menyetujui konfirmasi pengembalian dan laporan dipublikasikan pada Barang Ditemukan.
- Zona waktu yang digunakan adalah Asia/Makassar (WITA).
- Batas arsip dihitung dari timestamp persetujuan Admin ditambah 30 hari.
- Sistem menyimpan timestamp persetujuan Admin dan menjalankan proses terjadwal untuk memeriksa masa tampil.
- Setelah batas lewat, laporan berstatus `Archived` dan tidak ditampilkan pada halaman publik Barang Ditemukan.
- Data laporan tetap disimpan di database, tidak dihapus permanen oleh proses arsip otomatis. Penghapusan permanen hanya dapat dilakukan manual oleh Admin pada laporan `returned`/`archived` setelah konfirmasi.
- Foto bukti pengembalian tetap privat dan tidak ditampilkan kepada publik.
## 11. Informasi publik vs privat
### Publik
- Nama barang.
- Kategori.
- Deskripsi yang telah diverifikasi.
- Foto publik laporan.
- Tanggal dan lokasi ditemukan; tingkat detail masih terbuka.
- Status laporan.
- Tombol WhatsApp untuk menghubungi penemu (tanpa menampilkan nomor HP penemu).
### Privat
- NIM atau identitas mahasiswa.
- Email pribadi dan kontak pribadi lainnya.
- **Nomor HP penemu** — tidak dirender sebagai teks; hanya dipakai server untuk tautan `wa.me`.
- **Nama pemilik** dan **foto bersama penemu–pemilik** — dapat diakses User terkait dan Admin.
- Detail akun.
- Ciri unik yang sengaja tidak dipublikasikan.
## 12. Non-Functional Requirements
| ID | Aspek | Requirement |
|---|---|---|
| NFR-01 | Responsif | Website usable di desktop dan mobile |
| NFR-02 | Security | Password menggunakan hashing, bukan plaintext |
| NFR-03 | Security | Validasi input di server-side |
| NFR-04 | Authorization | Guest, User, dan Admin terpisah hanya dapat melakukan tindakan sesuai peran; jalur login Admin tidak ditautkan di navigasi publik |
| NFR-05 | Ownership | User hanya dapat mengakses/mengubah laporan miliknya, kecuali data publik |
| NFR-06 | File | Validasi tipe, ukuran, dan file upload; nama file tidak dipercaya dari client |
| NFR-07 | Privacy | Foto bukti pengembalian dan data pribadi tidak terbuka untuk umum |
| NFR-08 | Audit | Keputusan Admin dan perubahan status penting tercatat |
| NFR-09 | Reliability | Backup database dan file yang diperlukan memiliki prosedur |
| NFR-10 | Usability | Label, formulir, status, dan pesan kesalahan mudah dipahami |
| NFR-11 | Maintainability | Struktur kode Laravel konsisten dan terdokumentasi |
| NFR-12 | Performance | Target uji awal: halaman publik ≤3 detik; pencarian ≤2 detik pada lingkungan uji yang disepakati |
| NFR-13 | Privacy | Nomor HP penemu tidak muncul pada HTML/respons publik; kontak hanya berupa redirect `wa.me` yang dibuat server tanpa layanan eksternal berbayar |
| NFR-14 | Audit | Edit Admin pada laporan `Pending` dan penghapusan permanen dicatat pada riwayat |
## 13. User Stories dan Acceptance Criteria
### US-01 — Guest melihat informasi
**Sebagai Guest**, saya ingin melihat Barang Hilang dan Barang Ditemukan tanpa login agar dapat mencari informasi.  
**Acceptance:** halaman publik dapat dibuka; hanya item dengan status/visibilitas publik yang ditampilkan; data privat tidak tampil.
### US-02 — User melaporkan barang temuan
**Sebagai User/penemu**, saya ingin melaporkan barang yang saya temukan dengan no. HP, data barang, dan foto agar laporan dapat diperiksa Admin.
**Acceptance:** login diperlukan; no. HP penemu, tanggal dan lokasi ditemukan, serta foto wajib; persetujuan ketentuan wajib; laporan tersimpan `Pending` dan belum tampil publik.
### US-03 — Admin menyetujui laporan
**Sebagai Admin**, saya ingin memeriksa laporan agar hanya laporan yang sesuai yang dipublikasikan.  
**Acceptance:** Admin dapat melihat detail; menyetujui atau menolak; keputusan tercatat; Approved tampil di Barang Hilang.
### US-04 — User memantau laporan
**Sebagai User**, saya ingin melihat laporan dan status milik saya.  
**Acceptance:** User hanya melihat laporan miliknya; status dan keputusan terlihat; laporan user lain tidak dapat diakses lewat URL langsung.
### US-05 — User mengonfirmasi barang kembali
**Sebagai User pemilik laporan**, saya ingin mengonfirmasi barang telah kembali dengan nama pemilik dan foto bersama.
**Acceptance:** hanya pemilik laporan aktif; nama pemilik dan foto bersama penemu–pemilik wajib; keduanya tidak tampil publik; pernyataan wajib dicentang; konfirmasi masuk antrean Admin; laporan belum pindah sebelum disetujui Admin.
### US-06 — Admin menyetujui pengembalian
**Sebagai Admin**, saya ingin memeriksa bukti pengembalian agar perpindahan status benar.  
**Acceptance:** Admin dapat menyetujui/menolak; keputusan tercatat; jika disetujui laporan pindah ke Barang Ditemukan; jika ditolak item tetap aktif, alasan wajib tercatat, dan User dapat memperbaiki serta mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
### US-07 — Item Barang Ditemukan diarsipkan otomatis
**Sebagai pengelola**, saya ingin item tidak lagi muncul setelah 30 hari agar daftar Barang Ditemukan tetap relevan.  
**Acceptance:** sistem menyimpan tanggal acuan; proses terjadwal mengarsipkan item yang melewati 30 hari; item Archived tidak tampil publik; data dan histori tidak dihapus oleh proses arsip, kecuali penghapusan manual Admin sesuai BR-23.
### US-08 — Admin mengelola verifikasi
**Sebagai Admin**, saya ingin melihat antrean laporan dan konfirmasi pengembalian agar tugas verifikasi terorganisasi.  
**Acceptance:** antrean dapat difilter menurut status; detail dapat diperiksa; keputusan tersimpan beserta waktu dan Admin.
### US-09 — Guest menghubungi penemu
**Sebagai Guest**, saya ingin menghubungi penemu lewat WhatsApp tanpa melihat nomor HP penemu.
**Acceptance:** tombol tersedia pada detail laporan yang dipublikasikan; tautan mengarah ke `wa.me`; nomor HP tidak muncul pada HTML atau respons publik.
### US-10 — Admin mengedit laporan Pending
**Sebagai Admin**, saya ingin memperbaiki laporan `Pending` sebelum memutuskan agar data publik menjadi akurat.
**Acceptance:** edit hanya tersedia untuk `Pending`; perubahan disimpan dan tercatat pada riwayat dengan peninjau Admin; laporan `Approved` tidak dapat diedit langsung.
### US-11 — Admin menghapus laporan selesai
**Sebagai Admin**, saya ingin menghapus permanen laporan yang sudah kembali atau diarsipkan agar data usang tidak menumpuk.
**Acceptance:** penghapusan hanya untuk `returned`/`archived` dan memerlukan konfirmasi; data, foto, dan bukti privat terkait ikut dihapus; status lain ditolak.
## 14. Error dan kondisi gagal
- Field wajib tidak lengkap: sistem menolak submit dan memberi penjelasan.
- Foto tidak ada, format salah, ukuran terlalu besar, atau upload gagal: sistem menolak/menampilkan error yang jelas dan tidak membuat laporan publik.
- User belum login: diarahkan ke autentikasi sebelum melaporkan barang temuan atau konfirmasi.
- User mencoba mengakses laporan orang lain: akses ditolak.
- Admin menolak laporan: status Rejected dan alasan wajib tercatat; User dapat memperbaiki dan mengirim ulang.
- Admin menolak konfirmasi: laporan tetap Barang Hilang, alasan wajib tercatat; User dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
- Proses arsip otomatis gagal: item tidak dihapus permanen; kegagalan perlu log dan dapat diproses ulang.
- Admin mencoba menghapus laporan berstatus selain `returned`/`archived`: permintaan ditolak dan data tetap ada.
- Akun Admin mencoba mengakses area User atau akun User mencoba masuk ke area Admin: akses ditolak/diarahkan ke login yang sesuai.
## 15. Kriteria MVP selesai
- Guest dapat melihat Barang Hilang dan Barang Ditemukan tanpa login.
- Registrasi dan login User berfungsi; Admin masuk lewat jalur tersembunyi dengan akun terpisah.
- User dapat melaporkan barang temuan dengan no. HP penemu, data barang, foto, serta menyetujui ketentuan.
- Admin dapat menyetujui/menolak laporan dan mengedit laporan `Pending`.
- Laporan Approved muncul pada Barang Hilang.
- User dapat melihat riwayat laporan miliknya.
- User dapat mengedit atau membatalkan laporan `Pending`; laporan `Approved` tidak dapat diedit langsung.
- Penolakan laporan dan konfirmasi pengembalian selalu disertai alasan Admin.
- User dapat memperbaiki dan mengirim ulang laporan yang ditolak, serta konfirmasi pengembalian yang ditolak maksimal 3 kali sebelum penyelesaian melalui Admin.
- User dapat mengirim konfirmasi pengembalian pada laporan miliknya dengan nama pemilik, foto bersama penemu–pemilik, dan persetujuan pernyataan.
- Nomor HP penemu, nama pemilik, dan foto bersama tidak tampil di halaman publik.
- Admin dapat menyetujui/menolak konfirmasi.
- Konfirmasi yang disetujui memindahkan item ke Barang Ditemukan.
- Item di Barang Ditemukan tidak tampil lagi setelah 30 hari.
- Akses privat, peran terpisah, jalur Admin tersembunyi, dan validasi file diterapkan.
- Admin dapat menghapus permanen laporan `returned`/`archived` setelah konfirmasi; status lain tidak dapat dihapus.
- Dokumentasi instalasi, konfigurasi, dan penggunaan tersedia di repository.
## 16. Pertanyaan dan status keputusan
Pertanyaan P0 yang sudah dijawab tetap dicatat di bawah ini. Pertanyaan non-P0 yang belum dijawab ditandai terbuka.
1. Barang Ditemukan: hanya barang yang sudah dikembalikan, atau juga laporan barang temuan baru? **Keputusan 2026-11-22: hanya laporan yang pengembaliannya disetujui Admin. Laporan barang temuan dibuat User dan tampil di Barang Hilang setelah disetujui; bukan daftar baru di Barang Ditemukan.**
2. Lingkup akun: mahasiswa saja atau seluruh civitas akademika? **Sudah diputuskan: mahasiswa, dosen, dan staf aktif kampus.**
3. Registrasi: data apa yang diminta dan apakah email kampus diverifikasi? **Sudah diputuskan: pendaftaran meminta nama lengkap, NIM/NIP, email, password, fakultas, dan program studi khusus mahasiswa; identitas tidak diverifikasi melalui database resmi kampus. Sesuai A11 (2026-11-22), email tidak perlu diverifikasi sebelum pelaporan.**
4. Data form laporan barang temuan: setujukah dengan no. HP penemu, nama barang, kategori, deskripsi, tanggal dan lokasi ditemukan, foto, dan ketentuan? **Keputusan 2026-11-22: ya, seluruh field tersebut wajib; tanggal dan lokasi bermakna saat barang ditemukan.**
5. Batas foto: jumlah maksimum, ukuran maksimum, dan format? **Sudah diputuskan: maksimal 3 foto; JPG/JPEG/PNG/WebP; maksimal 2 MB per foto.**
6. Ketentuan pelaporan: apa saja yang wajib disetujui User?
7. Kontak penemu: ditampilkan, disembunyikan, atau komunikasi melalui Admin? **Keputusan 2026-11-22: nomor HP tidak ditampilkan; Guest menghubungi penemu lewat tombol WhatsApp (`wa.me`) yang dibuat server, tanpa WhatsApp API atau layanan berbayar.**
8. Bukti pengembalian: foto seperti apa yang wajib diunggah, apakah hanya dilihat Admin, dan berapa batas format/ukurannya? **Keputusan 2026-11-22: wajib foto bersama penemu–pemilik dan nama pemilik; dapat dilihat User terkait dan Admin, bukan publik. Batas implementasi: 1–3 file JPG/JPEG/PNG/WebP, maksimal 2 MB per file.**
9. Apakah laporan boleh diedit/dibatalkan oleh User? Pada status apa? **Sudah diputuskan: pemilik dapat mengedit/membatalkan laporan `Pending`; laporan `Approved` tidak dapat diedit langsung.**
10. Jika konfirmasi pengembalian ditolak, apakah laporan tetap Barang Hilang dan User boleh mengajukan ulang? **Sudah diputuskan: laporan tetap Barang Hilang, alasan wajib dicatat, dan pengiriman ulang maksimal 3 kali; setelah itu penyelesaian melalui Admin.**
11. 30 hari dihitung sejak User mengisi tanggal kembali atau sejak Admin menyetujui? **Sudah diputuskan: sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya, zona waktu Asia/Makassar.**
12. Setelah 30 hari apakah diarsipkan (data tetap ada) atau dihapus permanen? **Sudah diputuskan: diarsipkan dan disembunyikan; data tetap disimpan, foto bukti tetap privat.**
13. Apakah Admin dapat menghapus laporan atau hanya menolak/arsip? **Keputusan 2026-11-22: Admin dapat menghapus permanen laporan `returned`/`archived` setelah konfirmasi. Arsip otomatis 30 hari tetap berlaku dan data lain tidak dihapus.**
14. Unit penerapan yang pasti: fakultas, prodi, atau seluruh kampus? **Sudah diputuskan: seluruh lingkungan kampus.**
15. Apakah diperlukan fitur tambahan seperti lokasi titik/gedung, kontak melalui Admin, atau notifikasi status? **Keputusan 2026-11-22: kontak publik memakai WhatsApp (`wa.me`) yang dibuat server, bukan melalui Admin dan tanpa nomor HP tampil; lokasi titik/gedung serta notifikasi status masih terbuka.**
16. Apakah Admin dapat mengelola kategori, atau kategori tetap saja? **Terbuka; yang sudah ditetapkan: delapan kategori tetap untuk laporan.**
17. Tanggal pengembalian wajib diisi atau dicatat otomatis, dan catatan pengembalian wajib atau opsional? **Terbuka.**
## 17. Revisi
| Versi | Perubahan |
|---|---|
| 1.1 | Baseline lama berisi laporan barang hilang dan ditemukan terpisah, klaim digital, serta proses pertemuan langsung. |
| 2.0 | Disederhanakan sesuai konsep TitipTemu: laporan kehilangan, verifikasi Admin, konfirmasi pengembalian oleh pemilik laporan dengan foto, perpindahan ke Barang Ditemukan, dan pengarsipan publik 30 hari. |
| 2.1 | P0.6 ditetapkan: 30 hari sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya, zona waktu Asia/Makassar, lalu `Archived` tanpa penghapusan permanen; foto bukti tetap privat. |
| 2.2 | P0.1 ditetapkan: Barang Ditemukan hanya berisi laporan kehilangan yang berhasil dikembalikan setelah konfirmasi disetujui Admin; laporan barang temuan baru tidak termasuk. |
| 2.3 | P0.2 ditetapkan: cakupan seluruh lingkungan kampus; mahasiswa, dosen, dan staf aktif dapat mendaftar; Guest membaca publik tanpa akun. Ketentuan verifikasi email dari keputusan P0.3 dicabut pada A11 (2026-11-22). |
| 2.4 | P0.4 ditetapkan: field laporan, kategori tetap, maksimal 3 foto JPG/JPEG/PNG/WebP, maksimal 2 MB per foto, storage lokal Laravel, dan publikasi foto setelah persetujuan Admin. |
| 2.5 | P0.5 ditetapkan: kontak privat, edit/batal Pending, Approved tidak diedit langsung, alasan penolakan wajib, pengiriman ulang, batas 3 kali konfirmasi, serta retensi data dan riwayat. |
| 2.6 | P0.8 selesai: seluruh keputusan P0 diterapkan, status/versi diselaraskan, label draft yang sudah diputuskan difinalkan, dan daftar pertanyaan terbuka non-P0 disamakan dengan PRD serta ARCHITECTURE. |
| 2.7 | 2026-11-22: diselaraskan dengan alur yang disetujui pemilik — pelaporan barang temuan, no. HP penemu dan kontak WhatsApp, Admin terpisah tersembunyi, nama pemilik dan foto bersama yang privat, edit Admin hanya pada `Pending`, penghapusan permanen `returned`/`archived`, FR/US/NFR baru, serta catatan selisih implementasi. |
| 2.8 | 2026-11-22 (A11): verifikasi email dihapus; eyebrow hanya di beranda; subtitle **Cari laporan** dihapus; notifikasi mengambang otomatis hilang setelah 3 detik; **Akun** dimulai dari kartu profil. |
| 2.9 | 2026-11-22 (A12): no. HP opsional dan privat pada pendaftaran/biodata **Akun**; label program studi memakai format kurung; teks bantuan pemilihan foto profil dihapus. |
| 2.10 | 2026-11-22 (A13): no. HP Akun menjadi wajib pada pendaftaran/biodata; teks `(opsional)` dihapus dari labelnya. |
