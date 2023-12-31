<?php

namespace App\Domains\Item;

use App\Domains\Item\Entities\StoreItemEntity;
use App\Domains\Item\Entities\UpdateItemEntity;
use App\Domains\Items\Entities\CheckItemIsExistEntity;
use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;

interface ItemRepositoryInterface
{
    public function storeItem(StoreItemEntity $params): Item;
    public function getItems(): Collection;
    public function getItem(string $itemId): Item;
    public function updateItem(UpdateItemEntity $params): Item;
    public function softDeleteItem(string $itemId): void;
    public function checkItemIsExist(CheckItemIsExistEntity $params): void;
    public function substractItemStock(string $itemId, int $qty): void;
}
