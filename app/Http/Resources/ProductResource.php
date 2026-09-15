<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
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
            "category" => new CategoryResource($this->category),
            "sku" => $this->sku,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "description" => $this->description,
            "images" => ProductImageResource::collection($this->whenLoaded('images'))
            
        ];
    }
}
