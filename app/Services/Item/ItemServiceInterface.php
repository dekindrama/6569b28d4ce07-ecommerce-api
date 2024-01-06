<?php

namespace App\Services\Item;

use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ItemServiceInterface
{
    public function storeItem(User $loggedUser, StoreItemRequest $validatedRequest): Item;
    public function getListItems(): Collection;
    public function getDetailItem(string $itemId): Item;
    public function updateItem(User $loggedUser, string $itemId, UpdateItemRequest $validatedRequest): Item;
    public function softDeleteItem(string $itemId): void;
}
