<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\MissingReport;
use App\Models\MissingReportStatusHistory;
use App\Models\User;
use App\Support\MissingReportPhoto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WandiMissingStatusDemoSeeder extends Seeder
{
    /**
     * Enam laporan kehilangan fiktif per status aktif, semuanya dibuat akun wandi saputra.
     */
    private const STATUS_LABELS = [
        MissingReport::STATUS_PENDING => 'Menunggu',
        MissingReport::STATUS_APPROVED => 'Masih Dicari',
        MissingReport::STATUS_REJECTED => 'Ditolak',
        MissingReport::STATUS_FOUND => 'Sudah Ditemukan',
        MissingReport::STATUS_ARCHIVED => 'Diarsipkan',
        MissingReport::STATUS_CANCELLED => 'Dibatalkan',
    ];

    /**
     * @var array<string, string>
     */
    private const ITEMS = [
        'Dompet' => 'Aksesori',
        'Kartu' => 'Dokumen',
        'Laptop' => 'Elektronik',
        'Kunci' => 'Aksesori',
        'Jaket' => 'Pakaian',
        'Buku' => 'Lainnya',
    ];

    /**
     * @var array<int, string>
     */
    private const LOCATIONS = [
        'Kafetaria Fakultas',
        'Perpustakaan Pusat',
        'Gedung B Lantai 2',
        'Parkir Timur',
        'Lapangan Olahraga',
        'Aula Fakultas',
    ];

    public function run(): void
    {
        $owner = User::query()
            ->where('email', 'like', 'wandisaputra%')
            ->orWhereRaw('LOWER(name) = ?', ['wandi saputra'])
            ->first();

        if (! $owner) {
            $this->command?->warn('Akun wandi saputra tidak ditemukan; data uji laporan kehilangan dilewati.');

            return;
        }

        $admin = Admin::query()->orderBy('id')->first();
        $index = 0;

        foreach (self::STATUS_LABELS as $status => $label) {
            foreach (self::ITEMS as $item => $category) {
                $index++;
                $this->seedReport($owner, $admin, $status, $label, $item, $category, $index);
            }
        }
    }

    private function seedReport(
        User $owner,
        ?Admin $admin,
        string $status,
        string $label,
        string $item,
        string $category,
        int $index,
    ): void {
        $itemName = "{$item} Hilang Uji {$label}";
        $lostAt = now()->subDays(10 + $index)->setTime(9, 0);
        $location = self::LOCATIONS[($index - 1) % count(self::LOCATIONS)];
        $photoPath = MissingReportPhoto::DIRECTORY."/wandi-hilang-uji-{$index}.svg";

        Storage::disk(MissingReportPhoto::PRIVATE_DISK)->put($photoPath, $this->placeholderSvg($itemName));

        $foundAt = match ($status) {
            MissingReport::STATUS_FOUND => now()->subDays(3 + ($index % 7))->setTime(15, 0),
            MissingReport::STATUS_ARCHIVED => now()->subDays(35 + ($index % 7))->setTime(15, 0),
            default => null,
        };

        $archivedAt = $status === MissingReport::STATUS_ARCHIVED
            ? $foundAt?->copy()->addDays(MissingReport::FOUND_DISPLAY_DAYS)
            : null;

        $report = MissingReport::query()->updateOrCreate(
            ['user_id' => $owner->id, 'item_name' => $itemName],
            [
                'category' => $category,
                'description' => "Data uji coba fiktif untuk status {$label}. {$item} fiktif hilang di {$location} tanpa data pribadi.",
                'lost_at' => $lostAt->toDateString(),
                'location' => $location,
                'contact_phone' => $owner->phone ?: '0813'.str_pad((string) $index, 7, '0', STR_PAD_LEFT),
                'photo_path' => $photoPath,
                'status' => $status,
                'terms_accepted_at' => $lostAt,
                'found_at' => $foundAt,
                'archived_at' => $archivedAt,
            ],
        );

        DB::table('missing_reports')
            ->where('id', $report->id)
            ->update([
                'created_at' => $lostAt,
                'updated_at' => $archivedAt ?? $foundAt ?? $lostAt,
            ]);

        $this->seedHistory($report, $owner, $admin, $status, $lostAt, $foundAt, $archivedAt);
    }

    private function seedHistory(
        MissingReport $report,
        User $owner,
        ?Admin $admin,
        string $status,
        Carbon $lostAt,
        ?Carbon $foundAt,
        ?Carbon $archivedAt,
    ): void {
        $entries = match ($status) {
            MissingReport::STATUS_APPROVED, MissingReport::STATUS_FOUND, MissingReport::STATUS_ARCHIVED => [[
                'action' => 'approve',
                'from_status' => MissingReport::STATUS_PENDING,
                'to_status' => MissingReport::STATUS_APPROVED,
                'actor_id' => null,
                'admin_id' => $admin?->id,
                'reason' => null,
                'note' => 'Laporan uji coba kehilangan disetujui.',
                'time' => $lostAt->copy()->addDay(),
            ]],
            MissingReport::STATUS_REJECTED => [[
                'action' => 'reject',
                'from_status' => MissingReport::STATUS_PENDING,
                'to_status' => MissingReport::STATUS_REJECTED,
                'actor_id' => null,
                'admin_id' => $admin?->id,
                'reason' => 'Data uji coba: deskripsi kehilangan perlu diperjelas.',
                'note' => null,
                'time' => $lostAt->copy()->addDay(),
            ]],
            MissingReport::STATUS_CANCELLED => [[
                'action' => 'cancel_by_owner',
                'from_status' => MissingReport::STATUS_PENDING,
                'to_status' => MissingReport::STATUS_CANCELLED,
                'actor_id' => $owner->id,
                'admin_id' => null,
                'reason' => null,
                'note' => 'Laporan uji coba kehilangan dibatalkan pemilik.',
                'time' => $lostAt->copy()->addDays(2),
            ]],
            default => [],
        };

        if (in_array($status, [MissingReport::STATUS_FOUND, MissingReport::STATUS_ARCHIVED], true)) {
            $entries[] = [
                'action' => 'found_by_owner',
                'from_status' => MissingReport::STATUS_APPROVED,
                'to_status' => MissingReport::STATUS_FOUND,
                'actor_id' => $owner->id,
                'admin_id' => null,
                'reason' => null,
                'note' => 'Barang uji coba ditemukan langsung oleh pemilik.',
                'time' => $foundAt,
            ];
        }

        if ($status === MissingReport::STATUS_ARCHIVED) {
            $entries[] = [
                'action' => 'archive',
                'from_status' => MissingReport::STATUS_FOUND,
                'to_status' => MissingReport::STATUS_ARCHIVED,
                'actor_id' => null,
                'admin_id' => null,
                'reason' => null,
                'note' => 'Diarisipkan otomatis setelah 30 hari sejak ditemukan.',
                'time' => $archivedAt,
            ];
        }

        foreach ($entries as $entry) {
            $time = $entry['time'];
            unset($entry['time']);

            $history = MissingReportStatusHistory::query()->updateOrCreate(
                [
                    'missing_report_id' => $report->id,
                    'action' => $entry['action'],
                    'to_status' => $entry['to_status'],
                ],
                $entry,
            );

            DB::table('missing_report_status_histories')
                ->where('id', $history->id)
                ->update(['created_at' => $time, 'updated_at' => $time]);
        }
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
