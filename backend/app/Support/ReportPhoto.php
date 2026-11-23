<?php

namespace App\Support;

use App\Models\LostReport;
use App\Models\MissingReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class ReportPhoto
{
    public const DIRECTORY = 'lost-reports';

    public const PRIVATE_DISK = 'local';

    public static function store(UploadedFile $file): string
    {
        return $file->store(self::DIRECTORY, self::PRIVATE_DISK);
    }

    public static function response(LostReport|MissingReport $report)
    {
        $directory = $report instanceof MissingReport ? MissingReportPhoto::DIRECTORY : self::DIRECTORY;
        $path = $report->photo_path;

        abort_unless(
            is_string($path)
                && str_starts_with($path, $directory.'/')
                && ! str_contains($path, '..')
                && ! str_contains($path, '\\'),
            404
        );

        foreach ([self::PRIVATE_DISK, 'public'] as $disk) {
            if (! Storage::disk($disk)->exists($path)) {
                continue;
            }

            $response = response()->file(
                Storage::disk($disk)->path($path),
                ['Content-Type' => Storage::disk($disk)->mimeType($path) ?: 'application/octet-stream'],
            );

            $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
            $response->headers->set('Pragma', 'no-cache');

            return $response;
        }

        abort(404);
    }
}
