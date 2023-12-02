<?php

namespace App\Http\Resources\Item;

use App\Helpers\StorageHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "picture" => StorageHelper::getUrl($this->picture),
            "stock" => $this->stock,
            "status_stock" => $this->status_stock,
            "unit" => $this->unit,
            "unit_price" => $this->unit_price,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
    }
}
