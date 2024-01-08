<?php

namespace App\Services\Order;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface OrderServiceInterface {
    public function storeOrder(StoreOrderRequest $validatedRequest, User $loggedUser) : Order;
    public function getOrder(string $orderId, User $loggedUser) : Order;
    public function getOrders(User $loggedUser) : Collection;
}
