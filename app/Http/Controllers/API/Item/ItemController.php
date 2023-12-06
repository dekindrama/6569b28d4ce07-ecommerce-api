<?php

namespace App\Http\Controllers\API\Item;

use App\Enums\UserRoleEnum;
use App\Exceptions\Commons\CommonException;
use App\Exceptions\Commons\NotFoundException;
use App\Exceptions\Commons\UnauthorizedException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Http\Resources\Item\ItemDetailsResource;
use App\Http\Resources\Item\ListItemsResource;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function storeItem(StoreItemRequest $request) : Response {
        try {
            //* check logged user is admin / super admin
            $loggedUser = auth('sanctum')->user();
            $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
            if ($isNotAuthorized) {
                throw new UnauthorizedException('action is unauthorized');
            }

            //* store image
            $storedPicture = Storage::disk('public')->put('pictures/items', $request->picture);

            //* store item
            $storedItem = Item::create([
                'id' => Str::orderedUuid(),
                'name' => $request->name,
                'picture' => $storedPicture,
                'stock' => $request->stock,
                'unit' => $request->unit,
                'unit_price' => $request->unit_price,
            ]);

            //* return response
            return ResponseHelper::generate(
                true,
                'success store item',
                Response::HTTP_CREATED,
                [
                    'item' => $storedItem,
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getListItems() : Response {
        try {
            //* get list item
            $items = Item::get();

            //* return response
            return ResponseHelper::generate(
                true,
                'success get list items',
                Response::HTTP_OK,
                [
                    'items' => ListItemsResource::collection($items),
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getDetailItem($item_id) : Response {
        try {
            //* get item
            $item = Item::find($item_id);
            if (!$item) {
                throw new NotFoundException('item not found');
            }

            //* return response
            return ResponseHelper::generate(
                true,
                'success get item',
                Response::HTTP_OK,
                [
                    'item' => new ItemDetailsResource($item),
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateItem(UpdateItemRequest $request, $item_id) : Response {
        try {
            //* check logged user is admin / super admin
            $loggedUser = auth('sanctum')->user();
            $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
            if ($isNotAuthorized) {
                throw new UnauthorizedException('action is unauthorized');
            }

            //* get item
            $item = Item::find($item_id);
            if (!$item) {
                throw new NotFoundException('item not found');
            }

            //* update image
            $updatedPicture = $item->picture;
            if ($request->picture) {
                //* delete old image
                if (Storage::disk('public')->exists($item->picture)) {
                    Storage::disk('public')->delete($item->picture);
                }
                //* store new image
                $updatedPicture = Storage::disk('public')->put('pictures/items', $request->picture);
            }

            //* update item
            $item->update([
                'name' => $request->name,
                'picture' => $updatedPicture,
                'stock' => $request->stock,
                'unit' => $request->unit,
                'unit_price' => $request->unit_price,
            ]);

            //* return response
            return ResponseHelper::generate(
                true,
                'success update item',
                Response::HTTP_OK,
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function softDeleteItem($item_id) : Response {
        try {
            //* check logged user is admin / super admin
            $loggedUser = auth('sanctum')->user();
            $isNotAuthorized = !(in_array($loggedUser->role, UserRoleEnum::ROLES));
            if ($isNotAuthorized) {
                throw new UnauthorizedException('action is unauthorized');
            }

            //* get item
            $item = Item::find($item_id);
            if (!$item) {
                throw new NotFoundException('item not found');
            }

            //* soft delete item
            $item->delete();

            //* return response
            return ResponseHelper::generate(
                true,
                'success delete item',
                Response::HTTP_OK,
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // public function destroyItem() : Response {

    // }
}
