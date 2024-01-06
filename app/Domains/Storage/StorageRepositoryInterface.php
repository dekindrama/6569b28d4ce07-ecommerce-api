<?php

namespace App\Domains\Storage;

interface StorageRepositoryInterface
{
    public function storeFile(string $path, $file): string;
    public function checkFileIsExist(string $filePath): bool;
    public function deleteFile(string $filePath): void;
}
