<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            "quantity" => $this->quantity,
            "price" => $this->price,
            "subtotal" => $this->subtotal,

            "product" => [
                'id' => $this->product?->id,
                "name" => $this->product?->name,
                "sku" => $this->product?->sku
            ]
        ];
    }
}
