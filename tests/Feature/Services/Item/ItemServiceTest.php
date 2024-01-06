<?php

namespace Tests\Feature\Services\Item;

use App\Domains\Item\ItemRepositoryInterface;
use App\Domains\Storage\StorageRepositoryInterface;
use App\Enums\UserRoleEnum;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Item;
use App\Models\User;
use App\Services\Item\ItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    function test_store_item(): void
    {
        Storage::fake('public');
        $picture = UploadedFile::fake()->image('test.jpg');
        $validatedRequest = new StoreItemRequest([
            'name' => fake()->word,
            'stock' => 100,
            'picture' => $picture,
            'unit' => 'pcs',
            'unit_price' => 10000,
        ]);

        //* auth
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);

        //* action
        $itemService = new ItemService(
            app()->make(ItemRepositoryInterface::class),
            app()->make(StorageRepositoryInterface::class),
        );
        $storeItem = $itemService->storeItem($fakeUser, $validatedRequest);

        //* assert
        $this->assertDatabaseCount(Item::class, 1);
        $this->assertDatabaseHas(Item::class, [
            'name' => $storeItem->name,
            'stock' => $storeItem->stock,
            'picture' => $storeItem->picture,
            'unit' => $storeItem->unit,
            'unit_price' => $storeItem->unit_price,
        ]);
        Storage::disk('public')->assertExists($storeItem->picture);
    }

    function test_get_list_items(): void
    {
        //* params
        $fakeItems = Item::factory(10)->create();

        //* action
        $itemService = new ItemService(
            app()->make(ItemRepositoryInterface::class),
            app()->make(StorageRepositoryInterface::class),
        );
        $items = $itemService->getListItems();

        //* assert
        $this->assertCount(10, $items);
    }

    function test_get_detail_item(): void
    {
        //* params
        $fakeItem = Item::factory()->create();

        //* action
        $itemService = new ItemService(
            app()->make(ItemRepositoryInterface::class),
            app()->make(StorageRepositoryInterface::class),
        );
        $item = $itemService->getDetailItem($fakeItem->id);

        //* assert
        $this->assertEquals($fakeItem->id, $item->id);
        $this->assertEquals($fakeItem->name, $item->name);
    }

    function test_soft_delete_item(): void
    {
        //* params
        $fakeItem = Item::factory()->create();

        //* action
        $itemService = new ItemService(
            app()->make(ItemRepositoryInterface::class),
            app()->make(StorageRepositoryInterface::class),
        );
        $item = $itemService->softDeleteItem($fakeItem->id);

        //* assert
        $this->assertDatabaseHas(Item::class, [
            'id' => $fakeItem->id,
        ]);
        $this->assertSoftDeleted(Item::class, [
            'id' => $fakeItem->id,
        ]);
    }

    function test_update_item(): void
    {
        //* params
        Storage::fake('public');
        $fakeItem = Item::factory()->create();
        $picture = UploadedFile::fake()->image('test.jpg');
        $validatedRequest = new UpdateItemRequest([
            'name' => fake()->word,
            'stock' => 100,
            'picture' => $picture,
            'unit' => 'pcs',
            'unit_price' => 10000,
        ]);

        //* auth
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);

        //* action
        $itemService = new ItemService(
            app()->make(ItemRepositoryInterface::class),
            app()->make(StorageRepositoryInterface::class),
        );
        $updatedItem = $itemService->updateItem($fakeUser, $fakeItem->id, $validatedRequest);

        //* assert
        $this->assertDatabaseHas(Item::class, [
            'id' => $fakeItem->id,
            'name' => $validatedRequest->name,
            'stock' => $validatedRequest->stock,
            'unit' => $validatedRequest->unit,
            'unit_price' => $validatedRequest->unit_price,
        ]);
        Storage::disk('public')->assertExists(Item::find($fakeItem->id)->picture);
    }
}
