<?php

namespace App\Services\Order;

use App\Domains\Item\ItemRepositoryInterface;
use App\Domains\Items\Entities\CheckItemIsExistEntity;
use App\Domains\Order\Entities\CheckTotalAllPriceIsEqualEntity;
use App\Domains\Order\Entities\CheckTotalPriceIsEqualEntity;
use App\Domains\Order\Entities\StoreOrderEntity;
use App\Domains\Order\Entities\StoreOrderItemEntity;
use App\Domains\Order\Entities\StoreOrderPaymentEntity;
use App\Domains\Order\OrderRepositoryInterface;
use App\Enums\UserRoleEnum;
use App\Exceptions\Commons\UnauthorizedException;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;

class OrderService implements OrderServiceInterface
{
    private ItemRepositoryInterface|MockInterface $_itemRepository;
    private OrderRepositoryInterface|MockInterface $_orderRepository;
    public function __construct(
        ItemRepositoryInterface|MockInterface $itemRepository,
        OrderRepositoryInterface|MockInterface $orderRepository,
    ) {
        $this->_itemRepository = $itemRepository;
        $this->_orderRepository = $orderRepository;
    }

    function storeOrder(StoreOrderRequest $validatedRequest, User $loggedUser): Order
    {
        //* params
        $requestItem = (object)$validatedRequest->item;
        $requestPayment = (object)$validatedRequest->payment;

        //* check item is exist
        $checkItemIsExistEntity = new CheckItemIsExistEntity(
            $requestItem->id,
            $requestItem->name,
            $requestItem->unit,
            $requestItem->unit_price,
        );
        $this->_itemRepository->checkItemIsExist($checkItemIsExistEntity);

        //* validate price
        $checkTotalPriceIsEqualEntity = new CheckTotalPriceIsEqualEntity(
            $requestItem->id,
            $requestItem->qty,
            $requestItem->subtotal_price,
        );
        $this->_orderRepository->checkTotalPriceIsEqual($checkTotalPriceIsEqualEntity);
        $checkTotalAllPriceIsEqualEntity = new CheckTotalAllPriceIsEqualEntity(
            $requestItem->id,
            $requestItem->qty,
            $validatedRequest->total_all_price,
        );
        $this->_orderRepository->checkTotalAllPriceIsEqual($checkTotalAllPriceIsEqualEntity);

        //* check logged user super admin/admin
        $this->_checkLoggedUserIsauthorized($loggedUser);

        //* store order
        $storeOrderEntity = new StoreOrderEntity($validatedRequest->total_all_price);
        $storedOrder = $this->_orderRepository->storeOrder($storeOrderEntity);

        //* store order item
        $storeOrderItemEntity = new StoreOrderItemEntity((object)[
            'item_id' => $requestItem->id,
            'order_id' => $storedOrder->id,
            'name' => $requestItem->name,
            'unit' => $requestItem->unit,
            'unit_price' => $requestItem->unit_price,
            'qty' => $requestItem->qty,
            'subtotal_price' => $requestItem->subtotal_price,
        ]);
        $storedOrderItem = $this->_orderRepository->storeOrderItem($storeOrderItemEntity);

        //* substract stock item
        $this->_itemRepository->substractItemStock($requestItem->id, $requestItem->qty);

        //* store order payment
        $storeOrderPaymentEntity = new StoreOrderPaymentEntity((object)[
            'order_id' => $storedOrder->id,
            'payer_name' => $requestPayment->payer_name,
            'paid_amount' => $requestPayment->paid_amount,
            'change_amount' => $requestPayment->change_amount,
            'payment_type' => $requestPayment->payment_type,
        ]);
        $storedOrderPayment = $this->_orderRepository->storeOrderPayment($storeOrderPaymentEntity);

        //* return order
        return $storedOrder;
    }

    private function _checkLoggedUserIsauthorized(User $loggedUser) : void {
        $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
        if ($isNotAuthorized) {
            throw new UnauthorizedException('action is unauthorized');
        }
    }

    function getOrder(string $orderId, User $loggedUser): Order
    {
        //* check logged user super admin/admin
        $this->_checkLoggedUserIsauthorized($loggedUser);

        //* get order
        $order = $this->_orderRepository->getOrder($orderId);

        //* return data
        return $order;
    }

    function getOrders(User $loggedUser): Collection
    {
        //* check logged user super admin/admin
        $this->_checkLoggedUserIsauthorized($loggedUser);

        //* get orders
        $orders = $this->_orderRepository->getOrders();

        //* return data
        return $orders;
    }
}
