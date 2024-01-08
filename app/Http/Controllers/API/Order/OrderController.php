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
use App\Services\Auth\AuthServiceInterface;
use App\Services\Order\OrderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function getListOrders(AuthServiceInterface $authService, OrderServiceInterface $orderService): Response
    {
        try {
            $loggedUser = $authService->getLoggedUser();
            $orders = $orderService->getOrders($loggedUser);

            //* return response
            return ResponseHelper::generate(
                true,
                'success get list orders',
                Response::HTTP_OK,
                [
                    'orders' => ListOrdersResource::collection($orders),
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function generateReciept($order_id, AuthServiceInterface $authService, OrderServiceInterface $orderService): Response
    {
        try {
            $loggedUser = $authService->getLoggedUser();
            $order = $orderService->getOrder($order_id, $loggedUser);

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

    public function getDetailOrder($order_id, AuthServiceInterface $authService, OrderServiceInterface $orderService): Response
    {
        try {
            $loggedUser = $authService->getLoggedUser();
            $order = $orderService->getOrder($order_id, $loggedUser);

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

    public function storeOrder(StoreOrderRequest $request, AuthServiceInterface $authService, OrderServiceInterface $orderService): Response
    {
        try {
            DB::beginTransaction();

            $loggedUser = $authService->getLoggedUser();
            $storedOrder = $orderService->storeOrder($request, $loggedUser);

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
}
