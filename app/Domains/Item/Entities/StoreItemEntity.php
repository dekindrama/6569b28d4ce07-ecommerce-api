<?php

namespace App\Domains\Item\Entities;

use App\Http\Requests\Item\StoreItemRequest;

class StoreItemEntity
{
    public string $name;
    public string $picture;
    public int $stock;
    public string $unit;
    public int $unit_price;
    public function __construct(StoreItemRequest $validatedRequest)
    {
        $this->name = $validatedRequest->name;
        $this->picture = $validatedRequest->picture;
        $this->stock = $validatedRequest->stock;
        $this->unit = $validatedRequest->unit;
        $this->unit_price = $validatedRequest->unit_price;

        return $this;
    }
}
