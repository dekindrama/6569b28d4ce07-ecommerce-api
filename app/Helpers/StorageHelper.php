<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    public static function getUrl(string $filePath): string
    {
        return Storage::disk('public')->url($filePath);
    }

    public static function generateFilePathItem(): string
    {
        return 'pictures/items';
    }
}
