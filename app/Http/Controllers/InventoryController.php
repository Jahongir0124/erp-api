<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\InventoryAdjustmentRequest;
use App\Http\Requests\Inventory\InventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Product;
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

    public function adjust(Product $product, InventoryAdjustmentRequest $request)
    {
        $this->authorize('adjust', $product);
        $this->inventoryService->adjust($product, $request->validated());

        return response()->json([
            'msg' => 'success',
            201
        ]);
    }   
}
