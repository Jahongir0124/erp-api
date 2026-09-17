<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product?->id,
                'name' => $this->product?->name,
                'sku' => $this->sku
            ],
            "created_by" => [
                "id" => $this->creator?->id,
                "name" => $this->creator?->name
            ],
            'order' => [
                "id" => $this->order?->id,
                "order_number" => $this->order?->order_number
            ],
            "note" => $this->note,
            'type' => $this->type,
            "quantity_before" => $this->quantity_before,
            "quantity_change" => $this->quantity_change,
            "quantity_after" => $this->quantity_after,
            "created_at" => $this->created_at?->format('Y-m-d H:i')
        ];
    }
}
