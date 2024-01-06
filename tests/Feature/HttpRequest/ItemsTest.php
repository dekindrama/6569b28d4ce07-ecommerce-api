<?php

namespace Tests\Feature\HttpRequest;

use App\Enums\UserRoleEnum;
use App\Helpers\StorageHelper;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Http\Testing\File;

class ItemsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_store_item(): void
    {
        //* params
        Storage::fake('public');
        $image = UploadedFile::fake()->image('avatar.jpg');
        $validatedRequest = (object)[
            'name' => 'item 1',
            'stock' => 100,
            'picture' => $image,
            'unit' => 'pcs',
            'unit_price' => 20000,
        ];

        //* auth
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);

        //* action
        $response = $this->post(route('items.store'), [
            'name' => $validatedRequest->name,
            'stock' => $validatedRequest->stock,
            'picture' => $validatedRequest->picture,
            'unit' => $validatedRequest->unit,
            'unit_price' => $validatedRequest->unit_price,
        ]);

        //* assert
        Storage::disk('public')->assertExists(StorageHelper::generateFilePathItem() . '/' . $image->hashName());
        $response->assertCreated();
        $response->assertJson([
            'status' => true,
            'data' => [
                'item' => [
                    'name' => $validatedRequest->name,
                    'stock' => $validatedRequest->stock,
                    'picture' => 'pictures/items/' . $image->hashName(),
                    'unit' => $validatedRequest->unit,
                    'unit_price' => $validatedRequest->unit_price,
                ]
            ],
        ]);
    }

    public function test_update_item(): void
    {
        //* auth
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);

        //* params
        Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg');
        $newImage = UploadedFile::fake()->image('new_image.jpg');
        $validatedRequest = (object)[
            'name' => fake()->word,
            'stock' => 100,
            'picture' => $newImage,
            'unit' => 'pcs',
            'unit_price' => 20000,
        ];

        //* store item
        $dummyItem = $this->_dummyStoreItem($image);

        //* action
        $response = $this->post(route('items.update', ['item_id' => $dummyItem->id]), [
            'name' => $validatedRequest->name,
            'stock' => $validatedRequest->stock,
            'picture' => $validatedRequest->picture,
            'unit' => $validatedRequest->unit,
            'unit_price' => $validatedRequest->unit_price,
        ]);

        //* assert
        Storage::disk('public')->assertMissing('pictures/items/' . $image->hashName());
        Storage::disk('public')->assertExists('pictures/items/' . $newImage->hashName());
        $response->assertOk();
        $response->assertJson([
            'status' => true
        ]);
    }

    public function test_soft_delete_item(): void
    {
        //* auth
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);

        //* params
        Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg');

        //* store item
        $dummyItem = $this->_dummyStoreItem($image);

        //* action
        $response = $this->post(route('items.delete', ['item_id' => $dummyItem->id]));

        //* assert
        $response->assertOk();
        $response->assertJson([
            'status' => true
        ]);
    }

    function test_get_list_items(): void
    {
        //* auth
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);

        //* params
        $fakeItems = Item::factory(10)->create();

        //* action
        $response = $this->get(route('items.index'));

        //* assert
        $response->assertOk();
        $response->assertJsonCount(10, 'data.items');
        $response->assertJson([
            'status' => true,
        ]);
    }

    public function test_get_item(): void
    {
        //* auth
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);

        //* params
        $fakeItem = Item::factory()->create();

        //* action
        $response = $this->get(route('items.show', ['item_id' => $fakeItem->id]));

        //* assert
        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'data' => [
                'item' => [
                    'id' => $fakeItem->id,
                    'name' => $fakeItem->name,
                    'picture' => StorageHelper::getUrl($fakeItem->picture),
                    'stock' => $fakeItem->stock,
                    'status_stock' => $fakeItem->status_stock,
                    'unit' => $fakeItem->unit,
                    'unit_price' => $fakeItem->unit_price,
                ]
            ]
        ]);
    }

    private function _dummyStoreItem(File $image)
    {
        //* validated image
        $validatedRequest = (object)[
            'name' => fake()->word,
            'stock' => 100,
            'picture' => $image,
            'unit' => 'pcs',
            'unit_price' => 20000,
        ];

        //* store image
        $response = $this->post(route('items.store'), [
            'name' => $validatedRequest->name,
            'stock' => $validatedRequest->stock,
            'picture' => $validatedRequest->picture,
            'unit' => $validatedRequest->unit,
            'unit_price' => $validatedRequest->unit_price,
        ]);

        return json_decode($response->getContent())->data->item;
    }
}
