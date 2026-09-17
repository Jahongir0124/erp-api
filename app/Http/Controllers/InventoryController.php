<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\InventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Override;

class InventoryController extends Controller implements HasMiddleware
{

    #[Override]
    public static function middleware()
    {
        new Middleware(
            'permisson:view-product',
            only: ['index']
        );
    }
   

    public function __construct(protected readonly InventoryService $inventoryService)
    {}
    public function index(InventoryRequest $request)
    {
        $inventories = $this->inventoryService->index($request);
        return InventoryResource::collection($inventories);
    }
}
