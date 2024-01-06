<?php

namespace Tests\Feature\Domains\Storage;

use App\Domains\Storage\StorageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    function test_store_file(): void
    {
        Storage::fake('public');

        //* params
        $testFilePath = 'test';
        $file = UploadedFile::fake()->image('avatar.jpg');

        //* action
        $storageRepository = new StorageRepository(Storage::disk('public'));
        $filePath = $storageRepository->storeFile($testFilePath, $file);

        //* assert
        Storage::disk('public')->assertExists($filePath);
    }

    function test_check_file_is_exist(): void
    {
        Storage::fake('public');

        //* params
        $testFilePath = 'test';
        $file = UploadedFile::fake()->image('avatar.jpg');

        //* action
        $storageRepository = new StorageRepository(Storage::disk('public'));
        $filePath = $storageRepository->storeFile($testFilePath, $file);
        $result = $storageRepository->checkFileIsExist($filePath);

        //* assert
        Storage::disk('public')->assertExists($filePath);
        $this->assertTrue($result);
    }

    function test_delete_file(): void
    {
        Storage::fake('public');

        //* params
        $testFilePath = 'test';
        $file = UploadedFile::fake()->image('avatar.jpg');

        //* action
        $storageRepository = new StorageRepository(Storage::disk('public'));
        $filePath = $storageRepository->storeFile($testFilePath, $file);
        Storage::disk('public')->assertExists($filePath);

        $storageRepository->deleteFile($filePath);

        //* assert
        Storage::disk('public')->assertMissing($filePath);
    }
}
