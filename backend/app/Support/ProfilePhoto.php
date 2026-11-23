<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class ProfilePhoto
{
    public const DIRECTORY = 'profile-photos';

    public const PRIVATE_DISK = 'local';

    public static function store(UploadedFile $file): string
    {
        return $file->store(self::DIRECTORY, self::PRIVATE_DISK);
    }

    public static function isValidPath(?string $path): bool
    {
        return is_string($path)
            && str_starts_with($path, self::DIRECTORY.'/')
            && ! str_contains($path, '..')
            && ! str_contains($path, '\\');
    }

    public static function delete(?string $path): void
    {
        if (! self::isValidPath($path)) {
            return;
        }

        Storage::disk(self::PRIVATE_DISK)->delete($path);
    }

    public static function response(User $user)
    {
        $path = $user->profile_photo_path;

        abort_unless(self::isValidPath($path), 404);
        abort_unless(Storage::disk(self::PRIVATE_DISK)->exists($path), 404);

        $response = response()->file(
            Storage::disk(self::PRIVATE_DISK)->path($path),
            ['Content-Type' => Storage::disk(self::PRIVATE_DISK)->mimeType($path) ?: 'application/octet-stream'],
        );

        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
