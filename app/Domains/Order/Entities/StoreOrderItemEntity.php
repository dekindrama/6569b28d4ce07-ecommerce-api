<?php

namespace App\Domains\Order\Entities;

class StoreOrderItemEntity
{
    public string $item_id;
    public string $order_id;
    public string $name;
    public string $unit;
    public int $unit_price;
    public int $qty;
    public int $subtotal_price;

    public function __construct(object $params) {
        $this->item_id = $params->item_id;
        $this->order_id = $params->order_id;
        $this->name = $params->name;
        $this->unit = $params->unit;
        $this->unit_price = $params->unit_price;
        $this->qty = $params->qty;
        $this->subtotal_price = $params->subtotal_price;

        return $this;
    }
}
