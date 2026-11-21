# SYSTEM REQUIREMENT — TitipTemu (Baseline v2.6)

**Status:** Disetujui untuk MVP; pertanyaan non-P0 yang tersisa dicatat terbuka

**Versi:** 2.6

**Platform:** Website

**Teknologi acuan:** Laravel + MySQL; Blade + Bootstrap untuk frontend

**Lingkup:** Seluruh lingkungan kampus

> Baseline ini menggantikan alur baseline lama yang menggunakan laporan barang ditemukan terpisah, klaim kepemilikan, dan penjadwalan pertemuan. Fokusnya adalah laporan kehilangan dan konfirmasi pengembalian. Pertanyaan non-P0 yang masih terbuka diberi label terbuka/rekomendasi dan belum menjadi aturan final.

## 1. Ringkasan keputusan dan asumsi
| ID | Ketentuan | Status |
|---|---|---|
| ASM-01 | TitipTemu berupa website | Ditetapkan dari konsep |
| ASM-02 | Backend Laravel dan database MySQL; frontend Blade + Bootstrap | Ditetapkan |
| ASM-03 | Guest dapat mengakses informasi publik tanpa login | Ditetapkan dari konsep |
| ASM-04 | User wajib login untuk membuat laporan dan konfirmasi pengembalian | Ditetapkan dari konsep |
| ASM-05 | Admin memoderasi laporan dan konfirmasi pengembalian | Ditetapkan dari konsep |
| ASM-06 | MVP berfokus pada laporan barang hilang; laporan penemuan terpisah tidak termasuk lingkup | Ditetapkan |
| ASM-07 | Dua menu publik: Barang Hilang dan Barang Ditemukan; Barang Ditemukan hanya berisi laporan kehilangan yang berhasil dikembalikan setelah konfirmasi disetujui Admin | Ditetapkan |
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
- Membaca panduan dan ketentuan.
- Tidak dapat membuat laporan, mengirim konfirmasi pengembalian, atau membuka area User/Admin.
### 2.2 User
- User adalah mahasiswa, dosen, atau staf aktif kampus yang memiliki akun.
- Mendaftar dan login.
- Membuat laporan kehilangan.
- Mengunggah foto laporan.
- Menyetujui ketentuan laporan.
- Melihat daftar dan detail laporan miliknya serta statusnya.
- Mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung.
- Memperbaiki dan mengirim ulang laporan yang ditolak setelah membaca alasan Admin.
- Mengajukan konfirmasi barang kembali pada laporan miliknya.
- Mengunggah foto bukti pengembalian dan menyetujui pernyataan pengembalian.
- Melihat status pemeriksaan konfirmasi.
- Mengubah profil sesuai fitur yang tersedia.
### 2.3 Admin
- Sistem mendukung beberapa akun Admin; tidak ada asumsi satu Admin.
- Login Admin.
- Melihat laporan masuk.
- Memeriksa dan menyetujui/menolak laporan dengan alasan wajib saat menolak.
- Mengelola laporan dan publikasi.
- Melihat antrean konfirmasi pengembalian.
- Memeriksa bukti pengembalian.
- Menyetujui/menolak konfirmasi pengembalian dengan alasan wajib saat menolak.
- Menambahkan alasan penolakan.
- Melihat log aktivitas dan histori status.
- Mengelola kategori jika fitur kategori diterapkan.

