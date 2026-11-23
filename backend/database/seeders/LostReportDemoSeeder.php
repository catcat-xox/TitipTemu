<?php

namespace Database\Seeders;

use App\Models\LostReport;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LostReportDemoSeeder extends Seeder
{
    /**
     * Seed sepuluh laporan barang hilang fiktif yang idempoten.
     */
    public function run(): void
    {
        $owner = User::query()
            ->where('email', 'demo.user@titiptemu.test')
            ->firstOrFail();

        foreach ($this->reports() as $index => $item) {
            $day = $index + 1;
            $timestamp = now()->subDays($day)->setTime(9, 0);
            $photoPath = 'lost-reports/demo-'.str_pad((string) $day, 2, '0', STR_PAD_LEFT).'.svg';

            Storage::disk('local')->put($photoPath, $this->placeholderSvg($item['name']));

            $report = LostReport::query()->updateOrCreate(
                [
                    'user_id' => $owner->id,
                    'item_name' => $item['name'],
                ],
                [
                    'category' => $item['category'],
                    'description' => $item['description'],
                    'lost_at' => $timestamp->toDateString(),
                    'location' => $item['location'],
                    'contact_phone' => '0812'.str_pad((string) $day, 7, '0', STR_PAD_LEFT),
                    'photo_path' => $photoPath,
                    'status' => 'approved',
                    'terms_accepted_at' => $timestamp,
                ],
            );

            DB::table('lost_reports')
                ->where('id', $report->id)
                ->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function reports(): array
    {
        return [
            [
                'name' => 'Dompet Kulit Coklat',
                'category' => 'Aksesori',
                'description' => 'Dompet kulit warna coklat berisi kartu identitas fiktif untuk keperluan demo. Ditemukan dekat area kafetaria.',
                'location' => 'Kafetaria Fakultas',
            ],
            [
                'name' => 'Kartu Mahasiswa Demo',
                'category' => 'Dokumen',
                'description' => 'Kartu mahasiswa contoh dengan nama fiktif. Ditemukan di meja baca perpustakaan.',
                'location' => 'Perpustakaan Pusat',
            ],
            [
                'name' => 'Laptop Hitam 14 Inci',
                'category' => 'Elektronik',
                'description' => 'Laptop berwarna hitam dengan stiker kampus fiktif. Ditemukan di ruang kelas setelah perkuliahan.',
                'location' => 'Gedung B Lantai 2',
            ],
            [
                'name' => 'Kunci Motor Merek Demo',
                'category' => 'Aksesori',
                'description' => 'Gantungan kunci motor dengan gantungan biru. Ditemukan di area parkir timur.',
                'location' => 'Parkir Timur',
            ],
            [
                'name' => 'Jaket Biru Kampus',
                'category' => 'Pakaian',
                'description' => 'Jaket biru dengan tulisan kampus fiktif. Ditemukan di bangku dekat lapangan olahraga.',
                'location' => 'Lapangan Olahraga',
            ],
            [
                'name' => 'Buku Catatan Hijau',
                'category' => 'Lainnya',
                'description' => 'Buku catatan berwarna hijau dengan cover polos. Ditemukan di kursi aula fakultas.',
                'location' => 'Aula Fakultas',
            ],
            [
                'name' => 'Earphone Tanpa Kabel',
                'category' => 'Elektronik',
                'description' => 'Earphone nirkabel warna putih di dalam kotak kecil. Ditemukan di ruang kelas 3A.',
                'location' => 'Ruang Kelas 3A',
            ],
            [
                'name' => 'Tas Ransel Abu-Abu',
                'category' => 'Aksesori',
                'description' => 'Tas ransel abu-abu dengan resleting oranye. Ditemukan di koridor utama gedung fakultas.',
                'location' => 'Koridor Utama',
            ],
            [
                'name' => 'Transkrip Nilai Contoh',
                'category' => 'Dokumen',
                'description' => 'Berkas transkrip nilai contoh dengan data fiktif. Ditemukan dekat meja layanan kantor fakultas.',
                'location' => 'Kantor Fakultas',
            ],
            [
                'name' => 'Payung Lipat Hitam',
                'category' => 'Lainnya',
                'description' => 'Payung lipat warna hitam dengan gagang kayu. Ditemukan di pintu masuk utama kampus.',
                'location' => 'Pintu Masuk Utama',
            ],
        ];
    }

    private function placeholderSvg(string $label): string
    {
        $safeLabel = htmlspecialchars($label, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="640" height="420" viewBox="0 0 640 420" role="img" aria-label="Ilustrasi {$safeLabel}">
          <rect width="640" height="420" fill="#e8eef8"/>
          <circle cx="320" cy="170" r="76" fill="#c7d7f5"/>
          <path d="M286 170l24 24 48-52" fill="none" stroke="#1f4fd8" stroke-width="14" stroke-linecap="round" stroke-linejoin="round"/>
          <rect x="70" y="300" width="500" height="62" rx="18" fill="#ffffff" opacity="0.9"/>
          <text x="320" y="339" text-anchor="middle" font-family="sans-serif" font-size="26" fill="#1b2740">{$safeLabel}</text>
        </svg>
        SVG;
    }
}
