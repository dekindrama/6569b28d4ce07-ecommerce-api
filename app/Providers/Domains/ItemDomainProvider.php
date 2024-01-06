<?php

namespace App\Providers\Domains;

use App\Domains\Item\ItemRepository;
use App\Domains\Item\ItemRepositoryInterface;
use App\Models\Item;
use Illuminate\Support\ServiceProvider;

class ItemDomainProvider extends ServiceProvider
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
        $this->app->bind(ItemRepositoryInterface::class, function ($app): ItemRepository {
            return new ItemRepository(
                new Item(),
            );
        });
    }
}
