<?php

namespace App\Console\Commands;

use App\Services\MissingReportArchiveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ArchiveFoundMissingReports extends Command
{
    protected $signature = 'missing-reports:archive-found';

    protected $description = 'Mengarsipkan laporan Barang Hilang yang sudah ditemukan lebih dari 30 hari.';

    public function handle(MissingReportArchiveService $service): int
    {
        try {
            $archived = $service->archiveDue();
        } catch (Throwable $exception) {
            Log::error('Pengarsipan laporan kehilangan gagal.', [
                'error' => $exception->getMessage(),
            ]);
            $this->error('Pengarsipan gagal: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Diarsipkan: {$archived} laporan kehilangan.");

        return self::SUCCESS;
    }
}
