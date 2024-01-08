<?php

namespace Tests\Feature\Domains\Order;

use App\Domains\Order\Entities\CheckTotalAllPriceIsEqualEntity;
use App\Domains\Order\Entities\CheckTotalPriceIsEqualEntity;
use App\Domains\Order\Entities\StoreOrderEntity;
use App\Domains\Order\Entities\StoreOrderItemEntity;
use App\Domains\Order\Entities\StoreOrderPaymentEntity;
use App\Domains\Order\OrderRepository;
use App\Enums\OrderEnum;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_check_total_price_is_equal(): void
    {
        //* params
        $fakeItem = Item::factory()->create();

        //* action
        $orderRepository = new OrderRepository(new Order(), new OrderItem(), new Item(), new OrderPayment());
        $entity = new CheckTotalPriceIsEqualEntity($fakeItem->id, 2, $fakeItem->unit_price * 2);
        $orderRepository->checkTotalPriceIsEqual($entity);

        //* assert
        $this->expectNotToPerformAssertions();
    }

    public function test_check_total_all_price_is_equal(): void
    {
        //* params
        $fakeItem = Item::factory()->create();

        //* action
        $orderRepository = new OrderRepository(new Order(), new OrderItem(), new Item(), new OrderPayment());
        $entity = new CheckTotalAllPriceIsEqualEntity($fakeItem->id, 2, $fakeItem->unit_price * 2);
        $orderRepository->checkTotalAllPriceIsEqual($entity);

        //* assert
        $this->expectNotToPerformAssertions();
    }

    public function test_store_order() : void {
        //* action
        $orderRepository = new OrderRepository(new Order(), new OrderItem(), new Item(), new OrderPayment());
        $entity = new StoreOrderEntity(100000);
        $order = $orderRepository->storeOrder($entity);

        //* assert
        $this->assertDatabaseCount(Order::class, 1);
        $this->assertDatabaseHas(Order::class, [
            'id' => $order->id,
            'total_all_price' => 100000,
        ]);
    }

    public function test_store_order_item() : void {
        //* params
        $fakeItem = Item::factory()->create();
        $fakeOrder = Order::factory()->create();

        //* action
        $orderRepository = new OrderRepository(new Order(), new OrderItem(), new Item(), new OrderPayment());
        $entity = new StoreOrderItemEntity((object)[
            'item_id' => $fakeItem->id,
            'order_id' => $fakeOrder->id,
            'name' => $fakeItem->name,
            'unit' => $fakeItem->unit,
            'unit_price' => $fakeItem->unit_price,
            'qty' => 2,
            'subtotal_price' => $fakeItem->unit_price * 2,
        ]);
        $orderItem = $orderRepository->storeOrderItem($entity);

        //* assert
        $this->assertDatabaseCount(OrderItem::class, 1);
        $this->assertDatabaseHas(OrderItem::class, [
            'item_id' => $orderItem->item_id,
            'order_id' => $orderItem->order_id,
            'name' => $orderItem->name,
            'unit' => $orderItem->unit,
            'unit_price' => $orderItem->unit_price,
            'qty' => $orderItem->qty,
            'subtotal_price' => $orderItem->subtotal_price,
        ]);
    }

    public function test_store_order_payment() : void {
        //* params
        $fakeOrder = Order::factory()->create();

        //* action
        $orderRepository = new OrderRepository(new Order(), new OrderItem(), new Item(), new OrderPayment());
        $entity = new StoreOrderPaymentEntity((object)[
            'order_id' => $fakeOrder->id,
            'payer_name' => fake()->name,
            'paid_amount' => 100000,
            'change_amount' => 60000,
            'payment_type' => OrderEnum::PAYMENT_TYPE_CASH,
        ]);
        $orderItem = $orderRepository->storeOrderPayment($entity);

        //* assert
        $this->assertDatabaseCount(OrderPayment::class, 1);
        $this->assertDatabaseHas(OrderPayment::class, [
            'order_id' => $entity->order_id,
            'payer_name' => $entity->payer_name,
            'paid_amount' => $entity->paid_amount,
            'change_amount' => $entity->change_amount,
            'payment_type' => $entity->payment_type,
        ]);
    }

    function test_get_order() : void {
        //* params
        $fakeOrder = Order::factory()->create();

        //* action
        $orderRepository = new OrderRepository(new Order(), new OrderItem(), new Item(), new OrderPayment());
        $order = $orderRepository->getOrder($fakeOrder->id);

        //* assert
        $this->assertEquals($fakeOrder->id, $order->id);
        $this->assertEquals($fakeOrder->total_all_price, $order->total_all_price);
    }

    function test_get_orders() : void {
        //* params
        $fakeOrder = Order::factory(10)->create();

        //* action
        $orderRepository = new OrderRepository(new Order(), new OrderItem(), new Item(), new OrderPayment());
        $orders = $orderRepository->getOrders();

        //* assert
        $this->assertCount(10, $orders);
    }
}
