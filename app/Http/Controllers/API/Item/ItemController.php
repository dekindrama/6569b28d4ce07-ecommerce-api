<?php

namespace App\Http\Controllers\API\Item;

use App\Http\Controllers\Controller;
use App\Http\Requests\Item\StoreItemRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function storeItem(StoreItemRequest $request) : Response {
        //* store image
        $image = Storage::disk('public')->put('image', $request->picture);

        //* store item

        //* return response
    }
}
