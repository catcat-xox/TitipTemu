<?php

namespace App\Console\Commands;

use App\Services\ReportArchiveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ArchiveReturnedReports extends Command
{
    protected $signature = 'reports:archive-returned';

    protected $description = 'Mengarsipkan laporan Barang Ditemukan yang sudah melewati batas 30 hari.';

    public function handle(ReportArchiveService $service): int
    {
        try {
            $archived = $service->archiveDue();
        } catch (Throwable $exception) {
            Log::error('Pengarsipan laporan gagal.', [
                'error' => $exception->getMessage(),
            ]);
            $this->error('Pengarsipan gagal: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Diarsipkan: {$archived} laporan.");

        return self::SUCCESS;
    }
}
