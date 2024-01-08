<?php

namespace Tests\Feature\Domains\Item;

use App\Domains\Item\Entities\StoreItemEntity;
use App\Domains\Item\Entities\UpdateItemEntity;
use App\Domains\Item\ItemRepository;
use App\Domains\Items\Entities\CheckItemIsExistEntity;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    function test_store_item(): void
    {
        //* params
        $validatedRequest = new StoreItemRequest([
            "name" => fake()->word,
            "picture" => 'test.jpg',
            "stock" => 100,
            "unit" => 'pcs',
            "unit_price" => 20000,
        ]);

        //* action
        $entity = new StoreItemEntity($validatedRequest);
        $itemRepository = new ItemRepository(new Item());
        $storedItem = $itemRepository->storeItem($entity);

        $this->assertDatabaseCount(Item::class, 1);
        $this->assertDatabaseHas(Item::class, [
            'name' => $validatedRequest->name,
            'picture' => $validatedRequest->picture,
            'stock' => $validatedRequest->stock,
            'unit' => $validatedRequest->unit,
            'unit_price' => $validatedRequest->unit_price,
        ]);
    }

    function test_get_items(): void
    {
        //* params
        $fakeItems = Item::factory(10)->create();

        //* action
        $itemRepository = new ItemRepository(new Item());
        $resultItems = $itemRepository->getItems();

        //* assert
        $this->assertCount(10, $resultItems);
    }

    function test_get_item(): void
    {
        //* params
        $fakeItem = Item::factory()->create();

        //* action
        $itemRepository = new ItemRepository(new Item());
        $item = $itemRepository->getItem($fakeItem->id);

        //* assert
        $this->assertEquals($fakeItem->id, $item->id);
        $this->assertEquals($fakeItem->name, $item->name);
    }

    function test_soft_delete_item(): void
    {
        //* params
        $fakeItem = Item::factory()->create();

        //* action
        $itemRepository = new ItemRepository(new Item());
        $itemRepository->softDeleteItem($fakeItem->id);

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
        $fakeItem = Item::factory()->create();
        $validatedRequest = new UpdateItemRequest([
            "name" => fake()->word,
            "picture" => 'test.jpg',
            "stock" => 100,
            "unit" => 'pcs',
            "unit_price" => 20000,
        ]);

        //* action
        $itemRepository = new ItemRepository(new Item());
        $entity = new UpdateItemEntity($fakeItem->id, $validatedRequest);
        $itemRepository->updateItem($entity);

        //* assert
        $this->assertDatabaseHas(Item::class, [
            'id' => $fakeItem->id,
            "name" => $validatedRequest->name,
            "picture" => $validatedRequest->picture,
            "stock" => $validatedRequest->stock,
            "unit" => $validatedRequest->unit,
            "unit_price" => $validatedRequest->unit_price,
        ]);
    }

    function test_check_item_is_exist() : void {
        //* params
        $fakeItem = Item::factory()->create();

        //* action
        $itemRepository = new ItemRepository(new Item());
        $entity = new CheckItemIsExistEntity(
            $fakeItem->id,
            $fakeItem->name,
            $fakeItem->unit,
            $fakeItem->unit_price,
        );
        $itemRepository->checkItemIsExist($entity);

        //* assert
        $this->expectNotToPerformAssertions();
    }

    function test_substract_item_stock() : void {
        //* params
        $fakeItem = Item::factory()->create([
            'stock' => 10,
        ]);

        //* action
        $itemRepository = new ItemRepository(new Item());
        $itemRepository->substractItemStock($fakeItem->id, 5);

        //* assert
        $this->assertEquals(5, Item::find($fakeItem->id)->stock);
    }
}
