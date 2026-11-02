<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Media
{
    // URL of an uploaded file on whichever disk the app uses (local "public" folder or the Cloud bucket)
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk(config('filesystems.default'));

        return $disk->url($path);
    }
}