<?php

namespace Database\Seeders;

use App\Models\MissingReport;
use App\Models\User;
use App\Support\MissingReportPhoto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MissingReportDemoSeeder extends Seeder
{
    /**
     * Seed laporan kehilangan fiktif yang idempoten tanpa mengubah data lama.
     */
    public function run(): void
    {
        $owner = User::query()
            ->where('email', 'demo.user@titiptemu.test')
            ->firstOrFail();

        foreach ($this->reports() as $index => $item) {
            $day = $index + 1;
            $timestamp = now()->subDays($day)->setTime(8, 30);
            $photoPath = MissingReportPhoto::DIRECTORY.'/demo-kehilangan-'.str_pad((string) $day, 2, '0', STR_PAD_LEFT).'.svg';

            Storage::disk(MissingReportPhoto::PRIVATE_DISK)->put($photoPath, $this->placeholderSvg($item['name']));

            $foundAt = isset($item['found_days'])
                ? now()->subDays($item['found_days'])->setTime(15, 0)
                : null;

            $report = MissingReport::query()->updateOrCreate(
                [
                    'user_id' => $owner->id,
                    'item_name' => $item['name'],
                ],
                [
                    'category' => $item['category'],
                    'description' => $item['description'],
                    'lost_at' => $timestamp->toDateString(),
                    'location' => $item['location'],
                    'contact_phone' => '0813'.str_pad((string) $day, 7, '0', STR_PAD_LEFT),
                    'photo_path' => $photoPath,
                    'status' => $item['status'],
                    'terms_accepted_at' => $timestamp,
                    'found_at' => $foundAt,
                    'archived_at' => null,
                ],
            );

            DB::table('missing_reports')
                ->where('id', $report->id)
                ->update(['created_at' => $timestamp, 'updated_at' => $foundAt ?? $timestamp]);
        }
    }

    /**
     * @return array<int, array{name: string, category: string, description: string, location: string, status: string, found_days?: int}>
     */
    private function reports(): array
    {
        return [
            [
                'name' => 'Kartu Identitas Demo Hilang',
                'category' => 'Dokumen',
                'description' => 'Kartu identitas fiktif yang hilang di area perpustakaan dan dilaporkan oleh pemiliknya.',
                'location' => 'Perpustakaan Pusat',
                'status' => MissingReport::STATUS_APPROVED,
            ],
            [
                'name' => 'Kunci Kamar Kost Hilang',
                'category' => 'Aksesori',
                'description' => 'Kunci kamar kost dengan gantungan merah hilang setelah pulang dari kampus.',
                'location' => 'Kos Mahasiswa',
                'status' => MissingReport::STATUS_APPROVED,
            ],
            [
                'name' => 'Smartphone Layar Retak Hilang',
                'category' => 'Elektronik',
                'description' => 'Smartphone warna biru dengan layar retak hilang di ruang kelas.',
                'location' => 'Gedung A Lantai 3',
                'status' => MissingReport::STATUS_APPROVED,
            ],
            [
                'name' => 'Jaket Fakultas Hilang',
                'category' => 'Pakaian',
                'description' => 'Jaket fakultas warna navy tertinggal di bangku area kafetaria.',
                'location' => 'Kafetaria Fakultas',
                'status' => MissingReport::STATUS_APPROVED,
            ],
            [
                'name' => 'Earphone Putri Hilang',
                'category' => 'Elektronik',
                'description' => 'Earphone nirkabel warna putri yang hilang di aula fakultas.',
                'location' => 'Aula Fakultas',
                'status' => MissingReport::STATUS_FOUND_PENDING,
            ],
            [
                'name' => 'Buku Catatan Biru Hilang',
                'category' => 'Lainnya',
                'description' => 'Buku catatan biru berisi catatan kuliah fiktif yang hilang di koridor.',
                'location' => 'Koridor Utama',
                'status' => MissingReport::STATUS_FOUND_PENDING,
            ],
            [
                'name' => 'Dompet Kulit Hitam Hilang',
                'category' => 'Aksesori',
                'description' => 'Dompet kulit hitam sudah ditemukan pemiliknya dan menunggu masa tampil 30 hari.',
                'location' => 'Parkir Timur',
                'status' => MissingReport::STATUS_FOUND,
                'found_days' => 3,
            ],
            [
                'name' => 'Tas Ransel Hijau Hilang',
                'category' => 'Aksesori',
                'description' => 'Tas ransel hijau sudah dikembalikan kepada pemiliknya dan tampil di daftar Sudah Ditemukan.',
                'location' => 'Lapangan Olahraga',
                'status' => MissingReport::STATUS_FOUND,
                'found_days' => 12,
            ],
        ];
    }

    private function placeholderSvg(string $label): string
    {
        $safeLabel = htmlspecialchars($label, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="640" height="420" viewBox="0 0 640 420" role="img" aria-label="Ilustrasi {$safeLabel}">
          <rect width="640" height="420" fill="#eef2fb"/>
          <circle cx="320" cy="170" r="76" fill="#d5e1f8"/>
          <path d="M286 170l24 24 48-52" fill="none" stroke="#1f4fd8" stroke-width="14" stroke-linecap="round" stroke-linejoin="round"/>
          <rect x="70" y="300" width="500" height="62" rx="18" fill="#ffffff" opacity="0.9"/>
          <text x="320" y="339" text-anchor="middle" font-family="sans-serif" font-size="24" fill="#1b2740">{$safeLabel}</text>
        </svg>
        SVG;
    }
}
