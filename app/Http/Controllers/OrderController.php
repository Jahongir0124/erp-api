<?php

namespace App\Http\Controllers;

use App\DTOs\OrderData;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Override;

class OrderController extends Controller implements HasMiddleware
{

    #[Override]
    public static function middleware(): array
    {
        return [
            new Middleware(
                'permission:create-order',
                only: ['store'],
            ),
            new Middleware(
                'permission:view-order',
                only: ['index', 'show']
            ),
            new Middleware(
                'permission:update-order',
                only: ['update']
            ),
            new Middleware(
                'permission:confirm-order',
                only: ['confirm']
            )
        ];
    }
    public function __construct(protected readonly OrderService $orderService) {}

    public function index()
    {
        $orders = $this->orderService->index();
        return OrderResource::collection($orders);
    }
    public function store(OrderStoreRequest $request)
    {
        $dto = OrderData::fromArray($request->validated());
        $order = $this->orderService->storeOrder($dto);
        return new OrderResource($order);
    }

    public function show(Order $order)
    {
        $order->load([
            'customer:id,name',
            "creator:id,name",
            "items.product:id,name,sku"
        ]);
        return new OrderResource($order);
    }

    public function update(
        Order $order,
        OrderUpdateRequest $request
    ) {
        $this->authorize('update', $order);
        $dto = OrderData::fromArray($request->validated());
        $updatedOrder = $this->orderService->update($order, $dto);
        return new OrderResource($updatedOrder);
    }

    public function confirm(Order $order)
    {
        $this->authorize('confirm', $order);

        $this->orderService->confirm($order);

        
    }
}
