# Third-Party Notices — TitipTemu

Dokumen ini merangkum lisensi kode dan aset pihak ketiga yang dipakai TitipTemu. Lisensi proyek sendiri berada pada `LICENSE` (MIT, Copyright 2026 TEAM-SIK). Teks lisensi lengkap untuk pustaka dapat ditemukan pada paket masing-masing di `backend/vendor` dan `frontend/node_modules`; teks lisensi font disertakan pada `frontend/licenses/InstrumentSans-OFL.txt`.

## 1. Kode sisi server (PHP)

| Paket | Lisensi | Catatan |
|---|---|---|
| `laravel/framework` | MIT | Framework utama. |
| `laravel/tinker` | MIT | Development console; tidak dipakai pada runtime web. |
| `fakerphp/faker` | MIT | Development/testing. |
| `laravel/pail`, `laravel/pao`, `laravel/pint`, `nunomaduro/collision` | MIT | Development/testing. |
| `mockery/mockery` | BSD-3-Clause | Development/testing. |
| `phpunit/phpunit` | BSD-3-Clause | Development/testing. |

Tidak ada dependency PHP copyleft yang diketahui. Versi pasti dan transisi lisensi tercatat pada `backend/composer.lock`.

## 2. Aset dan kode sisi klien

| Paket / aset | Lisensi | Catatan |
|---|---|---|
| `bootstrap` | MIT | CSS dan JavaScript Bootstrap 5.3 diimpor oleh `frontend/css/app.css` dan `frontend/js/app.js`. |
| `vite`, `laravel-vite-plugin` | MIT | Build tooling. |
| `concurrently` | MIT | Development tooling. |
| `@laravel/multiplex` | MIT | Dependency opsional dari Laravel Vite plugin. |
| Instrument Sans | SIL Open Font License 1.1 | Font diunduh saat build oleh `laravel-vite-plugin/fonts` dari Bunny Fonts dan dibundel ke `backend/public/build/assets`. Teks lisensi disertakan pada `frontend/licenses/InstrumentSans-OFL.txt`. |

Ikon pada antarmuka berupa inline SVG yang dibuat dalam repositori; tidak ada paket ikon pihak ketiga yang dibundel. Bootstrap Icons tidak dipakai.

Versi pasti dan paket transisi tercatat pada `frontend/package-lock.json`. Karena hasil build tidak disimpan di Git, pastikan `THIRD_PARTY_NOTICES.md` dan `frontend/licenses/InstrumentSans-OFL.txt` ikut disertakan saat mendistribusikan artefak produksi.

## 3. Kewajiban atribusi

- MIT dan BSD-3-Clause meminta pencantuman notice lisensi pada salinan atau bagian substansial dari perangkat lunak. File lisensi pada paket vendor/node_modules harus dipertahankan bila sumber atau artefak didistribusikan.
- SIL OFL 1.1 mengharuskan setiap salinan font menyertakan copyright notice dan lisensinya. Berkas `frontend/licenses/InstrumentSans-OFL.txt` memenuhi kewajiban ini untuk font yang dibundel.
- Nama “Instrument Sans” tidak diubah dan tidak dipakai untuk mempromosikan turunan font.

## 4. Layanan eksternal

TitipTemu tidak memakai WhatsApp API berbayar, kredensial eksternal, SSO, atau layanan analitik pihak ketiga. Tombol WhatsApp hanya mengarahkan ke `wa.me` dengan nomor yang diproses di server. Font diambil pada proses build melalui Bunny Fonts; runtime aplikasi tidak bergantung pada CDN eksternal setelah aset dibangun.
