<?php

namespace App\Domains\Order;

use App\Domains\Order\Entities\CheckTotalAllPriceIsEqualEntity;
use App\Domains\Order\Entities\CheckTotalPriceIsEqualEntity;
use App\Domains\Order\Entities\StoreOrderEntity;
use App\Domains\Order\Entities\StoreOrderItemEntity;
use App\Domains\Order\Entities\StoreOrderPaymentEntity;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface {
    public function checkTotalPriceIsEqual(CheckTotalPriceIsEqualEntity $params) : void;
    public function checkTotalAllPriceIsEqual(CheckTotalAllPriceIsEqualEntity $params) : void;
    public function storeOrder(StoreOrderEntity $params): Order;
    public function storeOrderItem(StoreOrderItemEntity $params) : OrderItem;
    public function storeOrderPayment(StoreOrderPaymentEntity $params) : OrderPayment;

    public function getOrder(string $orderId) : Order;
    public function getOrders() : Collection;
}
