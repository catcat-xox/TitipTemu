# Panduan Kontribusi TitipTemu

Terima kasih sudah tertarik berkontribusi pada TitipTemu, portal barang hilang dan temuan kampus. Proyek ini berlisensi MIT dan terbuka untuk perbaikan bug, fitur baru, maupun perbaikan dokumentasi. Panduan ini menjelaskan cara mengajukan issue dan Pull Request (PR).

## Sebelum Mulai

1. Baca `README.md` untuk gambaran proyek dan cara menyiapkan lingkungan pengembangan (PHP, Composer, MySQL, Node.js).
2. Baca `docs/rules.md` untuk aturan pengembangan, khususnya bagian UI, privasi, dan status laporan.
3. Cari di tab **Issues** apakah masalah atau ide yang sama sudah pernah dibuat, agar tidak ganda.

Jangan pernah mengunggah data pribadi nyata, kata sandi, token, atau berkas `.env` ke issue, PR, maupun commit. Gunakan data fiktif dari seeder.

## Cara Mengajukan Issue

Buka tab **Issues**, klik **New issue**, lalu pilih template yang sesuai.

- **Laporan bug**: jelaskan langkah untuk memunculkan masalah, hasil yang diharapkan, hasil yang terjadi, dan lingkungan Anda (sistem operasi, versi PHP, browser). Sertakan tangkapan layar jika membantu.
- **Usulan fitur**: jelaskan masalah yang ingin diselesaikan, usulan solusinya, dan siapa yang diuntungkan.

Beri judul yang singkat dan jelas. Satu issue untuk satu masalah. Tunggu tanggapan pengelola sebelum mengerjakan fitur besar supaya tidak bentrok dengan arah produk.

## Alur Kerja Pull Request

Branch `main` dilindungi: **tidak boleh ada commit langsung ke `main`**. Semua perubahan masuk lewat PR.

1. **Fork** repositori ini (atau, bagi anggota tim, langsung buat branch di repositori utama), lalu clone ke komputer Anda.
2. Pastikan `main` Anda mutakhir, lalu buat branch baru dari `main`:

   ```bash
   git checkout main
   git pull origin main
   git checkout -b feature/nama-singkat
   ```

3. Kerjakan perubahan Anda secara terfokus. Satu PR sebaiknya hanya berisi satu tujuan.
4. Jalankan pengujian dan pemeriksaan format (lihat bagian di bawah) sebelum push.
5. Push branch Anda dan buka PR ke `main`:

   ```bash
   git push origin feature/nama-singkat
   ```

6. Isi template PR dengan lengkap, kaitkan dengan issue terkait (misalnya `Closes #12`), lalu minta review dari minimal satu anggota tim lain.
7. Tanggapi komentar review dengan commit tambahan pada branch yang sama. Setelah disetujui dan semua pemeriksaan lulus, PR di-merge.

### Penamaan Branch

| Awalan | Dipakai untuk |
| --- | --- |
| `feature/` | fitur baru |
| `fix/` | perbaikan bug |
| `docs/` | perubahan dokumentasi |
| `test/` | menambah atau memperbaiki tes |

Contoh: `feature/filter-kategori`, `fix/foto-profil-tidak-muncul`, `docs/contributing`.

### Pesan Commit

Gunakan pola `jenis: ringkasan singkat` dan lakukan commit bertahap, bukan satu commit besar di akhir.

- `feat: tambah filter kategori pada daftar barang`
- `fix: perbaiki foto profil yang tidak langsung tampil`
- `docs: tambah panduan kontribusi`
- `test: tambah tes akses admin`
- `refactor: pindahkan logika arsip ke service`

## Standar Kode dan Pengujian

Semua perintah Laravel dijalankan dari folder `backend/`.

```bash
cd backend
php artisan test
./vendor/bin/pint
```

Jika Anda mengubah CSS atau JavaScript, pastikan aset tetap berhasil dibangun:

```bash
cd frontend
npm run build
```

Ketentuan singkat:

- Ikuti konvensi Laravel dan aturan di `docs/rules.md`.
- Tambahkan atau perbarui tes untuk setiap perubahan perilaku, terutama akses peran, privasi, unggahan, dan perubahan status.
- Jangan menonaktifkan atau melemahkan tes hanya agar hasilnya hijau.
- Jangan menambah dependensi baru jika masalah bisa diselesaikan dengan stack yang sudah ada.
- Gunakan teks antarmuka berbahasa Indonesia yang konsisten.
- Perbarui `README.md` atau dokumen di `docs/` jika perubahan Anda memengaruhi instalasi, perintah, atau alur penggunaan.

Catatan: pengujian foto membutuhkan ekstensi PHP `gd`. Jika banyak tes gagal dengan pesan GD, aktifkan `extension=gd` di `php.ini`.

## Code Review

Reviewer memeriksa hal berikut sebelum menyetujui PR:

- Perubahan sesuai dengan issue dan tidak membawa perubahan lain yang tidak terkait.
- Tes lulus dan aplikasi tetap berjalan.
- Tidak ada kredensial, data pribadi, atau berkas lokal yang ikut ter-commit.
- Kode dan teks mudah dipahami dan konsisten dengan bagian lain.

Beri komentar yang sopan dan spesifik. Penulis PR tidak boleh me-merge PR-nya sendiri tanpa review dari anggota lain.

## Lisensi

Dengan mengirim kontribusi, Anda setuju kontribusi tersebut dilisensikan di bawah MIT License yang sama dengan proyek ini (lihat berkas `LICENSE`).
