<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Util
{
    /**
     * Generate a publicly accessible URL for an image stored on the public disk.
     *
     * This method assumes `php artisan storage:link` has been run and
     * the file exists under the `public` disk.
     *
     * Typical use cases:
     * - Displaying images in web views
     * - Linking images in frontend applications
     *
     * @param string|null $filename Relative path within the public disk (e.g. "logos/company.png")
     * @return string|null Public URL to the image or null if not found
     */
    public static function pictureUrl(?string $filename): ?string
    {
        if ($filename && Storage::disk('public')->exists($filename)) {
            return asset('storage/' . ltrim($filename, '/'));
        }

        return null;
    }

    /**
     * Convert an image/file to a Base64 data URI whether it lives in:
     * - storage disks (public, local, s3, etc)
     * - public directory (public/)
     *
     * Supported inputs:
     * - "logos/company.png"
     * - "storage/logos/company.png"
     * - "/public/images/logo.jpg"
     * - Full public path
     *
     * @param string|null $path
     * @param string $disk Default storage disk
     * @return string|null
     */
    public static function fileBase64(?string $path, string $disk = 'public'): ?string
    {
        if (!$path) {
            return null;
        }

        // Normalize path
        $path = ltrim($path, '/');

        /**
         * 1️⃣ Check storage disk
         */
        if (Storage::disk($disk)->exists($path)) {
            $contents = Storage::disk($disk)->get($path);
            $mime = Storage::disk($disk)->mimeType($path);

            return 'data:' . $mime . ';base64,' . base64_encode($contents);
        }

        /**
         * 2️⃣ Check public/storage symlink paths
         */
        $publicStoragePath = public_path('storage/' . $path);
        if (File::exists($publicStoragePath)) {
            $contents = File::get($publicStoragePath);
            $mime = File::mimeType($publicStoragePath);

            return 'data:' . $mime . ';base64,' . base64_encode($contents);
        }

        /**
         * 3️⃣ Check direct public path
         */
        $publicPath = public_path($path);
        if (File::exists($publicPath)) {
            $contents = File::get($publicPath);
            $mime = File::mimeType($publicPath);

            return 'data:' . $mime . ';base64,' . base64_encode($contents);
        }

        return null;
    }

    /**
     * Convert a file size in bytes to a human-readable format.
     *
     * Examples:
     * - 1024      → 1.00 KB
     * - 1048576   → 1.00 MB
     * - 536870912 → 512.00 MB
     *
     * Common use cases:
     * - Displaying upload sizes
     * - Showing attachment sizes
     * - Reporting storage usage
     *
     * @param int|float $bytes File size in bytes
     * @param int $decimals Number of decimal places to display
     * @return string Human-readable file size string
     */
    public static function humanFileSize(int|float $bytes, int $decimals = 2): string
    {
        $sizeUnits = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        $factor = floor((strlen((string) $bytes) - 1) / 3);

        return sprintf(
            "%.{$decimals}f",
            $bytes / pow(1024, $factor)
        ) . ' ' . $sizeUnits[$factor];
    }
}
