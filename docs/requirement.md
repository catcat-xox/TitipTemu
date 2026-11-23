# SYSTEM REQUIREMENT — TitipTemu (Baseline v2.11)

**Status:** Disetujui untuk MVP; keputusan lama sebelum A18 dicabut dan pertanyaan non-P0 yang tersisa dicatat terbuka

**Versi:** 2.11

**Platform:** Website

**Teknologi acuan:** Laravel + MySQL; Blade + Bootstrap untuk frontend

**Lingkup:** Seluruh lingkungan kampus

> Baseline ini menggantikan alur baseline lama yang menggunakan klaim kepemilikan dan penjadwalan pertemuan. **Keputusan A18 (2026-11-22) menggantikan baseline satu alur penemu:** TitipTemu kini memuat dua kanal — **Barang Hilang = laporan kehilangan oleh User pemilik**, **Barang Ditemukan = laporan temuan oleh User penemu**. Keduanya tersimpan di tabel terpisah, tidak ditautkan, dan masing-masing memakai dua card status; daftar “Sudah” tampil 30 hari lalu diarsipkan. Pertanyaan non-P0 yang masih terbuka diberi label terbuka/rekomendasi dan belum menjadi aturan final.

## 1. Ringkasan keputusan dan asumsi
| ID | Ketentuan | Status |
|---|---|---|
| ASM-01 | TitipTemu berupa website | Ditetapkan dari konsep |
| ASM-02 | Backend Laravel dan database MySQL; frontend Blade + Bootstrap | Ditetapkan |
| ASM-03 | Guest dapat mengakses informasi publik tanpa login | Ditetapkan dari konsep |
| ASM-04 | User wajib login untuk membuat laporan kehilangan, laporan temuan, dan konfirmasi status | Ditetapkan dari konsep; diperjelas A18 |
| ASM-05 | Admin memverifikasi publikasi kedua jenis laporan; konfirmasi “Barang ditemukan” pada laporan kehilangan langsung oleh pemilik, konfirmasi laporan temuan tetap diverifikasi Admin | Ditetapkan dari konsep; diperjelas A18, disesuaikan A28 |
| ASM-06 | MVP mencakup dua alur: laporan kehilangan oleh pemilik dan laporan temuan oleh penemu | Ditetapkan 2026-11-22; diperjelas A18 |
| ASM-07 | Dua menu publik: **Barang Hilang (laporan kehilangan pemilik)** dan **Barang Ditemukan (laporan temuan penemu)** | Ditetapkan A18; menggantikan keputusan lama |
| ASM-13 | Kontak pemilik/penemu hanya lewat redirect `wa.me`; nomor HP tidak ditampilkan sebagai teks di halaman/respons publik | Ditetapkan 2026-11-22; diperjelas A18 |
| ASM-14 | Akun Admin terpisah di tabel `admins`, login tersembunyi, dan beberapa akun Admin dapat berdiri sendiri | Ditetapkan 2026-11-22 |
| ASM-08 | Daftar “Sudah” pada kedua menu menampilkan barang selama 30 hari: sejak `found_at` saat pemilik mengonfirmasi laporan kehilangan (A28) atau sejak Admin menyetujui laporan temuan | Ditetapkan; diperjelas A18/A28; zona waktu Asia/Makassar |
| ASM-09 | Barang yang lewat 30 hari disembunyikan dari publik dan berstatus `Archived`, bukan dihapus permanen dari database | Ditetapkan |
| ASM-10 | Sistem memiliki peran Guest, User, dan Admin; Guest bukan akun terdaftar; User mencakup mahasiswa, dosen, dan staf aktif kampus | Ditetapkan |
| ASM-11 | Satu laporan memiliki tepat satu foto; JPG/JPEG/PNG/WebP; maksimal 2 MB per foto | Ditetapkan; diselaraskan 2026-11-23 |
| ASM-12 | Belum ada integrasi SSO akademik | Mengikuti baseline sebelumnya |
| ASM-15 | Laporan kehilangan dan laporan temuan disimpan di **tabel terpisah** dan tidak ditautkan/dicocokkan | Ditetapkan A18 |
| ASM-16 | Tiap menu publik memakai 2 card status → pencarian → card item; klik card tetap dalam jenis laporan aktif | Ditetapkan A18 |
| ASM-17 | Foto dan no. HP pemilik/penemu wajib diisi tetapi privat; kontak hanya lewat `wa.me` | Ditetapkan A18 |
| ASM-18 | Navbar User memakai dropdown **Buat Laporan**, **Akun** menampilkan daftar tanpa tombol buat laporan; sidebar Admin 5 menu dengan card status yang dapat diklik | Ditetapkan A18, disesuaikan A26 |
| ASM-19 | Pemilik laporan kehilangan dapat membatalkan `pending`/`approved` dan menandai `approved` sebagai **Barang ditemukan** langsung; Admin dapat edit/hapus laporan kehilangan di semua status | Ditetapkan A28 |

