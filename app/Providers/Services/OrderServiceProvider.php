<?php

namespace App\Providers\Services;

use App\Domains\Item\ItemRepositoryInterface;
use App\Domains\Order\OrderRepositoryInterface;
use App\Services\Order\OrderService;
use App\Services\Order\OrderServiceInterface;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
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
        $this->app->bind(OrderServiceInterface::class, function ($app) : OrderService {
            return new OrderService(
                $app->make(ItemRepositoryInterface::class),
                $app->make(OrderRepositoryInterface::class),
            );
        });
    }
}
