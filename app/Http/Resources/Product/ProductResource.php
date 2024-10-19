<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var  $this */
        return [
            "id"=>(integer)$this->id,
            "name"=>$this->name,
            "price"=>(float)$this->price,
            "description"=>$this->description,
            "stock_quantity"=>(integer)$this->stock_quantity,

        ];
    }
}
