<?php
namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaPath
{
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        if (self::isRemote($path)) {
            return $path;
        }
        return Storage::disk('public')->exists($path)
            ? Storage::url($path)
            : null;
    }

    public static function deleteIfLocal(?string $path): void
    {
        if (!self::isLocal($path)) {
            return;
        }
        Storage::disk('public')->delete($path);
    }

    public static function isRemote(?string $path): bool
    {
        return $path ? Str::startsWith($path, ['http://', 'https://']) : false;
    }

    public static function isLocal(?string $path): bool
    {
        return $path && !self::isRemote($path);
    }
}
