<?php

namespace App\Domains\Item;

use App\Domains\Item\Entities\StoreItemEntity;
use App\Domains\Item\Entities\UpdateItemEntity;
use App\Exceptions\Commons\NotFoundException;
use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ItemRepository implements ItemRepositoryInterface
{
    private Item $_itemModel;
    public function __construct(Item $itemModel)
    {
        $this->_itemModel = $itemModel;
    }

    function storeItem(StoreItemEntity $params): Item
    {
        $storedItem = Item::create([
            'id' => Str::orderedUuid(),
            'name' => $params->name,
            'picture' => $params->picture,
            'stock' => $params->stock,
            'unit' => $params->unit,
            'unit_price' => $params->unit_price,
        ]);

        return $storedItem;
    }

    function getItems(): Collection
    {
        $items = $this->_itemModel->get();
        return $items;
    }

    function getItem(string $itemId): Item
    {
        $item = $this->_itemModel->find($itemId);
        if (!$item) {
            throw new NotFoundException('item not found');
        }

        return $item;
    }

    function softDeleteItem(string $itemId): void
    {
        $item = $this->_itemModel->find($itemId);
        if (!$item) {
            throw new NotFoundException('item not found');
        }

        $item->delete();
    }

    function updateItem(UpdateItemEntity $params): Item
    {
        $item = $this->_itemModel->find($params->id);
        if (!$item) {
            throw new NotFoundException('item not found');
        }

        $updatedItem = $item->update([
            'name' => $params->name,
            'picture' => $params->picture,
            'stock' => $params->stock,
            'unit' => $params->unit,
            'unit_price' => $params->unit_price,
        ]);

        $item = $this->_itemModel->find($params->id);
        if (!$item) {
            throw new NotFoundException('item not found');
        }
        return $item;
    }
}
