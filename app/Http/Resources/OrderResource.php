<?php

namespace App\Http\Resources;

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
            "id" => $this->id,
            "customer" => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->name
            ],
            "created_by" => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name
            ],
            "confirmed_by" => $this->confirmer?->name,
            "cancelled_by" => $this->canceller?->name,
            "items" => OrderItemResource::collection($this->whenLoaded('items')),
            "order_number" => $this->order_number,
            "total_amount" => $this->total_amount,
            "status" => $this->status,
            "created_at" => $this->created_at->format('Y-m-d H:i'),
            "cancelled_at" => $this->cancelled_at?->format('Y-m-d H:i'),
            "confirmed_at" => $this->confirmed_at?->format('Y-m-d H:i'),
            "completed_at" => $this->completed_at?->format('Y-m-d H:i')
        ];
    }
}
