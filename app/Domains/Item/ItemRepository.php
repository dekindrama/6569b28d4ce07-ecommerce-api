<?php

namespace App\Domains\Item;

use App\Domains\Item\Entities\StoreItemEntity;
use App\Domains\Item\Entities\UpdateItemEntity;
use App\Domains\Items\Entities\CheckItemIsExistEntity;
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
        $storedItem = $this->_itemModel->create([
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

    public function checkItemIsExist(CheckItemIsExistEntity $params): void
    {
        $item = $this->_itemModel->query()
        ->where('id', $params->id)
        ->where('name', $params->name)
        ->where('unit', $params->unit)
        ->where('unit_price', $params->unit_price)
        ->first();
        if (!$item) {
            throw new NotFoundException('item not found');
        }
    }

    public function substractItemStock(string $itemId, int $qty): void
    {
        //* find item
        $item = $this->_itemModel->find($itemId);
        if (!$item) {
            throw new NotFoundException('item not found');
        }
        //* substact item stock
        $item->decrement('stock', $qty);
    }
}
