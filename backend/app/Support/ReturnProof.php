<?php

namespace App\Support;

use App\Models\ReturnConfirmation;
use Illuminate\Support\Facades\Storage;

final class ReturnProof
{
    public const DIRECTORY = 'return-proofs';

    public const DISK = 'local';

    public static function store(array $files): array
    {
        $paths = [];

        foreach ($files as $file) {
            $paths[] = $file->store(self::DIRECTORY, self::DISK);
        }

        return $paths;
    }

    public static function response(ReturnConfirmation $confirmation, int $index = 0)
    {
        $paths = $confirmation->proof_paths;
        $path = is_array($paths) ? ($paths[$index] ?? null) : null;

        abort_unless(
            is_string($path)
                && str_starts_with($path, self::DIRECTORY.'/')
                && ! str_contains($path, '..')
                && ! str_contains($path, '\\'),
            404
        );

        abort_unless(Storage::disk(self::DISK)->exists($path), 404);

        return response()->file(
            Storage::disk(self::DISK)->path($path),
            ['Content-Type' => Storage::disk(self::DISK)->mimeType($path) ?: 'application/octet-stream'],
        );
    }
}
