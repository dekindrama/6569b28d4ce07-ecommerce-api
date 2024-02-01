<?php

namespace App\Providers\Domains;

use App\Domains\Order\OrderRepository;
use App\Domains\Order\OrderRepositoryInterface;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use Illuminate\Support\ServiceProvider;

class OrderDomainProvider extends ServiceProvider
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
        $this->app->bind(OrderRepositoryInterface::class, function () : OrderRepositoryInterface {
            return new OrderRepository(new Order(), new OrderItem(), new Item(), new OrderPayment());
        });
    }
}
