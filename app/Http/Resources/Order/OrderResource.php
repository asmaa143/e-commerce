<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "status"=>$this->status,
            "reference"=>$this->reference,
            "total"=>$this->total,
            "product"=> OrderItem::collection($this->products),
            "user"=>new UserResource($this->user),
        ];
    }
}
