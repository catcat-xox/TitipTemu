<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

final class MissingReportPhoto
{
    public const DIRECTORY = 'missing-reports';

    public const PRIVATE_DISK = 'local';

    public static function store(UploadedFile $file): string
    {
        return $file->store(self::DIRECTORY, self::PRIVATE_DISK);
    }
}
