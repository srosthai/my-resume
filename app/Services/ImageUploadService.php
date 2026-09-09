<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores and removes uploaded images on the "uploads" disk.
 *
 * Paths are persisted in the database as "uploads/{dir}/{file}" so that
 * existing rows and the public URLs (/uploads/...) keep working. Every
 * delete is confined to the uploads disk, so a stored path can never reach
 * outside public/uploads.
 */
class ImageUploadService
{
    public const DISK = 'uploads';

    public const PREFIX = 'uploads/';

    public function store(UploadedFile $file, string $dir): string
    {
        $dir = trim($dir, '/');
        $path = Storage::disk(self::DISK)->putFile($dir, $file);

        return self::PREFIX.$path;
    }

    public function delete(?string $path): void
    {
        $relative = $this->relative($path);

        if ($relative === null) {
            return;
        }

        Storage::disk(self::DISK)->delete($relative);
    }

    /**
     * Whether the given stored path refers to a file managed by this service.
     */
    public function isManaged(?string $path): bool
    {
        return $this->relative($path) !== null;
    }

    /**
     * Turn a persisted "uploads/dir/file" path into a disk-relative "dir/file".
     * Returns null for anything that is not a plain path inside the uploads
     * directory (absolute paths, URLs, traversal attempts, other disks).
     */
    private function relative(?string $path): ?string
    {
        if ($path === null || ! str_starts_with($path, self::PREFIX)) {
            return null;
        }

        $relative = substr($path, strlen(self::PREFIX));

        if ($relative === '' || str_contains($relative, '..') || str_starts_with($relative, '/') || str_contains($relative, '://')) {
            return null;
        }

        return $relative;
    }
}
