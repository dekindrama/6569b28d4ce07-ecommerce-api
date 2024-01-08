<?php

namespace Tests\Feature\Services\Order;

use App\Domains\Item\ItemRepositoryInterface;
use App\Domains\Order\OrderRepositoryInterface;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_store_order(): void
    {
        //* params
        $fakeItem = Item::factory()->create();
        $validatedRequest = new StoreOrderRequest([
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
        ]);

        //* auth
        $fakeUser = User::factory()->create();

        //* action
        $orderService = new OrderService(
            app()->make(ItemRepositoryInterface::class),
            app()->make(OrderRepositoryInterface::class),
        );
        $storedOrder = $orderService->storeOrder($validatedRequest, $fakeUser);

        //* assert
        $this->assertDatabaseCount(Order::class, 1);
        $this->assertDatabaseHas(Order::class, [
            'total_all_price' => $validatedRequest['total_all_price']
        ]);
        $this->assertDatabaseCount(OrderItem::class, 1);
        $this->assertDatabaseHas(OrderItem::class, [
            'item_id' => $validatedRequest['item']['id'],
            'name' => $validatedRequest['item']['name'],
            'unit' => $validatedRequest['item']['unit'],
            'unit_price' => $validatedRequest['item']['unit_price'],
            'qty' => $validatedRequest['item']['qty'],
            'subtotal_price' => $validatedRequest['item']['subtotal_price'],
        ]);
        $this->assertDatabaseCount(OrderPayment::class, 1);
        $this->assertDatabaseHas(OrderPayment::class, [
            'payer_name' => $validatedRequest['payment']['payer_name'],
            'paid_amount' => $validatedRequest['payment']['paid_amount'],
            'change_amount' => $validatedRequest['payment']['change_amount'],
            'payment_type' => $validatedRequest['payment']['payment_type'],
        ]);
    }

    function test_get_order() : void {
        //* params
        $fakeOrder = Order::factory()->create();

        //* auth
        $fakeUser = User::factory()->create();

        //* action
        $orderService = new OrderService(
            app()->make(ItemRepositoryInterface::class),
            app()->make(OrderRepositoryInterface::class),
        );
        $order = $orderService->getOrder($fakeOrder->id, $fakeUser);

        //* assert
        $this->assertEquals($fakeOrder->id, $order->id);
        $this->assertEquals($fakeOrder->total_all_price, $order->total_all_price);
    }

    function test_get_orders() : void {
        //* params
        $fakeOrder = Order::factory(10)->create();

        //* auth
        $fakeUser = User::factory()->create();

        //* action
        $orderService = new OrderService(
            app()->make(ItemRepositoryInterface::class),
            app()->make(OrderRepositoryInterface::class),
        );
        $orders = $orderService->getOrders($fakeUser);

        //* assert
        $this->assertCount(10, $orders);
    }
}