### 2.4 Data pendaftaran dan verifikasi akun
- Nama lengkap wajib.
- NIM/NIP wajib dan bersifat privat.
- Email wajib, unik, dan harus diverifikasi.
- Password wajib dan disimpan dengan hashing aman.
- Fakultas wajib dan bersifat privat.
- Program studi wajib untuk mahasiswa dan tidak berlaku untuk dosen/staf.
- User tidak dapat membuat laporan sebelum email terverifikasi.
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
- Dashboard/beranda
- Buat laporan kehilangan
- Laporan Saya
- Detail laporan
- Konfirmasi barang telah kembali
- Profil
- Logout
### 3.3 Admin
- Dashboard Admin
- Laporan menunggu verifikasi
- Laporan aktif
- Detail laporan/verifikasi
- Konfirmasi pengembalian menunggu verifikasi
- Barang Ditemukan/riwayat pengembalian
- Riwayat aktivitas
- Pengaturan kategori (opsional)
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
| FR-07 | Auth | Admin dapat login ke area Admin | Must |
| FR-08 | Authorization | Sistem membatasi akses berdasarkan Guest/User/Admin | Must |
| FR-09 | Report | User login dapat membuat laporan kehilangan | Must |
| FR-10 | Report | Form laporan meminta data wajib dan foto | Must |
| FR-11 | Report | Sistem memvalidasi input dan file foto | Must |
| FR-12 | Report | Laporan baru disimpan sebagai Pending dan tidak dipublikasikan | Must |
| FR-13 | Admin | Admin dapat melihat antrean laporan Pending | Must |
| FR-14 | Admin | Admin dapat menyetujui atau menolak laporan | Must |
| FR-15 | Publication | Laporan Approved muncul di Barang Hilang | Must |
| FR-16 | User dashboard | User dapat melihat laporan yang dibuat sendiri dan statusnya | Must |
| FR-17 | User dashboard | User tidak dapat melihat atau mengubah laporan milik User lain | Must |
| FR-18 | Return confirmation | Pemilik laporan dapat mengirim konfirmasi barang telah kembali pada laporan miliknya | Must |
| FR-19 | Return confirmation | Form konfirmasi membutuhkan foto bukti dan persetujuan pernyataan | Must |
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
| FR-32 | Rules | User diwajibkan menyetujui ketentuan sebelum mengirim laporan | Must |
| FR-33 | Rules | User diwajibkan menyetujui pernyataan pengembalian sebelum mengirim konfirmasi | Must |
| FR-34 | Auth | User wajib memiliki email terverifikasi sebelum membuat laporan atau mengirim konfirmasi pengembalian | Must |

## 5. Alur proses sistem
### 5.1 Membuat laporan kehilangan
1. User login.
2. User membuka form Lapor Barang Hilang.
3. User mengisi informasi yang wajib.
4. User mengunggah foto.
5. User mencentang persetujuan ketentuan.
6. Sistem memvalidasi.
7. Sistem menyimpan laporan berstatus `Pending`.
8. Admin meninjau laporan.
9. Admin menyetujui atau menolak dengan alasan wajib saat menolak.
10. Jika disetujui, laporan berstatus aktif dan tampil pada Barang Hilang. Jika ditolak, User dapat memperbaiki dan mengirim ulang laporan.
### 5.2 Konfirmasi pengembalian barang
1. User membuka laporan miliknya yang berstatus aktif.
2. User memilih “Konfirmasi Barang Telah Kembali”.
3. User mengisi data konfirmasi dan mengunggah bukti foto.
4. User menyetujui pernyataan bahwa barang telah diterima.
5. Sistem memvalidasi dan menyimpan konfirmasi berstatus `Return Pending`.
6. Admin meninjau bukti.
7. Admin menyetujui atau menolak.
8. Jika disetujui: status laporan `Returned`, daftar publik berpindah dari Barang Hilang ke Barang Ditemukan, dan timestamp persetujuan Admin dicatat sebagai awal periode 30 hari.
9. Jika ditolak: laporan tetap aktif di Barang Hilang; alasan Admin wajib dicatat; User dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian dilakukan melalui Admin.
10. Setelah 30 hari dari tanggal acuan, sistem mengubah visibilitas menjadi `Archived` dan menghilangkan item dari daftar Barang Ditemukan.
### 5.3 Admin memoderasi laporan
1. Admin login.
2. Admin melihat antrean Pending.
3. Admin membuka detail, foto, dan data laporan.
4. Admin menyetujui atau menolak dengan alasan wajib saat menolak.
5. Sistem menyimpan keputusan, waktu, Admin yang mengambil tindakan, dan alasan penolakan.
### 5.4 Admin memoderasi pengembalian
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
| Tanggal kehilangan | Wajib; tidak boleh tanggal masa depan | Rekomendasi |
| Lokasi kehilangan | Wajib; 3–150 karakter | Rekomendasi |
| Foto barang | Wajib; minimal 1 dan maksimal 3 foto | Ditetapkan |
| Format foto | JPG/JPEG/PNG/WebP | Ditetapkan |
| Ukuran foto | Maksimal 2 MB per foto | Ditetapkan |
| Penyimpanan foto | Storage lokal Laravel; tidak publik sebelum laporan disetujui Admin | Ditetapkan |
| Ketentuan | Checkbox persetujuan wajib | Ditetapkan dari konsep |
| Kontak | Tidak ditampilkan kepada publik | Ditetapkan |