## 2. Aktor dan hak akses
### 2.1 Guest
- Membuka beranda.
- Melihat daftar **Barang Hilang** (laporan kehilangan pemilik) dan **Barang Ditemukan** (laporan temuan penemu) yang dipublikasikan.
- Memilih salah satu dari dua card status di dalam menu aktif.
- Melihat detail publik.
- Melakukan pencarian/filter di dalam menu aktif.
- Menghubungi pemilik/penemu lewat tombol WhatsApp tanpa melihat nomor HP.
- Membaca panduan dan ketentuan.
- Tidak dapat membuat laporan, mengirim konfirmasi status, atau membuka area User/Admin.
### 2.2 User
- User adalah mahasiswa, dosen, atau staf aktif kampus yang memiliki akun dan dapat bertindak sebagai **pemilik** atau **penemu**.
- Mendaftar dan login.
- Membuat **Laporkan Kehilangan Barang** dengan **no. HP pemilik**, tanggal dan lokasi **hilang**, serta foto.
- Membuat **Laporkan Barang Temuan** dengan **no. HP penemu**, tanggal dan lokasi **ditemukan**, serta foto.
- Menyetujui ketentuan pelaporan pada formulir yang dipakai.
- Melihat daftar dan detail kedua jenis laporan miliknya melalui menu **Akun**, tanpa tombol buat laporan; pembuatan tetap lewat dropdown **Buat Laporan** di navbar.
- Mengedit atau membatalkan laporan pada status menunggu verifikasi; laporan yang sudah disetujui tidak dapat diedit langsung.
- **A28:** pemilik laporan kehilangan dapat membatalkan `pending`/`approved` dan menekan **Barang ditemukan** saat `approved` tanpa bukti/form atau verifikasi Admin.
- Memperbaiki dan mengirim ulang laporan yang ditolak setelah membaca alasan Admin.
- Mengajukan konfirmasi status “Sudah” pada laporan temuan miliknya dengan **nama pemilik** dan **foto bersama penemu–pemilik**; laporan kehilangan memakai aksi langsung A28.
- Melihat status pemeriksaan konfirmasi laporan temuan.
### 2.3 Admin
- Sistem mendukung beberapa akun Admin; tidak ada asumsi satu Admin.
- Akun Admin disimpan di tabel `admins`, terpisah dari tabel `users`.
- Login melalui jalur tersembunyi `ADMIN_LOGIN_URI`; tidak ada tautan Admin di navigasi publik.
- Melihat laporan masuk.
- Memeriksa dan menyetujui/menolak laporan dengan alasan wajib saat menolak.
- Mengedit laporan temuan berstatus `Pending` dan mencatat perubahan pada riwayat. **A28:** mengedit laporan kehilangan pada semua status dengan status asal tetap tercatat.
- Mengelola laporan dan publikasi.
- Melihat antrean konfirmasi laporan temuan; konfirmasi “Barang ditemukan” laporan kehilangan tidak masuk antrean (A28).
- Memeriksa bukti privat laporan temuan.
- Menyetujui/menolak konfirmasi laporan temuan dengan alasan wajib saat menolak.
- Menambahkan alasan penolakan.
- Melihat log aktivitas dan histori status.
- Memakai sidebar 5 menu: **Dashboard**, **Cari laporan**, **Barang Hilang**, **Barang Ditemukan**, **Keluar Admin** di bawah; status tidak masuk sidebar dan card status dapat diklik tanpa mengubah jenis aktif.
- Menu **Barang Hilang**/**Barang Ditemukan** difilter menurut jenis laporan; **Cari laporan** tetap lintas jenis dan status.
- Menghapus permanen laporan temuan yang sudah selesai/arsip setelah konfirmasi. **A28:** menghapus laporan kehilangan pada semua status setelah konfirmasi, termasuk foto privat dan riwayat status.

### 2.4 Data pendaftaran dan akun
- Nama lengkap wajib.
- NIM/NIP wajib dan bersifat privat.
- Email wajib dan unik; tidak memerlukan verifikasi.
- No. HP wajib dan bersifat privat; diisi saat pendaftaran dan dapat diubah pada menu **Akun**, serta tidak ditampilkan di halaman publik. Label formulir hanya **No. HP**.
- Password wajib dan disimpan dengan hashing aman.
- Fakultas wajib dan bersifat privat.
- Program studi wajib untuk mahasiswa dan tidak berlaku untuk dosen/staf.
- User dapat melaporkan kehilangan maupun temuan setelah login; email tidak perlu diverifikasi.
- Sistem mencatat identitas kampus, tetapi tidak mencocokkannya dengan database resmi kampus.
## 3. Navigasi dan halaman
### 3.1 Publik
- Beranda
- **Barang Hilang** — laporan kehilangan pemilik; card **Barang Belum Ditemukan** / **Barang Sudah Ditemukan**
- **Barang Ditemukan** — laporan temuan penemu; card **Barang Belum Kembali** / **Barang Sudah Kembali**
- Detail Barang
- Pencarian di dalam menu aktif
- Panduan/Ketentuan
- Login
- Registrasi
### 3.2 User
- Beranda
- Dropdown **Buat Laporan**: **Laporkan Kehilangan Barang** dan **Laporkan Barang Temuan**
- **Akun** (profil, aktivitas, dan daftar kedua jenis laporan tanpa tombol buat laporan)
- Detail laporan
- Konfirmasi status “Sudah” untuk laporan temuan miliknya; tombol **Batalkan**/**Barang ditemukan** untuk laporan kehilangan (A28)
- Tombol ikon **Akun** di navbar, diikuti dropdown **Buat Laporan** di pinggir kanan; **Keluar** tersedia di halaman **Akun**, bukan navbar. Teks “Halo…” pada navbar publik dihapus
- Logout
### 3.3 Admin
- Login tersembunyi melalui `ADMIN_LOGIN_URI`
- Sidebar 5 menu: **Dashboard**, **Cari laporan**, **Barang Hilang**, **Barang Ditemukan**, **Keluar Admin** di bawah
- Dashboard dengan 2 card status yang dapat diklik; status tidak menjadi menu sidebar
- Menu **Barang Hilang**/**Barang Ditemukan** difilter menurut jenis laporan
- Cari laporan lintas jenis dan status
- Detail laporan/verifikasi, edit laporan temuan `Pending`, edit laporan kehilangan di semua status (A28), dan bukti privat
- Riwayat aktivitas
- Penghapusan permanen laporan kehilangan di semua status (A28); laporan temuan tetap selesai/arsip
## 4. Functional Requirements
| ID | Modul | Requirement | Prioritas |
|---|---|---|---|
| FR-01 | Public | Sistem menampilkan beranda tanpa login | Must |
| FR-02 | Public | Sistem menyediakan halaman Barang Hilang (laporan kehilangan) dan Barang Ditemukan (laporan temuan), masing-masing dengan 2 card status yang dapat diklik | Must |
| FR-03 | Public | Guest dapat melihat detail laporan yang statusnya boleh dipublikasi | Must |
| FR-04 | Search | Sistem menyediakan pencarian kata kunci barang | Must |
| FR-05 | Search/filter | Sistem dapat memfilter kategori, tanggal, atau lokasi jika data/filter tersebut tersedia | Should |
| FR-06 | Auth | User dapat registrasi dan login | Must |
| FR-07 | Auth | Admin dapat login ke area Admin melalui jalur tersembunyi dengan akun terpisah dari User | Must |
| FR-08 | Authorization | Sistem membatasi akses berdasarkan Guest/User/Admin | Must |
| FR-09 | Report | User login dapat membuat laporan kehilangan (sebagai pemilik) dan laporan temuan (sebagai penemu) pada tabel terpisah | Must |
| FR-10 | Report | Form laporan kehilangan meminta no. HP pemilik, data barang, tanggal dan lokasi hilang, serta foto; form laporan temuan meminta no. HP penemu, data barang, tanggal dan lokasi ditemukan, serta foto | Must |
| FR-11 | Report | Sistem memvalidasi input dan file foto | Must |
| FR-12 | Report | Laporan baru menunggu verifikasi dan tidak dipublikasikan; laporan kehilangan dan laporan temuan tidak ditautkan | Must |
| FR-13 | Admin | Admin dapat melihat antrean laporan Pending | Must |
| FR-14 | Admin | Admin dapat menyetujui atau menolak laporan | Must |
| FR-15 | Publication | Laporan kehilangan yang disetujui muncul di **Barang Hilang → Barang Belum Ditemukan**; laporan temuan yang disetujui muncul di **Barang Ditemukan → Barang Belum Kembali** | Must |
| FR-16 | User dashboard | User dapat melihat laporan yang dibuat sendiri dan statusnya | Must |
| FR-17 | User dashboard | User tidak dapat melihat atau mengubah laporan milik User lain | Must |
| FR-18 | Status confirmation | Pembuat laporan temuan dapat mengirim konfirmasi status “Sudah”; **A28:** pemilik laporan kehilangan langsung menandai `approved` sebagai **Barang ditemukan** | Must |
| FR-19 | Status confirmation | Form konfirmasi laporan temuan membutuhkan nama pemilik, foto bersama penemu–pemilik, dan persetujuan pernyataan; **A28:** laporan kehilangan tidak memakai form/bukti konfirmasi | Must |
| FR-20 | Status confirmation | Konfirmasi laporan temuan menunggu pemeriksaan Admin dan item tetap pada card “Belum”; **A28:** konfirmasi laporan kehilangan langsung berstatus `found` | Must |
| FR-21 | Admin | Admin dapat melihat antrean konfirmasi laporan temuan; **A28:** tidak ada antrean konfirmasi laporan kehilangan | Must |
| FR-22 | Admin | Admin dapat menyetujui atau menolak konfirmasi laporan temuan | Must |
| FR-23 | Admin | Admin wajib menyertakan alasan saat menolak laporan atau konfirmasi laporan temuan | Must |
| FR-24 | Status | Laporan keluar dari card “Belum” saat konfirmasi laporan temuan disetujui atau saat pemilik mengonfirmasi laporan kehilangan (A28) | Must |
| FR-25 | Status | Laporan tampil pada card “Sudah” di **menu yang sama**: kehilangan → Barang Sudah Ditemukan (langsung oleh pemilik, A28); temuan → Barang Sudah Kembali (setelah Admin) | Must |
| FR-26 | Retention | Sistem menghentikan publikasi laporan pada kedua daftar “Sudah” setelah 30 hari; untuk kehilangan dihitung dari `found_at` (A28), temuan dari persetujuan Admin | Must |
| FR-27 | Retention | Pengarsipan otomatis dilakukan tanpa memerlukan tindakan Admin manual | Must |
| FR-28 | Privacy | Sistem tidak menampilkan data pribadi atau foto bukti privat ke Guest | Must |
| FR-29 | Audit | Sistem mencatat keputusan Admin dan perubahan status penting | Should/Must untuk status inti |
| FR-30 | Admin management | Admin dapat mengelola visibilitas/arsip laporan sesuai aturan status dan retensi yang disetujui | Must |
| FR-31 | Error handling | Sistem memberi pesan ketika data wajib kosong, foto tidak valid, atau upload gagal | Must |
| FR-32 | Rules | User diwajibkan menyetujui ketentuan pelaporan pada formulir yang dipakai sebelum mengirim laporan | Must |
| FR-33 | Rules | User diwajibkan menyetujui pernyataan status “Sudah” sebelum mengirim konfirmasi | Must |
| FR-34 | Auth | User wajib login sebelum membuat laporan kehilangan, laporan temuan, atau mengirim konfirmasi status; verifikasi email tidak diperlukan | Must |
| FR-35 | Public contact | Detail publik menyediakan tombol WhatsApp yang mengarahkan ke `wa.me` tanpa menampilkan nomor HP pemilik/penemu | Must |
| FR-36 | Admin | Admin dapat mengedit laporan temuan `Pending`; **A28:** Admin dapat mengedit laporan kehilangan pada semua status dengan status asal tercatat | Must |
| FR-37 | Privacy | Nama pemilik dan bukti privat hanya dapat diakses oleh User terkait dan Admin | Must |
| FR-38 | Admin | Admin dapat menghapus permanen laporan temuan selesai/arsip setelah konfirmasi; **A28:** Admin dapat menghapus laporan kehilangan di semua status, termasuk foto privat dan riwayat status | Must |
| FR-39 | Auth | Admin terpisah tidak dapat mengakses area User dan sebaliknya; akun User lama ber-role admin tidak dapat login sebagai Admin | Must |
| FR-40 | Admin | Admin dapat mencari laporan lintas jenis dan status melalui menu **Cari laporan** | Must |
| FR-41 | Navigation | Navbar User menyediakan dropdown **Buat Laporan** berisi dua formulir; **Akun** menampilkan daftar tanpa tombol buat laporan | Must |
| FR-42 | Navigation | Teks “Halo…” dihapus dari navbar publik; pada sidebar Admin dipertahankan untuk sementara | Must |
| FR-43 | Admin UI | Sidebar Admin terdiri dari 5 menu: Dashboard, Cari laporan, Barang Hilang, Barang Ditemukan, Keluar Admin di bawah | Must |
| FR-44 | Admin UI | Status tidak menjadi menu sidebar; card status pada Dashboard dapat diklik dan tetap dalam jenis laporan aktif | Must |
| FR-45 | Admin filter | Menu Admin **Barang Hilang** dan **Barang Ditemukan** difilter menurut jenis laporan | Must |
| FR-46 | Data | Laporan kehilangan dan laporan temuan disimpan di tabel terpisah tanpa penautan atau pencocokan | Must |
| FR-47 | Public UI | Klik card status pada menu publik membawa ke tampilan/state berbeda di dalam jenis laporan aktif | Must |
| FR-48 | User action (A28) | Pemilik laporan kehilangan dapat membatalkan `pending`/`approved` dan menandai `approved` sebagai **Barang ditemukan** dari kelola laporan serta ringkasan Akun | Must |
| FR-49 | Admin filter (A28) | Status lama `found_pending` tidak ditawarkan sebagai filter Admin pada jenis kehilangan; data lama tetap disimpan | Must |

## 5. Alur proses sistem
### 5.1 Membuat laporan kehilangan (pemilik)
1. User login, lalu membuka **Buat Laporan → Laporkan Kehilangan Barang** atau tombol di **Akun**.
2. User mengisi no. HP pemilik, nama/kategori/deskripsi barang, dan tanggal serta lokasi **hilang**.
3. User mengunggah foto dan mencentang persetujuan ketentuan pelaporan kehilangan.
4. Sistem memvalidasi, lalu menyimpan ke **tabel laporan kehilangan** dengan status menunggu verifikasi.
5. Admin meninjau dan dapat mengedit laporan pada status menunggu verifikasi.
6. Admin menyetujui atau menolak dengan alasan wajib saat menolak.
7. Jika disetujui, laporan tampil pada **Barang Hilang → Barang Belum Ditemukan** beserta tombol WhatsApp. Jika ditolak, User dapat memperbaiki dan mengirim ulang.
### 5.2 Membuat laporan temuan (penemu)
1. User login, lalu membuka **Buat Laporan → Laporkan Barang Temuan** atau tombol di **Akun**.
2. User mengisi no. HP penemu, nama/kategori/deskripsi barang, dan tanggal serta lokasi **ditemukan**.
3. User mengunggah foto dan mencentang persetujuan ketentuan pelaporan temuan.
4. Sistem memvalidasi, lalu menyimpan ke **tabel laporan temuan** (`lost_reports`) dengan status menunggu verifikasi.
5. Admin meninjau, lalu menyetujui atau menolak dengan alasan wajib saat menolak.
6. Jika disetujui, laporan tampil pada **Barang Ditemukan → Barang Belum Kembali** beserta tombol WhatsApp. Jika ditolak, User dapat memperbaiki dan mengirim ulang.
7. Laporan kehilangan dan laporan temuan tidak dicocokkan atau ditautkan, walau barangnya sama.
### 5.3 Guest menghubungi pelapor
1. Guest membuka daftar pada menu aktif, memilih card status, lalu membuka detail laporan.
2. Guest menekan tombol WhatsApp.
3. Sistem membuat tautan `wa.me` dari nomor pemilik/penemu yang disimpan dan mengarahkan browser ke sana.
4. Nomor HP tidak pernah dikirim sebagai teks pada halaman atau respons publik.
### 5.4 Konfirmasi status “Sudah” dan arsip (A18, disesuaikan A28)
1. **Laporan kehilangan (A28):** User pemilik menekan **Barang ditemukan** di kelola laporan atau ringkasan **Akun** saat status `approved`. Sistem langsung menulis `found`, `found_at = sekarang`, riwayat `found_by_owner`, dan memindahkan laporan ke **Barang Sudah Ditemukan**; tidak ada bukti, form, `found_pending`, atau verifikasi Admin.
2. **Laporan kehilangan (A28):** User pemilik dapat menekan **Batalkan** saat `pending`/`approved`; sistem menulis `cancelled` dan riwayat `cancel_by_owner`.
3. **Laporan temuan:** penemu mengisi nama pemilik, mengunggah foto bersama penemu–pemilik, menambah catatan opsional, dan menyetujui pernyataan. Sistem menyimpan konfirmasi menunggu pemeriksaan Admin; item tetap pada card “Belum”.
4. Admin meninjau data dan bukti laporan temuan melalui halaman privat, lalu menyetujui atau menolak dengan alasan wajib saat menolak.
5. Laporan berpindah ke card “Sudah” **di menu yang sama** — kehilangan ke **Barang Sudah Ditemukan**, temuan ke **Barang Sudah Kembali**.
6. Periode 30 hari dihitung dari `found_at` untuk laporan kehilangan (A28) atau dari timestamp persetujuan Admin untuk laporan temuan. Setelah batas, sistem mengarsipkan dan menghilangkan item dari daftar “Sudah”.
7. Penolakan konfirmasi hanya berlaku untuk laporan temuan; User dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
8. **A28:** Admin dapat mengedit dan menghapus permanen laporan kehilangan pada semua status setelah konfirmasi, termasuk foto privat dan riwayat status. Laporan temuan tetap hanya dapat dihapus pada status `returned`/`archived`.
### 5.5 Admin memverifikasi laporan
1. Admin login melalui jalur tersembunyi.
2. Admin melihat Dashboard yang merangkum kedua jenis laporan; card status dapat diklik dan tetap dalam jenis aktif.
3. Admin membuka detail, foto, dan data laporan.
4. Admin dapat mengedit laporan temuan pada status menunggu verifikasi; **A28:** Admin dapat mengedit laporan kehilangan pada semua status, dengan status asal tercatat pada riwayat.
5. Admin menyetujui atau menolak dengan alasan wajib saat menolak.
6. Sistem menyimpan keputusan, waktu, Admin yang mengambil tindakan, dan alasan penolakan.
### 5.6 Admin memverifikasi konfirmasi status laporan temuan
1. Admin membuka antrean konfirmasi laporan temuan dari menu terkait (atau **Cari laporan** lintas jenis); konfirmasi laporan kehilangan tidak masuk antrean sejak A28.
2. Admin melihat data konfirmasi dan bukti yang aksesnya privat.
3. Admin menyetujui atau menolak.
4. Sistem mencatat keputusan, waktu, Admin, dan alasan penolakan wajib jika ditolak.
## 6. Aturan validasi form laporan
Berlaku untuk kedua formulir; yang berbeda hanya makna tanggal/lokasi dan nomor HP.

| Field | Validasi awal | Status |
|---|---|---|
| Nama barang | Wajib; 2–100 karakter | Rekomendasi |
| Kategori | Wajib dipilih dari daftar tetap yang disetujui | Ditetapkan |
| Deskripsi | Wajib; 10–1000 karakter | Rekomendasi |
| No. HP pemilik/penemu | Wajib; format nomor dapat dinormalisasi menjadi `wa.me`; bersifat privat | Ditetapkan 2026-11-22; diperjelas A18 |
| Tanggal hilang / ditemukan | Wajib; tidak boleh tanggal masa depan | Rekomendasi; diperjelas A18 |
| Lokasi hilang / ditemukan | Wajib; 3–150 karakter | Rekomendasi; diperjelas A18 |
| Foto barang | Wajib; tepat satu file | Ditetapkan; diselaraskan 2026-11-23 |
| Format foto | JPG/JPEG/PNG/WebP | Ditetapkan |
| Ukuran foto | Maksimal 2 MB per foto | Ditetapkan |
| Penyimpanan foto | Storage lokal Laravel; tidak publik sebelum laporan disetujui Admin | Ditetapkan |
| Ketentuan | Checkbox persetujuan ketentuan pelaporan sesuai formulir wajib | Ditetapkan dari konsep; diperjelas A18 |
| Kontak | No. HP pemilik/penemu tidak ditampilkan kepada publik; kontak lewat `wa.me` | Ditetapkan 2026-11-22; diperjelas A18 |

Kategori tetap yang berlaku (lima pilihan): Elektronik; Dokumen; Aksesori; Pakaian; Lainnya.

**Penyelarasan 2026-11-23:** dokumen mengikuti implementasi untuk foto dan kategori — tepat satu foto per laporan dan lima pilihan kategori di atas. Keputusan P0.4 lama tentang maksimal tiga foto dan delapan kategori dicatat sebagai riwayat, bukan aturan aktif.
## 7. Validasi form konfirmasi status “Sudah” (laporan temuan)
| Field | Validasi awal | Status |
|---|---|---|
| Laporan terkait | Wajib; harus milik User yang login dan berstatus aktif (“Belum”) | Must |
| Nama pemilik | Wajib; 2–100 karakter; bersifat privat | Ditetapkan 2026-11-22 |
| Foto bersama penemu–pemilik | Wajib; 1–3 file JPG/JPEG/PNG/WebP, maksimal 2 MB per file, storage privat | Ditetapkan |
| Tanggal pengembalian | Dicatat otomatis saat Admin menyetujui | Ditetapkan |
| Catatan pengembalian | Opsional; maksimal 500 karakter; jangan memuat kontak pribadi | Ditetapkan |
| Pernyataan pengembalian | Checkbox wajib | Ditetapkan dari konsep |
| Catatan Admin | Wajib diisi ketika menolak | Ditetapkan |

> Validasi di atas mengikat konfirmasi status “Sudah” pada **laporan temuan**. **A28:** validasi untuk konfirmasi **laporan kehilangan** sudah ditetapkan — pemilik menekan **Barang ditemukan** saat status `approved`, sistem langsung mencatat `found_at` dan riwayat `found_by_owner`; tidak ada field, bukti, form, `found_pending`, atau verifikasi Admin tambahan.
## 8. Aturan bisnis
| ID | Aturan |
|---|---|
| BR-01 | Sistem berlaku untuk seluruh lingkungan kampus. |
| BR-02 | Guest dapat membaca konten publik tanpa login. |
| BR-03 | User wajib login untuk membuat laporan kehilangan, laporan temuan, dan konfirmasi status; verifikasi email tidak diperlukan. |
| BR-04 | Kedua jenis laporan disimpan di tabel terpisah dan tidak ditautkan/dicocokkan. |
| BR-05 | Laporan kehilangan dan laporan temuan harus menyertakan no. HP pelapor, foto, dan data wajib lengkap. |
| BR-06 | Laporan baru menunggu verifikasi dan tidak dipublikasikan sebelum Admin menyetujui. |
| BR-07 | Laporan kehilangan yang disetujui tampil di **Barang Hilang → Barang Belum Ditemukan**; laporan temuan yang disetujui tampil di **Barang Ditemukan → Barang Belum Kembali**. Status lain tidak tampil. |
| BR-08 | Nomor HP pemilik maupun penemu tidak ditampilkan sebagai teks pada halaman/respons publik; kontak hanya lewat redirect `wa.me`. |
| BR-09 | Konfirmasi status hanya dapat diajukan oleh User pembuat laporan; **A28:** aksi **Barang ditemukan** hanya untuk pemilik laporan kehilangan `approved`. |
| BR-10 | Konfirmasi status “Sudah” laporan temuan wajib menyertakan nama pemilik, foto bersama penemu–pemilik, dan persetujuan pernyataan. **A28:** laporan kehilangan tidak memakai form/bukti konfirmasi. |
| BR-11 | Nama pemilik dan bukti laporan temuan bersifat privat; hanya User terkait dan Admin yang dapat melihatnya. |
| BR-12 | Konfirmasi laporan temuan harus disetujui Admin sebelum perpindahan card. **A28:** konfirmasi laporan kehilangan langsung berstatus `found` tanpa verifikasi Admin. |
| BR-13 | Laporan kehilangan pindah ke **Barang Sudah Ditemukan** saat pemilik menekan **Barang ditemukan** (A28); laporan temuan pindah ke **Barang Sudah Kembali** setelah Admin menyetujui, tetap di menu yang sama. |
| BR-14 | Kedua daftar “Sudah” tampil 30 hari: sejak `found_at` untuk laporan kehilangan (A28) atau sejak persetujuan Admin untuk laporan temuan, zona waktu Asia/Makassar (WITA), lalu tidak tampil publik dan berstatus `Archived`. |
| BR-15 | Pengarsipan 30 hari tidak menghapus data secara permanen; data tetap disimpan dan bukti tetap privat. |
| BR-16 | Bukti konfirmasi, nama pemilik, data pribadi, dan nomor HP pemilik/penemu bersifat terbatas dan bukan konten publik. |
| BR-17 | Admin wajib menyertakan alasan saat menolak laporan atau konfirmasi laporan temuan. |
| BR-18 | Pembuat laporan dapat mengedit atau membatalkan laporan pada status menunggu verifikasi; laporan yang sudah disetujui tidak dapat diedit langsung. **A28:** pemilik laporan kehilangan dapat membatalkan `approved` dan menandainya **Barang ditemukan**. |
| BR-19 | Admin dapat mengedit laporan temuan pada status menunggu verifikasi; **A28:** Admin dapat mengedit laporan kehilangan pada semua status dengan status asal tercatat pada riwayat. |
| BR-20 | Pembuat laporan dapat memperbaiki dan mengirim ulang laporan yang ditolak setelah membaca alasan Admin. |
| BR-21 | Konfirmasi laporan temuan yang ditolak wajib memiliki alasan; pembuat laporan dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin. |
| BR-22 | Data laporan, data konfirmasi, dan riwayat status tidak dihapus permanen, kecuali Admin melakukan penghapusan manual sesuai BR-23. |
| BR-23 | Admin dapat menghapus permanen laporan temuan selesai/arsip setelah konfirmasi. **A28:** Admin dapat menghapus laporan kehilangan pada semua status setelah konfirmasi; penghapusan mencakup foto privat dan riwayat status. Aturan lama tetap berlaku untuk laporan temuan. |
| BR-24 | Akun Admin terpisah dari akun User dan masuk melalui jalur tersembunyi; beberapa akun Admin dapat berdiri sendiri. |
| BR-25 | Klik card status pada menu publik mengarah ke state lain **di dalam jenis laporan aktif**, bukan ke menu atau jenis lain. |
| BR-26 | Navbar User memakai dropdown **Buat Laporan** berisi dua formulir; **Akun** menampilkan daftar tanpa tombol buat laporan. Teks “Halo…” dihapus dari navbar publik, sedangkan di sidebar Admin dipertahankan sementara. Kolom status di tabel **Akun** dirata-tengahkan; tautan **Detail** di daftar pengelolaan berbentuk tombol dengan rute sesuai jenis. |
| BR-27 | Sidebar Admin berisi 5 menu — Dashboard, Cari laporan, Barang Hilang, Barang Ditemukan, Keluar Admin di bawah. Status tidak masuk sidebar; card status dapat diklik dan tetap dalam jenis aktif. |
| BR-28 | Menu Admin **Barang Hilang** dan **Barang Ditemukan** difilter menurut jenis laporan; **Cari laporan** tetap lintas jenis dan status. |
| BR-29 (A28) | Status lama `found_pending` pada laporan kehilangan tidak dipakai untuk transisi baru; data lama tetap disimpan dan opsi filter Admin untuk status tersebut disembunyikan pada jenis kehilangan. |
| BR-30 (A28) | Tombol **Barang ditemukan** tersedia hanya untuk laporan kehilangan `approved` di kelola laporan dan ringkasan Akun; tombol **Batalkan** tersedia untuk `pending`/`approved`. |
## 9. Status laporan dan transisi
Kedua tabel memakai siklus setara — menunggu verifikasi → aktif (“Belum”) → konfirmasi “Sudah” → arsip. **A28:** pada laporan kehilangan, konfirmasi “Sudah” terjadi langsung saat pemilik menekan **Barang ditemukan**; `found_pending` lama tidak dipakai untuk transisi baru. Konstanta internal laporan kehilangan (A19): `pending`, `approved`, `rejected`, `found_pending`, `found`, `archived`, `cancelled`; label publik mengikuti jenis laporan:

| Jenis laporan | Menu | Card “Belum” | Card “Sudah” |
|---|---|---|---|
| Kehilangan (pemilik) | Barang Hilang | **Barang Belum Ditemukan** | **Barang Sudah Ditemukan** |
| Temuan (penemu) | Barang Ditemukan | **Barang Belum Kembali** | **Barang Sudah Kembali** |

| Status umum | Deskripsi | Visibilitas publik |
|---|---|---|
| Menunggu verifikasi | Laporan baru | Tidak |
| Aktif / Belum | Disetujui Admin | Card “Belum” pada menu sesuai jenis |
| Konfirmasi Sudah | Khusus laporan temuan: penemu mengirim konfirmasi, menunggu Admin | Tetap card “Belum” sampai keputusan Admin |
| Sudah | Kehilangan: langsung setelah konfirmasi pemilik (A28); temuan: setelah Admin menyetujui | Card “Sudah” pada menu yang sama (30 hari) |
| Arsip | Lewat masa 30 hari | Tidak |
| Ditolak | Admin menolak dengan alasan | Tidak |
| Dibatalkan | Kehilangan `pending`/`approved` dibatalkan pemilik (A28); temuan `pending` dibatalkan pembuatnya | Tidak |

Admin dapat menghapus permanen laporan kehilangan pada semua status setelah konfirmasi (A28). Laporan temuan tetap hanya `returned`/`archived` setelah konfirmasi; status lain tidak dapat dihapus.

**Transisi normal:** `Menunggu verifikasi → Aktif/Belum → Konfirmasi Sudah → Sudah → Arsip`.
Penolakan laporan: `Menunggu verifikasi → Ditolak`, dengan alasan Admin; pembuat laporan dapat memperbaiki dan mengirim ulang.

Penolakan konfirmasi laporan temuan: `Konfirmasi Sudah → Aktif/Belum`, dengan alasan Admin; pembuat laporan dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.

**A28 (kehilangan):** `Aktif/Belum → Sudah` langsung oleh pemilik; `Aktif/Belum → Dibatalkan` dan `Menunggu verifikasi → Dibatalkan` juga langsung oleh pemilik. Kedua alur berjalan sendiri-sendiri pada tabel masing-masing.
## 10. Aturan 30 hari
- Masa tampil 30 hari berlaku untuk **kedua daftar “Sudah”**: **Barang Sudah Ditemukan** dan **Barang Sudah Kembali**.
- **A28:** awal hitungan laporan kehilangan adalah `found_at`, yaitu timestamp saat User pemilik menekan **Barang ditemukan**; laporan temuan tetap dihitung dari timestamp saat Admin menyetujui konfirmasi dan mempublikasikannya.
- Zona waktu yang digunakan adalah Asia/Makassar (WITA).
- Batas arsip dihitung dari timestamp acuan masing-masing jenis (kehilangan: `found_at`; temuan: persetujuan Admin) ditambah 30 hari.
- Sistem menyimpan timestamp acuan tersebut dan menjalankan proses terjadwal untuk memeriksa masa tampil pada kedua tabel. Dua command harian `reports:archive-returned` dan `missing-reports:archive-found` terdaftar pada scheduler Laravel pukul `00:05` WITA; `MissingReport::publicFound()` juga menyembunyikan laporan kehilangan setelah 30 hari. Entri cron `schedule:run` tetap merupakan konfigurasi server yang dipasang saat deployment, bukan perubahan kode.
- Setelah batas lewat, laporan berstatus `Archived` dan tidak ditampilkan pada halaman publik.
- Data laporan tetap disimpan di database, tidak dihapus permanen oleh proses arsip otomatis. Penghapusan permanen dapat dilakukan manual oleh Admin setelah konfirmasi: laporan kehilangan pada semua status (A28), laporan temuan hanya `returned`/`archived`.
- Foto/bukti privat tetap privat dan tidak ditampilkan kepada publik.
## 11. Informasi publik vs privat
### Publik
- Nama barang.
- Kategori.
- Deskripsi yang telah diverifikasi.
- Foto publik laporan.
- Tanggal dan lokasi **hilang** (Barang Hilang) atau **ditemukan** (Barang Ditemukan); tingkat detail masih terbuka.
- Status laporan (“Belum”/“Sudah”).
- Tombol WhatsApp untuk menghubungi pemilik/penemu (tanpa menampilkan nomor HP).
### Privat
- NIM atau identitas mahasiswa.
- Email pribadi dan kontak pribadi lainnya.
- **Nomor HP pemilik maupun penemu** — tidak dirender sebagai teks; hanya dipakai server untuk tautan `wa.me`.
- **Nama pemilik** dan **bukti/status privat** — dapat diakses User terkait dan Admin.
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
**Sebagai Guest**, saya ingin melihat Barang Hilang (laporan kehilangan) dan Barang Ditemukan (laporan temuan) tanpa login agar dapat mencari informasi.
**Acceptance:** halaman publik dapat dibuka; tiap menu menampilkan 2 card status yang dapat diklik dan tetap dalam jenis aktif; hanya item publik yang tampil; data privat tidak tampil.
### US-02 — User melaporkan barang temuan
**Sebagai User/penemu**, saya ingin melaporkan barang yang saya temukan dengan no. HP, data barang, dan foto agar laporan dapat diperiksa Admin.
**Acceptance:** login diperlukan; no. HP penemu, tanggal dan lokasi ditemukan, serta foto wajib; persetujuan ketentuan wajib; laporan tersimpan pada tabel laporan temuan dengan status menunggu verifikasi dan belum tampil publik; laporan tampil di Barang Ditemukan → Barang Belum Kembali setelah disetujui.
### US-02a — User melaporkan kehilangan barang
**Sebagai User/pemilik**, saya ingin melaporkan barang yang saya hilangkan dengan no. HP, data barang, dan foto agar laporan dapat diperiksa Admin.
**Acceptance:** login diperlukan; no. HP pemilik, tanggal dan lokasi hilang, serta foto wajib; laporan tersimpan pada tabel terpisah, tidak ditautkan ke laporan temuan; laporan tampil di Barang Hilang → Barang Belum Ditemukan setelah disetujui.
### US-03 — Admin menyetujui laporan
**Sebagai Admin**, saya ingin memeriksa kedua jenis laporan agar hanya laporan yang sesuai yang dipublikasikan.
**Acceptance:** Admin dapat melihat detail; menyetujui atau menolak; keputusan tercatat; laporan kehilangan tampil di Barang Hilang dan laporan temuan di Barang Ditemukan sesuai card “Belum”.
### US-04 — User memantau laporan
**Sebagai User**, saya ingin melihat laporan dan status milik saya.  
**Acceptance:** User hanya melihat laporan miliknya; status dan keputusan terlihat; laporan user lain tidak dapat diakses lewat URL langsung.
### US-05 — User mengonfirmasi status “Sudah”
**Sebagai User pembuat laporan**, saya ingin mengonfirmasi barang sudah ditemukan/sudah kembali agar status laporan diperbarui.
**Acceptance:** hanya pemilik laporan terkait; **A28:** untuk laporan kehilangan `approved`, tombol **Barang ditemukan** di kelola laporan atau ringkasan **Akun** langsung menulis `found`, `found_at = sekarang`, dan riwayat `found_by_owner`, tanpa bukti/form/`found_pending` maupun verifikasi Admin; untuk laporan temuan, nama pemilik dan foto bersama penemu–pemilik wajib dan tidak tampil publik, pernyataan wajib dicentang, konfirmasi masuk antrean Admin, dan item belum pindah card sebelum disetujui Admin. Pemilik juga dapat membatalkan laporan kehilangan `pending`/`approved` (A28).
### US-06 — Admin menyetujui konfirmasi status laporan temuan
**Sebagai Admin**, saya ingin memeriksa bukti konfirmasi agar perpindahan card benar.
**Acceptance:** Admin dapat menyetujui/menolak konfirmasi laporan temuan; keputusan tercatat; jika disetujui laporan pindah ke card “Sudah” di menu yang sama; jika ditolak item tetap di card “Belum”, alasan wajib tercatat, dan User dapat memperbaiki serta mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin. Konfirmasi laporan kehilangan tidak masuk antrean Admin sejak A28.
### US-07 — Item pada daftar “Sudah” diarsipkan otomatis
**Sebagai pengelola**, saya ingin item tidak lagi muncul setelah 30 hari agar kedua daftar “Sudah” tetap relevan.
**Acceptance:** sistem menyimpan tanggal acuan; proses terjadwal mengarsipkan item yang melewati 30 hari pada kedua tabel; item Archived tidak tampil publik; data dan histori tidak dihapus oleh proses arsip, kecuali penghapusan manual Admin sesuai BR-23.
### US-08 — Admin mengelola verifikasi
**Sebagai Admin**, saya ingin melihat antrean laporan dan konfirmasi pengembalian agar tugas verifikasi terorganisasi.  
**Acceptance:** antrean dapat difilter menurut status; detail dapat diperiksa; keputusan tersimpan beserta waktu dan Admin.
### US-09 — Guest menghubungi pelapor
**Sebagai Guest**, saya ingin menghubungi pemilik atau penemu lewat WhatsApp tanpa melihat nomor HP mereka.
**Acceptance:** tombol tersedia pada detail laporan yang dipublikasikan; tautan mengarah ke `wa.me`; nomor HP tidak muncul pada HTML atau respons publik.
### US-10 — Admin mengedit laporan
**Sebagai Admin**, saya ingin memperbaiki laporan sebelum memutuskan agar data publik menjadi akurat.
**Acceptance:** edit laporan temuan hanya tersedia untuk `Pending`; **A28:** edit laporan kehilangan tersedia pada semua status; perubahan disimpan dan tercatat pada riwayat dengan peninjau Admin serta status asal; laporan temuan `Approved` tetap tidak dapat diedit langsung oleh User.
### US-11 — Admin menghapus laporan
**Sebagai Admin**, saya ingin menghapus permanen laporan usang agar data tidak menumpuk.
**Acceptance:** penghapusan memerlukan konfirmasi; **A28:** laporan kehilangan dapat dihapus pada semua status beserta foto privat dan riwayat status; laporan temuan tetap hanya `returned`/`archived`. Status lain untuk laporan temuan ditolak.
### US-12 — User membuat laporan lewat dropdown
**Sebagai User**, saya ingin memilih jenis laporan dari dropdown **Buat Laporan** di navbar dan memantau hasilnya di **Akun**.
**Acceptance:** dropdown berisi **Laporkan Kehilangan Barang** dan **Laporkan Barang Temuan**; **Akun** tidak menampilkan tombol buat laporan; teks “Halo…” tidak muncul di navbar publik.
### US-13 — Admin memakai sidebar 5 menu
**Sebagai Admin**, saya ingin sidebar berisi 5 menu agar navigasi tidak penuh.
**Acceptance:** sidebar menampilkan Dashboard, Cari laporan, Barang Hilang, Barang Ditemukan, dan Keluar Admin di bawah; status tidak menjadi menu; teks “Halo…” di sidebar dipertahankan untuk sementara.
### US-14 — Admin mengklik card status
**Sebagai Admin**, saya ingin mengklik card status pada Dashboard untuk membuka daftar sesuai status.
**Acceptance:** card dapat diklik; filter yang dihasilkan tetap berada dalam jenis laporan yang sedang aktif; tidak berpindah ke menu jenis lain.
## 14. Error dan kondisi gagal
- Field wajib tidak lengkap: sistem menolak submit dan memberi penjelasan.
- Foto tidak ada, format salah, ukuran terlalu besar, atau upload gagal: sistem menolak/menampilkan error yang jelas dan tidak membuat laporan publik.
- User belum login: diarahkan ke autentikasi sebelum membuat laporan kehilangan/temuan atau konfirmasi status.
- User mencoba mengakses laporan orang lain: akses ditolak.
- Admin menolak laporan: status ditolak dan alasan wajib tercatat; User dapat memperbaiki dan mengirim ulang.
- Admin menolak konfirmasi: item tetap pada card “Belum” di menu yang sama, alasan wajib tercatat; User dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
- Proses arsip otomatis gagal: item tidak dihapus permanen; kegagalan perlu log dan dapat diproses ulang.
- Admin mencoba menghapus laporan berstatus selain `returned`/`archived`: permintaan ditolak dan data tetap ada.
- Akun Admin mencoba mengakses area User atau akun User mencoba masuk ke area Admin: akses ditolak/diarahkan ke login yang sesuai.
## 15. Kriteria MVP selesai
- Guest dapat melihat Barang Hilang (laporan kehilangan) dan Barang Ditemukan (laporan temuan) tanpa login, masing-masing dengan 2 card status yang dapat diklik.
- Registrasi dan login User berfungsi; Admin masuk lewat jalur tersembunyi dengan akun terpisah.
- User dapat melaporkan kehilangan dan temuan dengan no. HP pelapor, data barang, foto, serta menyetujui ketentuan; keduanya tersimpan di tabel terpisah dan tidak ditautkan.
- Navbar User memakai dropdown **Buat Laporan**; **Akun** menampilkan daftar tanpa tombol buat laporan; teks “Halo…” tidak ada di navbar publik.
- Admin dapat menyetujui/menolak kedua jenis laporan, mengedit laporan temuan pada status menunggu verifikasi, dan mengedit laporan kehilangan pada semua status (A28).
- Laporan kehilangan yang disetujui muncul di Barang Hilang; laporan temuan yang disetujui muncul di Barang Ditemukan.
- User dapat melihat riwayat kedua jenis laporan miliknya.
- User dapat mengedit atau membatalkan laporan pada status menunggu verifikasi; laporan yang sudah disetujui tidak dapat diedit langsung.
- Penolakan laporan dan konfirmasi laporan temuan selalu disertai alasan Admin; konfirmasi “Barang ditemukan” laporan kehilangan langsung oleh pemilik tanpa verifikasi Admin (A28).
- User dapat memperbaiki dan mengirim ulang laporan yang ditolak, serta konfirmasi yang ditolak maksimal 3 kali sebelum penyelesaian melalui Admin.
- User dapat mengirim konfirmasi status “Sudah” pada laporan temuan miliknya dengan nama pemilik, foto bersama penemu–pemilik, dan persetujuan pernyataan; laporan kehilangan memakai tombol **Barang ditemukan** langsung oleh pemilik (A28).
- Nomor HP pemilik/penemu tidak tampil di halaman publik.
- Admin dapat menyetujui/menolak konfirmasi laporan temuan; sidebar Admin berisi 5 menu dan card status dapat diklik tanpa mengubah jenis aktif.
- Laporan kehilangan memindahkan item ke card “Sudah” saat dikonfirmasi pemilik (A28); laporan temuan memindahkan item setelah konfirmasi disetujui Admin, keduanya di menu yang sama.
- Item pada kedua daftar “Sudah” tidak tampil lagi setelah 30 hari (kehilangan sejak `found_at`, temuan sejak persetujuan Admin).
- Akses privat, peran terpisah, jalur Admin tersembunyi, dan validasi file diterapkan.
- Admin dapat menghapus permanen laporan kehilangan pada semua status setelah konfirmasi, termasuk foto dan riwayat (A28); laporan temuan tetap hanya `returned`/`archived`.
- Dokumentasi instalasi, konfigurasi, dan penggunaan tersedia di repository.
## 16. Pertanyaan dan status keputusan
Pertanyaan P0 yang sudah dijawab tetap dicatat di bawah ini. Pertanyaan non-P0 yang belum dijawab ditandai terbuka.
1. Barang Ditemukan: hanya barang yang sudah dikembalikan, atau juga laporan barang temuan baru? **Keputusan lama 2026-11-22: hanya laporan yang pengembaliannya disetujui Admin. Keputusan ini DICABUT oleh A18 (2026-11-22): Barang Ditemukan adalah kanal laporan temuan penemu; Barang Hilang adalah kanal laporan kehilangan pemilik.**
2. Lingkup akun: mahasiswa saja atau seluruh civitas akademika? **Sudah diputuskan: mahasiswa, dosen, dan staf aktif kampus.**
3. Registrasi: data apa yang diminta dan apakah email kampus diverifikasi? **Sudah diputuskan: pendaftaran meminta nama lengkap, NIM/NIP, email, password, fakultas, dan program studi khusus mahasiswa; identitas tidak diverifikasi melalui database resmi kampus. Sesuai A11 (2026-11-22), email tidak perlu diverifikasi sebelum pelaporan.**
4. Data form laporan barang temuan: setujukah dengan no. HP penemu, nama barang, kategori, deskripsi, tanggal dan lokasi ditemukan, foto, dan ketentuan? **Keputusan 2026-11-22: ya, seluruh field tersebut wajib; tanggal dan lokasi bermakna saat barang ditemukan.**
5. Batas foto: jumlah maksimum, ukuran maksimum, dan format? **Keputusan 2026-11-23: satu foto per laporan; JPG/JPEG/PNG/WebP; maksimal 2 MB per foto. Keputusan lama “maksimal 3 foto” digantikan.**
6. Ketentuan pelaporan: apa saja yang wajib disetujui User?
7. Kontak penemu: ditampilkan, disembunyikan, atau komunikasi melalui Admin? **Keputusan 2026-11-22: nomor HP tidak ditampilkan; Guest menghubungi penemu lewat tombol WhatsApp (`wa.me`) yang dibuat server, tanpa WhatsApp API atau layanan berbayar.**
8. Bukti pengembalian: foto seperti apa yang wajib diunggah, apakah hanya dilihat Admin, dan berapa batas format/ukurannya? **Keputusan 2026-11-22: wajib foto bersama penemu–pemilik dan nama pemilik; dapat dilihat User terkait dan Admin, bukan publik. Batas implementasi: 1–3 file JPG/JPEG/PNG/WebP, maksimal 2 MB per file.**
9. Apakah laporan boleh diedit/dibatalkan oleh User? Pada status apa? **Keputusan lama: pemilik dapat mengedit/membatalkan laporan `Pending`; laporan `Approved` tidak dapat diedit langsung. A28 (2026-11-23): pemilik laporan kehilangan juga dapat membatalkan `Approved` dan menandainya **Barang ditemukan**; tetap tidak dapat mengedit langsung laporan `Approved`. Perubahan ini tidak berlaku untuk laporan temuan.**
10. Jika konfirmasi pengembalian ditolak, apakah laporan tetap Barang Hilang dan User boleh mengajukan ulang? **Sudah diputuskan: laporan tetap Barang Hilang, alasan wajib dicatat, dan pengiriman ulang maksimal 3 kali; setelah itu penyelesaian melalui Admin.**
11. 30 hari dihitung sejak User mengisi tanggal kembali atau sejak Admin menyetujui? **Laporan temuan: sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya. A28 untuk laporan kehilangan: sejak User pemilik menekan **Barang ditemukan** (`found_at`). Zona waktu Asia/Makassar (WITA).**
12. Setelah 30 hari apakah diarsipkan (data tetap ada) atau dihapus permanen? **Sudah diputuskan: diarsipkan dan disembunyikan; data tetap disimpan, foto bukti tetap privat.**
13. Apakah Admin dapat menghapus laporan atau hanya menolak/arsip? **Keputusan lama 2026-11-22: Admin dapat menghapus permanen laporan `returned`/`archived` setelah konfirmasi. A28 (2026-11-23) menggantinya untuk laporan kehilangan: Admin dapat mengedit dan menghapus pada semua status dengan konfirmasi, termasuk foto privat dan riwayat. Aturan lama tetap berlaku untuk laporan temuan; arsip otomatis 30 hari dan retensi data tidak berubah.**
14. Unit penerapan yang pasti: fakultas, prodi, atau seluruh kampus? **Sudah diputuskan: seluruh lingkungan kampus.**
15. Apakah diperlukan fitur tambahan seperti lokasi titik/gedung, kontak melalui Admin, atau notifikasi status? **Keputusan 2026-11-22: kontak publik memakai WhatsApp (`wa.me`) yang dibuat server, bukan melalui Admin dan tanpa nomor HP tampil; lokasi titik/gedung serta notifikasi status masih terbuka.**
16. Apakah Admin dapat mengelola kategori, atau kategori tetap saja? **Admin belum dapat mengelola kategori; yang berlaku: lima kategori tetap — Elektronik, Dokumen, Aksesori, Pakaian, Lainnya (diselaraskan 2026-11-23).**
17. Tanggal pengembalian wajib diisi atau dicatat otomatis, dan catatan pengembalian wajib atau opsional? **Terbuka.**
18. Field dan bukti konfirmasi untuk transisi laporan kehilangan ke **Barang Sudah Ditemukan**? **Selesai A28 (2026-11-23): tidak ada field/bukti/form tersendiri. Pemilik menekan **Barang ditemukan** saat `approved`; sistem mencatat `found_at` dan riwayat `found_by_owner`, lalu status langsung `found`.**
19. Apakah URI publik `/barang/lost` dan `/barang/found` diganti mengikuti pemetaan baru? **Ya, kanoniknya `/barang/hilang` dan `/barang/ditemukan` sejak 2026-11-23. URI lama tetap melayani redirect 301 agar tautan lama tidak rusak.**
20. Apakah teks “Halo…” di sidebar Admin ikut dihapus? **Keputusan A18: dipertahankan dulu; hapus hanya jika hasil akhir tampilan terasa penuh.**
## 17. Revisi
| Versi | Perubahan |
|---|---|
| 1.1 | Baseline lama berisi laporan barang hilang dan ditemukan terpisah, klaim digital, serta proses pertemuan langsung. |
| 2.0 | Disederhanakan sesuai konsep TitipTemu: laporan kehilangan, verifikasi Admin, konfirmasi pengembalian oleh pemilik laporan dengan foto, perpindahan ke Barang Ditemukan, dan pengarsipan publik 30 hari. |
| 2.1 | P0.6 ditetapkan: 30 hari sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya, zona waktu Asia/Makassar, lalu `Archived` tanpa penghapusan permanen; foto bukti tetap privat. **Catatan A28 (2026-11-23):** aturan ini tetap berlaku untuk laporan temuan; laporan kehilangan dihitung sejak `found_at` saat pemilik mengonfirmasi. |
| 2.2 | P0.1 ditetapkan: Barang Ditemukan hanya berisi laporan kehilangan yang berhasil dikembalikan setelah konfirmasi disetujui Admin; laporan barang temuan baru tidak termasuk. |
| 2.3 | P0.2 ditetapkan: cakupan seluruh lingkungan kampus; mahasiswa, dosen, dan staf aktif dapat mendaftar; Guest membaca publik tanpa akun. Ketentuan verifikasi email dari keputusan P0.3 dicabut pada A11 (2026-11-22). |
| 2.4 | P0.4 ditetapkan: field laporan, kategori tetap, maksimal 3 foto JPG/JPEG/PNG/WebP, maksimal 2 MB per foto, storage lokal Laravel, dan publikasi foto setelah persetujuan Admin. Ketentuan jumlah foto dan kategori digantikan pada 2.13. |
| 2.5 | P0.5 ditetapkan: kontak privat, edit/batal Pending, Approved tidak diedit langsung, alasan penolakan wajib, pengiriman ulang, batas 3 kali konfirmasi, serta retensi data dan riwayat. |
| 2.6 | P0.8 selesai: seluruh keputusan P0 diterapkan, status/versi diselaraskan, label draft yang sudah diputuskan difinalkan, dan daftar pertanyaan terbuka non-P0 disamakan dengan PRD serta ARCHITECTURE. |
| 2.7 | 2026-11-22: diselaraskan dengan alur yang disetujui pemilik — pelaporan barang temuan, no. HP penemu dan kontak WhatsApp, Admin terpisah tersembunyi, nama pemilik dan foto bersama yang privat, edit Admin hanya pada `Pending`, penghapusan permanen `returned`/`archived`, FR/US/NFR baru, serta catatan selisih implementasi. |
| 2.8 | 2026-11-22 (A11): verifikasi email dihapus; eyebrow hanya di beranda; subtitle **Cari laporan** dihapus; notifikasi mengambang otomatis hilang setelah 3 detik; **Akun** dimulai dari kartu profil. |
| 2.9 | 2026-11-22 (A12): no. HP opsional dan privat pada pendaftaran/biodata **Akun**; label program studi memakai format kurung; teks bantuan pemilihan foto profil dihapus. |
| 2.10 | 2026-11-22 (A13): no. HP Akun menjadi wajib pada pendaftaran/biodata; teks `(opsional)` dihapus dari labelnya. |
| 2.11 | 2026-11-22 (A18): baseline dua alur — **Barang Hilang = laporan kehilangan pemilik**, **Barang Ditemukan = laporan temuan penemu**. Tabel terpisah, dua card status per menu (Belum/Sudah), 30 hari pada daftar “Sudah”, foto dan no. HP wajib tetapi privat, tanpa penautan antarlaporan, dropdown **Buat Laporan**, sidebar Admin 5 menu, dan card status yang tetap dalam jenis aktif. Keputusan lama dicabut. |
| 2.12 | 2026-11-23 (A28): konfirmasi **Barang ditemukan** pada laporan kehilangan langsung oleh pemilik (`approved → found`, `found_at`, riwayat `found_by_owner`, 30 hari sejak `found_at`) tanpa bukti/form/`found_pending` baru dan tanpa verifikasi Admin; pembatalan pemilik `pending`/`approved`; Admin tetap memverifikasi publikasi awal serta dapat edit/hapus laporan kehilangan di semua status. Aturan lama difinalkan hanya untuk laporan temuan; data `found_pending` lama tetap ada sebagai historis. |
| 2.13 | 2026-11-23: penjadwalan harian arsip 30 hari laporan kehilangan, URI publik kanonik `/barang/hilang` dan `/barang/ditemukan` dengan redirect 301 dari URI lama, penghapusan catatan usang bukti konfirmasi kehilangan, dan penyelarasan spesifikasi foto/kategori ke implementasi (satu foto, lima kategori). |
