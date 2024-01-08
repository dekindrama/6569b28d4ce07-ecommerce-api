<?php

namespace App\Domains\Order\Entities;

class StoreOrderEntity
{
    public int $total_all_price;
    public function __construct(int $totalAllPrice) {
        $this->total_all_price = $totalAllPrice;

        return $this;
    }
}
