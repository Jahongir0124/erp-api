<?php




namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;


class DashboardService
{
  
    public function index(): array
    {
        $orders = Order::query();
        $ordersTotal = $orders->count();
        $products = Product::query();

        $ordersPending = Order::query()->where(
            'status', OrderStatus::PENDING->value
        )->count();

        $ordersConfirmed = Order::query()->where(
            'status', OrderStatus::CONFIRMED->value
        )->count();

        $ordersCompleted = Order::query()->where(
            'status', OrderStatus::COMPLETED->value
        )->count();

        $ordersCancelled = Order::query()->where(
            'status', OrderStatus::CANCELLED->value
        )->count();

        $productsTotal = Product::query()->count();

        $stockTotal = Product::query()->sum('quantity');

        $lowStockCount = Product::query()
            ->whereBetween('quantity', [1,10])->count();

        $outStockCount = Product::query()
            ->where('quantity', 0)->count();

        $salesTotal = Order::query()
            ->where('status', OrderStatus::COMPLETED->value)
            ->sum('total_amount');

        return [
            'orders' => [
                'total' => $ordersTotal,
                'pending' => $ordersPending,
                'confirmed' => $ordersConfirmed,
                'completed' => $ordersCompleted,
                'cancelled' => $ordersCancelled
            ],

            'products' => [
                'total' => $productsTotal
            ],

            'inventory' => [
                'stock_total' => $stockTotal,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outStockCount,
            ],

            'sales' => [
                'total' => $salesTotal
            ]
        ];
    }
}