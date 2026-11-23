<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\LostReport;
use App\Models\ReportStatusHistory;
use App\Models\ReturnConfirmation;
use App\Models\User;
use App\Support\ReportPhoto;
use App\Support\ReturnProof;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WandiStatusDemoSeeder extends Seeder
{
    /**
     * Enam laporan fiktif per status, semuanya dibuat oleh akun wandi saputra.
     */
    private const STATUS_LABELS = [
        'pending' => 'Menunggu',
        'approved' => 'Masih Dicari',
        'return_pending' => 'Pengembalian',
        'rejected' => 'Ditolak',
        'returned' => 'Sudah Kembali',
        'archived' => 'Diarsipkan',
        'cancelled' => 'Dibatalkan',
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
            ->whereRaw('LOWER(name) = ?', ['wandi saputra'])
            ->first();

        if (! $owner) {
            $this->command?->warn('Akun wandi saputra tidak ditemukan; data uji coba dilewati.');

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
        $itemName = "{$item} Uji {$label}";
        $lostAt = now()->subDays(10 + $index)->setTime(9, 0);
        $location = self::LOCATIONS[($index - 1) % count(self::LOCATIONS)];
        $photoPath = ReportPhoto::DIRECTORY."/wandi-uji-{$index}.svg";

        Storage::disk(ReportPhoto::PRIVATE_DISK)->put($photoPath, $this->placeholderSvg($itemName));

        $returnedAt = match ($status) {
            'returned' => now()->subDays(3 + ($index % 7)),
            'archived' => now()->subDays(35 + ($index % 7)),
            default => null,
        };

        $report = LostReport::query()->updateOrCreate(
            ['user_id' => $owner->id, 'item_name' => $itemName],
            [
                'category' => $category,
                'description' => "Data uji coba fiktif untuk status {$label}. {$item} contoh tanpa data pribadi. Ditemukan di {$location}.",
                'lost_at' => $lostAt->toDateString(),
                'location' => $location,
                'contact_phone' => $owner->phone,
                'photo_path' => $photoPath,
                'status' => $status,
                'terms_accepted_at' => $lostAt,
                'returned_at' => $returnedAt,
                'archived_at' => $status === 'archived' ? $returnedAt?->copy()->addDays(30) : null,
            ],
        );

        DB::table('lost_reports')
            ->where('id', $report->id)
            ->update(['created_at' => $lostAt, 'updated_at' => $returnedAt ?? $lostAt]);

        $this->seedHistory($report, $owner, $admin, $status, $lostAt, $returnedAt);
        $this->seedReturnConfirmation($report, $owner, $admin, $status, $index, $returnedAt);
    }

    private function seedHistory(
        LostReport $report,
        User $owner,
        ?Admin $admin,
        string $status,
        mixed $lostAt,
        mixed $returnedAt,
    ): void {
        $entries = match ($status) {
            'approved', 'return_pending', 'returned', 'archived' => [[
                'action' => 'approve',
                'from_status' => 'pending',
                'to_status' => 'approved',
                'actor_id' => null,
                'admin_id' => $admin?->id,
                'reason' => null,
                'note' => 'Laporan uji coba disetujui.',
                'time' => $lostAt->copy()->addDay(),
            ]],
            'rejected' => [[
                'action' => 'reject',
                'from_status' => 'pending',
                'to_status' => 'rejected',
                'actor_id' => null,
                'admin_id' => $admin?->id,
                'reason' => 'Data uji coba: deskripsi perlu diperjelas.',
                'note' => null,
                'time' => $lostAt->copy()->addDay(),
            ]],
            default => [],
        };

        if (in_array($status, ['return_pending', 'returned', 'archived'], true)) {
            $entries[] = [
                'action' => 'return_submitted',
                'from_status' => 'approved',
                'to_status' => 'return_pending',
                'actor_id' => $owner->id,
                'admin_id' => null,
                'reason' => null,
                'note' => 'Konfirmasi pengembalian uji coba dikirim.',
                'time' => $lostAt->copy()->addDays(2),
            ];
        }

        if (in_array($status, ['returned', 'archived'], true)) {
            $entries[] = [
                'action' => 'return_approved',
                'from_status' => 'return_pending',
                'to_status' => 'returned',
                'actor_id' => null,
                'admin_id' => $admin?->id,
                'reason' => null,
                'note' => 'Bukti uji coba diperiksa.',
                'time' => $returnedAt,
            ];
        }

        if ($status === 'archived') {
            $entries[] = [
                'action' => 'archive',
                'from_status' => 'returned',
                'to_status' => 'archived',
                'actor_id' => null,
                'admin_id' => null,
                'reason' => null,
                'note' => 'Diarisipkan otomatis setelah 30 hari.',
                'time' => $returnedAt?->copy()->addDays(30),
            ];
        }

        foreach ($entries as $entry) {
            $time = $entry['time'];
            unset($entry['time']);

            $history = ReportStatusHistory::query()->updateOrCreate(
                [
                    'lost_report_id' => $report->id,
                    'action' => $entry['action'],
                    'to_status' => $entry['to_status'],
                ],
                $entry,
            );

            DB::table('report_status_histories')
                ->where('id', $history->id)
                ->update(['created_at' => $time, 'updated_at' => $time]);
        }
    }

    private function seedReturnConfirmation(
        LostReport $report,
        User $owner,
        ?Admin $admin,
        string $status,
        int $index,
        mixed $returnedAt,
    ): void {
        if (! in_array($status, ['return_pending', 'returned', 'archived'], true)) {
            return;
        }

        $proofPath = ReturnProof::DIRECTORY."/wandi-uji-{$index}.svg";
        Storage::disk(ReturnProof::DISK)->put($proofPath, $this->placeholderSvg('Bukti Pengembalian Uji Coba'));

        $approved = in_array($status, ['returned', 'archived'], true);

        ReturnConfirmation::query()->updateOrCreate(
            ['lost_report_id' => $report->id, 'attempt' => 1],
            [
                'user_id' => $owner->id,
                'owner_name' => 'Pemilik Uji Coba '.$index,
                'admin_id' => null,
                'reviewer_admin_id' => $approved ? $admin?->id : null,
                'status' => $approved ? 'approved' : 'pending',
                'reported_returned_at' => $returnedAt ?? now()->subDay(),
                'proof_paths' => [$proofPath],
                'note' => 'Data uji coba fiktif.',
                'rejection_reason' => null,
                'admin_note' => $approved ? 'Bukti uji coba diperiksa.' : null,
                'is_admin_resolution' => false,
                'reviewed_at' => $approved ? $returnedAt : null,
            ],
        );
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
