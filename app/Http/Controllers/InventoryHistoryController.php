<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryHistoryResource;
use App\Services\InventoryHistoryService;
use Illuminate\Http\Request;

class InventoryHistoryController extends Controller
{
    public function __construct(
        protected readonly InventoryHistoryService $inventoryService) 
        {}

    public function index(Request $request)
    {
        $inventories = $this->inventoryService->index($request);
        return InventoryHistoryResource::collection($inventories);
    }
}
