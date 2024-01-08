<?php

namespace App\Http\Controllers\API\Order;

use App\Enums\UserRoleEnum;
use App\Exceptions\Commons\BadRequestException;
use App\Exceptions\Commons\CommonException;
use App\Exceptions\Commons\NotFoundException;
use App\Exceptions\Commons\UnauthorizedException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\Order\ListOrdersResource;
use App\Http\Resources\Order\OrderDetailsResource;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function getListOrders(): Response
    {
        try {
            //* check logged user super admin
            $loggedUser = auth('sanctum')->user();
            $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
            if ($isNotAuthorized) {
                throw new UnauthorizedException('action is unauthorized');
            }

            //* get order
            $order = Order::all();

            //* return response
            return ResponseHelper::generate(
                true,
                'success get list orders',
                Response::HTTP_OK,
                [
                    'order' => ListOrdersResource::collection($order),
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function generateReciept($order_id): Response
    {
        try {
            //* check logged user super admin
            $loggedUser = auth('sanctum')->user();
            $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
            if ($isNotAuthorized) {
                throw new UnauthorizedException('action is unauthorized');
            }

            //* get order
            $order = Order::query()
                ->where('id', $order_id)
                ->with(['item', 'payment'])
                ->first();
            if (!$order) {
                throw new NotFoundException('order not found');
            }

            //* return response
            return ResponseHelper::generate(
                true,
                'success generate reciept',
                Response::HTTP_OK,
                [
                    'order' => new OrderDetailsResource($order),
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getDetailOrder($order_id): Response
    {
        try {
            //* check logged user super admin
            $loggedUser = auth('sanctum')->user();
            $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
            if ($isNotAuthorized) {
                throw new UnauthorizedException('action is unauthorized');
            }

            //* get order
            $order = Order::query()
                ->where('id', $order_id)
                ->with(['item', 'payment'])
                ->first();
            if (!$order) {
                throw new NotFoundException('order not found');
            }

            //* return response
            return ResponseHelper::generate(
                true,
                'success get detail order',
                Response::HTTP_OK,
                [
                    'order' => new OrderDetailsResource($order),
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function storeOrder(StoreOrderRequest $request): Response
    {
        try {
            DB::beginTransaction();

            //* params
            $request = (object)($request->validated());
            $requestItem = (object)$request->item;
            $requestPayment = (object)$request->payment;

            //* check item is exist
            $item = Item::query()
                ->where('id', $requestItem->id)
                ->where('name', $requestItem->name)
                ->where('unit', $requestItem->unit)
                ->where('unit_price', $requestItem->unit_price)
                ->first();
            if (!$item) {
                throw new NotFoundException('item not found');
            }

            //* validate price
            $this->_checkTotalPriceIsEqual($requestItem);
            $this->_checkTotalAllPriceIsEqual($requestItem, $request->total_all_price);

            //* check logged user super admin
            $loggedUser = auth('sanctum')->user();
            $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
            if ($isNotAuthorized) {
                throw new UnauthorizedException('action is unauthorized');
            }

            //* store order
            $storedOrder = Order::create([
                'id' => Str::orderedUuid(),
                'order_code' => Str::orderedUuid(),
                'total_all_price' => $request->total_all_price,
            ]);

            //* store order item
            $storedOrderitem = OrderItem::create([
                'id' => Str::orderedUuid(),
                'item_id' => $requestItem->id,
                'order_id' => $storedOrder->id,
                'name' => $requestItem->name,
                'unit' => $requestItem->unit,
                'unit_price' => $requestItem->unit_price,
                'qty' => $requestItem->qty,
                'subtotal_price' => $requestItem->subtotal_price,
            ]);
            //* substract stock item
            $this->_substractItemStock($requestItem);

            //* store order payment
            $storedOrderPayment = OrderPayment::create([
                'id' => Str::orderedUuid(),
                'order_id' => $storedOrder->id,
                'payer_name' => $requestPayment->payer_name,
                'paid_amount' => $requestPayment->paid_amount,
                'change_amount' => $requestPayment->change_amount,
                'payment_type' => $requestPayment->payment_type,
            ]);

            DB::commit();

            //* return response
            return ResponseHelper::generate(
                true,
                'success store order',
                Response::HTTP_CREATED,
                [
                    'order' => new OrderDetailsResource($storedOrder),
                ],
            );
        } catch (CommonException $th) {
            DB::rollBack();
            return $th->renderResponse();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function _checkTotalPriceIsEqual($requestItem): void
    {
        //* find item
        $item = Item::find($requestItem->id);
        if (!$item) {
            throw new NotFoundException('item not found');
        }

        $itemSubtotalPrice = $item->unit_price * $requestItem->qty;

        if ($itemSubtotalPrice != $requestItem->subtotal_price) {
            throw new BadRequestException('subtotal price item is not equal');
        }
    }

    private function _checkTotalAllPriceIsEqual($requestItem, $totalAllPrice): void
    {
        //* find item
        $item = Item::find($requestItem->id);
        if (!$item) {
            throw new NotFoundException('item not found');
        }

        $itemSubtotalPrice = $item->unit_price * $requestItem->qty;
        if ($itemSubtotalPrice != $totalAllPrice) {
            throw new BadRequestException('total all price is not equal');
        }
    }

    private function _substractItemStock($requestItem): void
    {
        //* find item
        $item = Item::find($requestItem->id);
        if (!$item) {
            throw new NotFoundException('item not found');
        }
        //* substact item stock
        $item->decrement('stock', $requestItem->qty);
    }
}
