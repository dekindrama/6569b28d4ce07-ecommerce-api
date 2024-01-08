<?php

namespace App\Domains\Order;

use App\Domains\Order\Entities\CheckTotalAllPriceIsEqualEntity;
use App\Domains\Order\Entities\CheckTotalPriceIsEqualEntity;
use App\Domains\Order\Entities\StoreOrderEntity;
use App\Domains\Order\Entities\StoreOrderItemEntity;
use App\Domains\Order\Entities\StoreOrderPaymentEntity;
use App\Exceptions\Commons\BadRequestException;
use App\Exceptions\Commons\NotFoundException;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class OrderRepository implements OrderRepositoryInterface
{
    private Order $_orderModel;
    private OrderItem $_orderItemModel;
    private Item $_itemModel;
    private OrderPayment $_orderPaymentModel;
    public function __construct(Order $orderModel, OrderItem $orderItemModel, Item $itemModel, OrderPayment $orderPaymentModel) {
        $this->_orderModel = $orderModel;
        $this->_orderItemModel = $orderItemModel;
        $this->_itemModel = $itemModel;
        $this->_orderPaymentModel = $orderPaymentModel;
    }

    function checkTotalPriceIsEqual(CheckTotalPriceIsEqualEntity $params): void
    {
        //* find item
        $item = $this->_itemModel->find($params->id);
        if (!$item) {
            throw new NotFoundException('item not found');
        }

        $itemSubtotalPrice = $item->unit_price * $params->qty;

        if ($itemSubtotalPrice != $params->subtotal_price) {
            throw new BadRequestException('subtotal price item is not equal');
        }
    }

    function checkTotalAllPriceIsEqual(CheckTotalAllPriceIsEqualEntity $params): void
    {
        //* find item
        $item = $this->_itemModel->find($params->id);
        if (!$item) {
            throw new NotFoundException('item not found');
        }

        $itemSubtotalPrice = $item->unit_price * $params->qty;
        if ($itemSubtotalPrice != $params->total_all_price) {
            throw new BadRequestException('total all price is not equal');
        }
    }

    function storeOrder(StoreOrderEntity $params): Order
    {
        $storedOrder = $this->_orderModel->create([
            'id' => Str::orderedUuid(),
            'order_code' => Str::orderedUuid(),
            'total_all_price' => $params->total_all_price,
        ]);

        return $storedOrder;
    }

    function storeOrderItem(StoreOrderItemEntity $params): OrderItem
    {
        $storedOrderitem = $this->_orderItemModel->create([
            'id' => Str::orderedUuid(),
            'item_id' => $params->item_id,
            'order_id' => $params->order_id,
            'name' => $params->name,
            'unit' => $params->unit,
            'unit_price' => $params->unit_price,
            'qty' => $params->qty,
            'subtotal_price' => $params->subtotal_price,
        ]);

        return $storedOrderitem;
    }

    function storeOrderPayment(StoreOrderPaymentEntity $params): OrderPayment
    {
        $storedOrderPayment = $this->_orderPaymentModel->create([
            'id' => Str::orderedUuid(),
            'order_id' => $params->order_id,
            'payer_name' => $params->payer_name,
            'paid_amount' => $params->paid_amount,
            'change_amount' => $params->change_amount,
            'payment_type' => $params->payment_type,
        ]);

        return $storedOrderPayment;
    }

    function getOrder(string $orderId): Order
    {
        $order = Order::query()
            ->where('id', $orderId)
            ->with(['item', 'payment'])
            ->first();
        if (!$order) {
            throw new NotFoundException('order not found');
        }

        return $order;
    }

    function getOrders(): Collection
    {
        $orders = Order::all();
        return $orders;
    }
}
