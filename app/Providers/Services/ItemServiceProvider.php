<?php

namespace App\Providers\Services;

use App\Domains\Item\ItemRepositoryInterface;
use App\Domains\Storage\StorageRepositoryInterface;
use App\Services\Item\ItemService;
use App\Services\Item\ItemServiceInterface;
use Illuminate\Support\ServiceProvider;

class ItemServiceProvider extends ServiceProvider
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
        $this->app->bind(ItemServiceInterface::class, function ($app): ItemServiceInterface {
            return new ItemService(
                $app->make(ItemRepositoryInterface::class),
                $app->make(StorageRepositoryInterface::class),
            );
        });
    }
}
