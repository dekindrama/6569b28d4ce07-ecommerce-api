<?php

namespace App\Services\Item;

use App\Domains\Item\Entities\StoreItemEntity;
use App\Domains\Item\Entities\UpdateItemEntity;
use App\Domains\Item\ItemRepositoryInterface;
use App\Domains\Storage\StorageRepositoryInterface;
use App\Enums\UserRoleEnum;
use App\Exceptions\Commons\UnauthorizedException;
use App\Helpers\StorageHelper;
use App\Models\User;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;

class ItemService implements ItemServiceInterface
{
    private ItemRepositoryInterface|MockInterface $_itemRepository;
    private StorageRepositoryInterface|MockInterface $_storageRepository;
    public function __construct(
        ItemRepositoryInterface|MockInterface $itemRepository,
        StorageRepositoryInterface|MockInterface $storageRepository,
    ) {
        $this->_itemRepository = $itemRepository;
        $this->_storageRepository = $storageRepository;
    }

    function storeItem(User $loggedUser, StoreItemRequest $validatedRequest): Item
    {
        //* check logged user is admin / super admin
        $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
        if ($isNotAuthorized) {
            throw new UnauthorizedException('action is unauthorized');
        }

        //* store image
        $storedPicture = $this->_storageRepository->storeFile(
            StorageHelper::generateFilePathItem(),
            $validatedRequest->picture,
        );

        //* store item
        $storeItemEntity = new StoreItemEntity(new StoreItemRequest([
            ...$validatedRequest->toArray(),
            'picture' => $storedPicture,
        ]));
        $storedItem = $this->_itemRepository->storeItem($storeItemEntity);

        //* return data
        return $storedItem;
    }

    function getListItems(): Collection
    {
        $items = $this->_itemRepository->getItems();
        return $items;
    }

    function getDetailItem(string $itemId): Item
    {
        $item = $this->_itemRepository->getItem($itemId);
        return $item;
    }

    function softDeleteItem(string $itemId): void
    {
        $this->_itemRepository->softDeleteItem($itemId);
    }

    function updateItem(User $loggedUser, string $itemId, UpdateItemRequest $validatedRequest): Item
    {
        //* check logged user is admin / super admin
        $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
        if ($isNotAuthorized) {
            throw new UnauthorizedException('action is unauthorized');
        }

        //* get item
        $item = $this->getDetailItem($itemId);

        //* update picture
        $updatedPicture = $this->_updatePicture($item->picture, $validatedRequest->picture);

        //* update item
        $updateItemEntity = new UpdateItemEntity($itemId, new UpdateItemRequest([
            ...$validatedRequest->toArray(),
            'picture' => $updatedPicture,
        ]));
        $updatedItem = $this->_itemRepository->updateItem($updateItemEntity);

        return $updatedItem;
    }

    private function _updatePicture(string $oldPicturePath, mixed $newPicture): string
    {
        $updatedPicture = $oldPicturePath;
        if ($newPicture) {
            //* delete old picture
            if ($this->_storageRepository->checkFileIsExist($oldPicturePath)) {
                $this->_storageRepository->deleteFile($oldPicturePath);
            }
            //* store new picture
            $updatedPicture = $this->_storageRepository->storeFile(StorageHelper::generateFilePathItem(), $newPicture);
        }

        return $updatedPicture;
    }
}