Kategori tetap: Elektronik; Dompet & Uang; Dokumen & Kartu Identitas; Kunci; Tas; Pakaian & Aksesoris; Buku & Alat Tulis; Lainnya.
## 7. Validasi form konfirmasi pengembalian
| Field | Validasi awal | Status |
|---|---|---|
| Laporan terkait | Wajib; harus milik User yang login dan aktif | Must |
| Tanggal pengembalian | Wajib atau dicatat otomatis | Perlu keputusan |
| Catatan | Panjang/opsional sesuai ketentuan | Perlu keputusan |
| Foto bukti | Wajib; batas format dan ukuran ditentukan | Ditetapkan wajib foto; batas belum ditetapkan |
| Pernyataan pengembalian | Checkbox wajib | Ditetapkan dari konsep |
| Catatan Admin | Wajib diisi ketika menolak | Ditetapkan |
## 8. Aturan bisnis
| ID | Aturan |
|---|---|
| BR-01 | Sistem berlaku untuk seluruh lingkungan kampus. |
| BR-02 | Guest dapat membaca konten publik tanpa login. |
| BR-03 | User wajib login untuk membuat laporan. |
| BR-04 | User wajib login untuk mengirim konfirmasi pengembalian. |
| BR-05 | Laporan kehilangan harus menyertakan foto. |
| BR-06 | Laporan baru berstatus Pending dan tidak dipublikasikan sebelum Admin menyetujui. |
| BR-07 | Laporan yang tampil di Barang Hilang adalah laporan berstatus `Approved`/`Lost Active` dan `Return Pending`; laporan `Pending`, `Rejected`, `Cancelled`, dan `Archived` tidak tampil. |
| BR-08 | Konfirmasi pengembalian hanya dapat diajukan oleh pemilik laporan. |
| BR-09 | Konfirmasi pengembalian wajib menyertakan foto bukti dan persetujuan pernyataan. |
| BR-10 | Konfirmasi pengembalian harus disetujui Admin sebelum perpindahan daftar. |
| BR-11 | Setelah disetujui, laporan berpindah dari Barang Hilang ke Barang Ditemukan. |
| BR-12 | Barang Ditemukan tampil 30 hari sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya pada daftar tersebut, menggunakan zona waktu Asia/Makassar (WITA), kemudian tidak tampil publik dan berstatus `Archived`. |
| BR-13 | Pengarsipan 30 hari tidak menghapus data secara permanen; data tetap disimpan dan foto bukti pengembalian tetap privat. |
| BR-14 | Bukti foto konfirmasi, data pribadi, dan kontak pribadi pengguna bersifat terbatas dan bukan konten publik. |
| BR-15 | Admin wajib menyertakan alasan saat menolak laporan atau konfirmasi pengembalian. |
| BR-16 | Pemilik dapat mengedit atau membatalkan laporan berstatus `Pending`; laporan `Approved` tidak dapat diedit langsung. |
| BR-17 | Pemilik dapat memperbaiki dan mengirim ulang laporan yang ditolak setelah membaca alasan Admin. |
| BR-18 | Konfirmasi pengembalian yang ditolak wajib memiliki alasan; pemilik dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian dilakukan melalui Admin. |
| BR-19 | Data laporan, data konfirmasi, dan riwayat status tidak dihapus permanen. |
## 9. Status laporan dan transisi
| Status | Deskripsi | Visibilitas publik |
|---|---|---|
| `Pending` | Menunggu moderasi laporan | Tidak |
| `Approved` / `Lost Active` | Laporan disetujui dan masih aktif | Barang Hilang |
| `Rejected` | Laporan ditolak | Tidak |
| `Return Pending` | User mengirim konfirmasi pengembalian, menunggu Admin | Tetap Barang Hilang sampai keputusan Admin |
| `Returned` | Admin menyetujui konfirmasi pengembalian | Barang Ditemukan |
| `Archived` | Lewat masa 30 hari | Tidak |
| `Cancelled` | Laporan Pending dibatalkan oleh pemilik | Tidak |

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
- Data laporan tetap disimpan di database, tidak dihapus permanen.
- Foto bukti pengembalian tetap privat dan tidak ditampilkan kepada publik.
## 11. Informasi publik vs privat
### Publik
- Nama barang.
- Kategori.
- Deskripsi yang telah dimoderasi.
- Foto publik laporan.
- Tanggal kehilangan.
- Lokasi kehilangan; tingkat detail masih terbuka.
- Status laporan.
### Privat
- NIM atau identitas mahasiswa.
- Email, nomor telepon pribadi, dan kontak pribadi lainnya.
- Detail akun.
- Foto bukti pengembalian jika digunakan untuk verifikasi.
- Ciri unik yang sengaja tidak dipublikasikan.
## 12. Non-Functional Requirements
| ID | Aspek | Requirement |
|---|---|---|
| NFR-01 | Responsif | Website usable di desktop dan mobile |
| NFR-02 | Security | Password menggunakan hashing, bukan plaintext |
| NFR-03 | Security | Validasi input di server-side |
| NFR-04 | Authorization | Guest, User, Admin hanya dapat melakukan tindakan sesuai role |
| NFR-05 | Ownership | User hanya dapat mengakses/mengubah laporan miliknya, kecuali data publik |
| NFR-06 | File | Validasi tipe, ukuran, dan file upload; nama file tidak dipercaya dari client |
| NFR-07 | Privacy | Foto bukti pengembalian dan data pribadi tidak terbuka untuk umum |
| NFR-08 | Audit | Keputusan Admin dan perubahan status penting tercatat |
| NFR-09 | Reliability | Backup database dan file yang diperlukan memiliki prosedur |
| NFR-10 | Usability | Label, formulir, status, dan pesan kesalahan mudah dipahami |
| NFR-11 | Maintainability | Struktur kode Laravel konsisten dan terdokumentasi |
| NFR-12 | Performance | Target uji awal: halaman publik ≤3 detik; pencarian ≤2 detik pada lingkungan uji yang disepakati |
## 13. User Stories dan Acceptance Criteria
### US-01 — Guest melihat informasi
**Sebagai Guest**, saya ingin melihat Barang Hilang dan Barang Ditemukan tanpa login agar dapat mencari informasi.  
**Acceptance:** halaman publik dapat dibuka; hanya item dengan status/visibilitas publik yang ditampilkan; data privat tidak tampil.
### US-02 — User membuat laporan kehilangan
**Sebagai User**, saya ingin membuat laporan kehilangan dengan foto dan ketentuan agar laporan dapat diperiksa Admin.  
**Acceptance:** login diperlukan; field wajib tervalidasi; foto wajib; persetujuan ketentuan wajib; laporan tersimpan Pending dan belum tampil publik.
### US-03 — Admin menyetujui laporan
**Sebagai Admin**, saya ingin memeriksa laporan agar hanya laporan yang sesuai yang dipublikasikan.  
**Acceptance:** Admin dapat melihat detail; menyetujui atau menolak; keputusan tercatat; Approved tampil di Barang Hilang.
### US-04 — User memantau laporan
**Sebagai User**, saya ingin melihat laporan dan status milik saya.  
**Acceptance:** User hanya melihat laporan miliknya; status dan keputusan terlihat; laporan user lain tidak dapat diakses lewat URL langsung.
### US-05 — User mengonfirmasi barang kembali
**Sebagai pemilik laporan**, saya ingin mengonfirmasi barang telah kembali dengan foto bukti.  
**Acceptance:** hanya pemilik laporan aktif; foto bukti wajib; pernyataan wajib dicentang; konfirmasi masuk antrean Admin; laporan belum pindah sebelum disetujui Admin.
### US-06 — Admin menyetujui pengembalian
**Sebagai Admin**, saya ingin memeriksa bukti pengembalian agar perpindahan status benar.  
**Acceptance:** Admin dapat menyetujui/menolak; keputusan tercatat; jika disetujui laporan pindah ke Barang Ditemukan; jika ditolak item tetap aktif, alasan wajib tercatat, dan User dapat memperbaiki serta mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
### US-07 — Item Barang Ditemukan diarsipkan otomatis
**Sebagai pengelola**, saya ingin item tidak lagi muncul setelah 30 hari agar daftar Barang Ditemukan tetap relevan.  
**Acceptance:** sistem menyimpan tanggal acuan; proses terjadwal mengarsipkan item yang melewati 30 hari; item Archived tidak tampil publik; data dan histori tidak dihapus permanen.
### US-08 — Admin mengelola moderasi
**Sebagai Admin**, saya ingin melihat antrean laporan dan konfirmasi pengembalian agar tugas verifikasi terorganisasi.  
**Acceptance:** antrean dapat difilter menurut status; detail dapat diperiksa; keputusan tersimpan beserta waktu dan Admin.
## 14. Error dan kondisi gagal
- Field wajib tidak lengkap: sistem menolak submit dan memberi penjelasan.
- Foto tidak ada, format salah, ukuran terlalu besar, atau upload gagal: sistem menolak/menampilkan error yang jelas dan tidak membuat laporan publik.
- User belum login: diarahkan ke autentikasi sebelum membuat laporan atau konfirmasi.
- User mencoba mengakses laporan orang lain: akses ditolak.
- Admin menolak laporan: status Rejected dan alasan wajib tercatat; User dapat memperbaiki dan mengirim ulang.
- Admin menolak konfirmasi: laporan tetap Barang Hilang, alasan wajib tercatat; User dapat memperbaiki dan mengirim ulang maksimal 3 kali, lalu penyelesaian melalui Admin.
- Proses arsip otomatis gagal: item tidak dihapus permanen; kegagalan perlu log dan dapat diproses ulang.
## 15. Kriteria MVP selesai
- Guest dapat melihat Barang Hilang dan Barang Ditemukan tanpa login.
- Registrasi, login User, dan login Admin berfungsi.
- User dapat membuat laporan kehilangan dengan data dan foto serta menyetujui ketentuan.
- Admin dapat menyetujui/menolak laporan.
- Laporan Approved muncul pada Barang Hilang.
- User dapat melihat riwayat laporan miliknya.
- User dapat mengedit atau membatalkan laporan `Pending`; laporan `Approved` tidak dapat diedit langsung.
- Penolakan laporan dan konfirmasi pengembalian selalu disertai alasan Admin.
- User dapat memperbaiki dan mengirim ulang laporan yang ditolak, serta konfirmasi pengembalian yang ditolak maksimal 3 kali sebelum penyelesaian melalui Admin.
- User dapat mengirim konfirmasi pengembalian pada laporan miliknya dengan foto bukti dan persetujuan pernyataan.
- Admin dapat menyetujui/menolak konfirmasi.
- Konfirmasi yang disetujui memindahkan item ke Barang Ditemukan.
- Item di Barang Ditemukan tidak tampil lagi setelah 30 hari.
- Akses privat, role, dan validasi file diterapkan.
- Dokumentasi instalasi, konfigurasi, dan penggunaan tersedia di repository.
## 16. Pertanyaan dan status keputusan
Pertanyaan P0 yang sudah dijawab tetap dicatat di bawah ini. Pertanyaan non-P0 yang belum dijawab ditandai terbuka.
1. Barang Ditemukan: hanya barang dari laporan kehilangan yang sudah dikembalikan, atau juga laporan barang temuan baru? **Sudah diputuskan: hanya laporan barang hilang yang berhasil dikembalikan setelah konfirmasi disetujui Admin; tidak ada laporan barang temuan baru.**
2. Lingkup akun: mahasiswa saja atau seluruh civitas akademika? **Sudah diputuskan: mahasiswa, dosen, dan staf aktif kampus.**
3. Registrasi: data apa yang diminta dan apakah email kampus diverifikasi? **Sudah diputuskan: pendaftaran meminta nama lengkap, NIM/NIP, email, password, fakultas, dan program studi khusus mahasiswa; email wajib diverifikasi sebelum pelaporan; identitas tidak diverifikasi melalui database resmi kampus.**
4. Data form laporan kehilangan: setujukah dengan nama barang, kategori, deskripsi, tanggal, lokasi, foto? **Sudah diputuskan: nama barang, kategori tetap, deskripsi, tanggal hilang, lokasi hilang, foto, dan ketentuan.**
5. Batas foto: jumlah maksimum, ukuran maksimum, dan format? **Sudah diputuskan: maksimal 3 foto; JPG/JPEG/PNG/WebP; maksimal 2 MB per foto.**
6. Ketentuan pelaporan: apa saja yang wajib disetujui User?
7. Kontak pelapor: ditampilkan, disembunyikan, atau komunikasi melalui Admin? **Sudah diputuskan: kontak pribadi pengguna tidak ditampilkan secara publik.**
8. Bukti pengembalian: foto seperti apa yang wajib diunggah, apakah hanya dilihat Admin, dan berapa batas format/ukurannya? **Terbuka; yang sudah ditetapkan: foto bukti wajib dan bersifat privat.**
9. Apakah laporan boleh diedit/dibatalkan oleh User? Pada status apa? **Sudah diputuskan: pemilik dapat mengedit/membatalkan laporan `Pending`; laporan `Approved` tidak dapat diedit langsung.**
10. Jika konfirmasi pengembalian ditolak, apakah laporan tetap Barang Hilang dan User boleh mengajukan ulang? **Sudah diputuskan: laporan tetap Barang Hilang, alasan wajib dicatat, dan pengiriman ulang maksimal 3 kali; setelah itu penyelesaian melalui Admin.**
11. 30 hari dihitung sejak User mengisi tanggal kembali atau sejak Admin menyetujui? **Sudah diputuskan: sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya, zona waktu Asia/Makassar.**
12. Setelah 30 hari apakah diarsipkan (data tetap ada) atau dihapus permanen? **Sudah diputuskan: diarsipkan dan disembunyikan; data tetap disimpan, foto bukti tetap privat.**
13. Apakah Admin dapat menghapus laporan atau hanya menolak/arsip? **Sudah diputuskan: Admin hanya menolak atau mengarsipkan; data dan riwayat tidak dihapus permanen.**
14. Unit penerapan yang pasti: fakultas, prodi, atau seluruh kampus? **Sudah diputuskan: seluruh lingkungan kampus.**
15. Apakah diperlukan fitur tambahan seperti lokasi titik/gedung, kontak melalui Admin, atau notifikasi status? **Sudah diputuskan: tidak ada kontak pribadi publik; lokasi titik/gedung dan notifikasi status masih terbuka.**
16. Apakah Admin dapat mengelola kategori, atau kategori tetap saja? **Terbuka; yang sudah ditetapkan: delapan kategori tetap untuk laporan.**
17. Tanggal pengembalian wajib diisi atau dicatat otomatis, dan catatan pengembalian wajib atau opsional? **Terbuka.**
## 17. Revisi
| Versi | Perubahan |
|---|---|
| 1.1 | Baseline lama berisi laporan barang hilang dan ditemukan terpisah, klaim digital, serta proses pertemuan langsung. |
| 2.0 | Disederhanakan sesuai konsep TitipTemu: laporan kehilangan, moderasi Admin, konfirmasi pengembalian oleh pemilik laporan dengan foto, perpindahan ke Barang Ditemukan, dan pengarsipan publik 30 hari. |
| 2.1 | P0.6 ditetapkan: 30 hari sejak Admin menyetujui konfirmasi pengembalian dan mempublikasikannya, zona waktu Asia/Makassar, lalu `Archived` tanpa penghapusan permanen; foto bukti tetap privat. |
| 2.2 | P0.1 ditetapkan: Barang Ditemukan hanya berisi laporan kehilangan yang berhasil dikembalikan setelah konfirmasi disetujui Admin; laporan barang temuan baru tidak termasuk. |
| 2.3 | P0.2 ditetapkan: cakupan seluruh lingkungan kampus; mahasiswa, dosen, dan staf aktif dapat mendaftar; Guest membaca publik tanpa akun. |
| 2.4 | P0.4 ditetapkan: field laporan, kategori tetap, maksimal 3 foto JPG/JPEG/PNG/WebP, maksimal 2 MB per foto, storage lokal Laravel, dan publikasi foto setelah persetujuan Admin. |
| 2.5 | P0.5 ditetapkan: kontak privat, edit/batal Pending, Approved tidak diedit langsung, alasan penolakan wajib, pengiriman ulang, batas 3 kali konfirmasi, serta retensi data dan riwayat. |
| 2.6 | P0.8 selesai: seluruh keputusan P0 diterapkan, status/versi diselaraskan, label draft yang sudah diputuskan difinalkan, dan daftar pertanyaan terbuka non-P0 disamakan dengan PRD serta ARCHITECTURE. |
