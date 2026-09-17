<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $stockStatus = match (true) {
            
            $this->quantity === 0 => 'out_of_stock',
            $this->quantity <= 10 => 'low_stock',
            $this->quantity > 10 => 'in_stock'
        };
        return [
            'id' => $this->id,
            'name' => $this->name,
            'stock' => $this->quantity,
            'sku' => $this->sku,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name
            ],
            'stock_status' => $stockStatus
        ];
    }
}
