<?php

namespace App\Domains\Order\Entities;

class CheckTotalAllPriceIsEqualEntity
{
    public string $id;
    public int $qty;
    public int $total_all_price;
    public function __construct(string $id, int $qty, int $totalAllPrice) {
        $this->id = $id;
        $this->qty = $qty;
        $this->total_all_price = $totalAllPrice;

        return $this;
    }
}
