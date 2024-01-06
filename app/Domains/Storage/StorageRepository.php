<?php

namespace App\Domains\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class StorageRepository implements StorageRepositoryInterface
{
    private Storage|Filesystem $_storage;
    public function __construct(Storage|Filesystem $storage)
    {
        $this->_storage = $storage;
    }

    public function storeFile(string $path, $file): string
    {
        $storedPicture = $this->_storage->put($path, $file);
        return $storedPicture;
    }

    public function checkFileIsExist(string $filePath): bool
    {
        $result = $this->_storage->exists($filePath);
        return $result;
    }

    public function deleteFile(string $filePath): void
    {
        $this->_storage->delete($filePath);
    }
}
