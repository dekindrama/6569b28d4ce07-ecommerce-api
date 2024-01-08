<?php

namespace App\Domains\Items\Entities;

class CheckItemIsExistEntity
{
    public string $id;
    public string $name;
    public string $unit;
    public int $unit_price;
    public function __construct(string $id, string $name, string $unit, int $unitPrice) {
        $this->id = $id;
        $this->name = $name;
        $this->unit = $unit;
        $this->unit_price = $unitPrice;

        return $this;
    }
}
