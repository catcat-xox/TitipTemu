<?php

namespace App\Support;

use App\Models\LostReport;
use App\Models\MissingReport;

final class WhatsAppLink
{
    public static function forReport(LostReport|MissingReport $report): ?string
    {
        $phone = self::normalizePhone($report->contact_phone);

        if ($phone === null) {
            return null;
        }

        $message = $report instanceof MissingReport
            ? 'Halo, saya melihat laporan kehilangan barang "'.$report->item_name.'" di TitipTemu. '
                .'Saya ingin menghubungi Anda untuk membantu pencarian barang.'
            : 'Halo, saya menemukan informasi barang "'.$report->item_name.'" di TitipTemu. '
                .'Saya ingin menghubungi Anda untuk langkah pengembalian.';

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }

    public static function normalizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return strlen($digits) >= 8 && strlen($digits) <= 15 ? $digits : null;
    }
}
