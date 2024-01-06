<?php

namespace App\Domains\Item\Entities;

use App\Http\Requests\Item\UpdateItemRequest;

class UpdateItemEntity
{
    public string $id;
    public string $name;
    public string $picture;
    public int $stock;
    public string $unit;
    public int $unit_price;
    public function __construct(string $itemId, UpdateItemRequest $validatedRequest)
    {
        $this->id = $itemId;
        $this->name = $validatedRequest->name;
        $this->picture = $validatedRequest->picture;
        $this->stock = $validatedRequest->stock;
        $this->unit = $validatedRequest->unit;
        $this->unit_price = $validatedRequest->unit_price;

        return $this;
    }
}
