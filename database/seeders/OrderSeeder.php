<?php

namespace Database\Seeders;

use App\Enums\OrderEnum;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderObject = (object)[
            "total_all_price" => 40000,
            "item" => (object)[
                "id" => Str::orderedUuid(),
                "name" => "item 1",
                "unit" => "pcs",
                "unit_price" => 20000,
                "qty" => 2,
                "subtotal_price" => 40000
            ],
            "payment" => (object)[
                "payer_name" => fake()->name(),
                "paid_amount" => 100000,
                "change_amount" => 60000,
                "payment_type" => OrderEnum::PAYMENT_TYPE_CASH,
            ],
        ];

        //* store item
        $item = Item::factory()->create([
            'name' => $orderObject->item->name,
            'unit' => $orderObject->item->unit,
            'unit_price' => $orderObject->item->unit_price,
        ]);

        //* store order
        $storedOrder = Order::create([
            'id' => Str::orderedUuid(),
            'order_code' => Str::orderedUuid(),
            'total_all_price' => $orderObject->total_all_price,
        ]);

        //* store order item
        $storedOrderitem = OrderItem::create([
            'id' => Str::orderedUuid(),
            'item_id' => $item->id,
            'order_id' => $storedOrder->id,
            'name' => $orderObject->item->name,
            'unit' => $orderObject->item->unit,
            'unit_price' => $orderObject->item->unit_price,
            'qty' => $orderObject->item->qty,
            'subtotal_price' => $orderObject->item->subtotal_price,
        ]);

        //* store order payment
        $storedOrderPayment = OrderPayment::create([
            'id' => Str::orderedUuid(),
            'order_id' => $storedOrder->id,
            'payer_name' => $orderObject->payment->payer_name,
            'paid_amount' => $orderObject->payment->paid_amount,
            'change_amount' => $orderObject->payment->change_amount,
            'payment_type' => $orderObject->payment->payment_type,
        ]);
    }
}
