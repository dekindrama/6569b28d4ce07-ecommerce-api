<?php

namespace Tests\Feature\HttpRequest;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    function test_store_order(): void
    {
        //* params
        $fakeItem = Item::factory()->create();
        $validatedRequest = [
            "total_all_price" => $fakeItem->unit_price * 2,
            "item" => [
                "id" => $fakeItem->id->toString(),
                "name" => $fakeItem->name,
                "unit" => $fakeItem->unit,
                "unit_price" => $fakeItem->unit_price,
                "qty" => 2,
                "subtotal_price" => $fakeItem->unit_price * 2,
            ],
            "payment" => [
                "payer_name" => "john doe",
                "paid_amount" => 100000,
                "change_amount" => 60000,
                "payment_type" => "CASH"
            ]
        ];

        //* auth
        $fakeUser = User::factory()->create();
        Sanctum::actingAs($fakeUser);

        //* action
        $response = $this->post(route('orders.store'), $validatedRequest);

        //* assert
        $response->assertCreated();
        $response->assertJson([
            'status' => true,
            'data' => [
                'order' => [
                    'total_all_price' => $validatedRequest['total_all_price'],
                    'item' => [
                        'item_id' => $validatedRequest['item']['id'],
                        'name' => $validatedRequest['item']['name'],
                        'unit' => $validatedRequest['item']['unit'],
                        'unit_price' => $validatedRequest['item']['unit_price'],
                        'qty' => $validatedRequest['item']['qty'],
                        'subtotal_price' => $validatedRequest['item']['subtotal_price'],
                    ]
                ]
            ]
        ]);
    }

    function test_get_order(): void
    {
        //* params
        $fakeOrder = $this->_storeDummyOrder();

        //* action
        $response = $this->get(route('orders.show', ['order_id' => $fakeOrder->id]));

        //* assert
        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'data' => [
                'order' => [
                    'id' => $fakeOrder->id,
                ],

            ]
        ]);
    }

    function test_get_list_orders(): void
    {
        //* params
        $fakeOrder = $this->_storeDummyOrder();

        //* action
        $response = $this->get(route('orders.index'));

        //* assert
        $response->assertOk();

        $response->assertJsonCount(1, 'data.order');
    }

    function test_get_generate_reciept(): void
    {
        //* params
        $fakeOrder = $this->_storeDummyOrder();

        //* action
        $response = $this->get(route('orders.generate_reciept', ['order_id' => $fakeOrder->id]));

        //* assert
        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'data' => [
                'order' => [
                    'id' => $fakeOrder->id,
                ],

            ]
        ]);
    }

    private function _storeDummyOrder(): object
    {
        //* params
        $fakeItem = Item::factory()->create();
        $validatedRequest = [
            "total_all_price" => $fakeItem->unit_price * 2,
            "item" => [
                "id" => $fakeItem->id->toString(),
                "name" => $fakeItem->name,
                "unit" => $fakeItem->unit,
                "unit_price" => $fakeItem->unit_price,
                "qty" => 2,
                "subtotal_price" => $fakeItem->unit_price * 2,
            ],
            "payment" => [
                "payer_name" => "john doe",
                "paid_amount" => 100000,
                "change_amount" => 60000,
                "payment_type" => "CASH"
            ]
        ];

        //* auth
        $fakeUser = User::factory()->create();
        Sanctum::actingAs($fakeUser);

        //* action
        $response = $this->post(route('orders.store'), $validatedRequest);

        return json_decode($response->getContent())->data->order;
    }
}
