<?php

namespace App\Providers\Domains;

use App\Domains\Storage\StorageRepository;
use App\Domains\Storage\StorageRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class StorageDomainProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(StorageRepositoryInterface::class, function ($app): StorageRepositoryInterface {
            return new StorageRepository(Storage::disk('public'));
        });
    }
}
