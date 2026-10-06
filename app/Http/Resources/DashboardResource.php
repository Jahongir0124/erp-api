<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'orders' => [
                'total' => $this['orders']['total'],
                'pending' => $this['orders']['pending'],
                'confirmed' => $this['orders']['confirmed'],
                'completed' => $this['orders']['completed'],
                'cancelled' => $this['orders']['cancelled']
            ],

            'products' => [
                'total' => $this['products']['total']
            ],

            'inventory' => [
                'stock_total' => $this['inventory']['stock_total'],
                'low_stock_count' => $this['inventory']['low_stock_count'],
                'out_of_stock_count' => $this['inventory']['out_of_stock_count']
            ],

            'sales' => [
                'total' => $this['sales']['total']
            ]
        ];
    }
}
