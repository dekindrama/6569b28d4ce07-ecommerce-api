<?php

namespace App\Domains\Order\Entities;

class CheckTotalPriceIsEqualEntity
{
    public string $id;
    public int $qty;
    public int $subtotal_price;
    public function __construct(string $id, int $qty, int $subtotalPrice) {
        $this->id = $id;
        $this->qty = $qty;
        $this->subtotal_price = $subtotalPrice;

        return $this;
    }
}
