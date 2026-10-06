<?php



namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;




class ReportService
{
    public function sales(Request $request): array
    {
        $orders = Order::query()
            ->where('status', OrderStatus::COMPLETED->value);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $orders->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('seller_id')) {
            $orders->where('created_by', $request->seller_id);
        }

        if ($request->filled('customer_id')) {
            $orders->where('customer_id', $request->customer_id);
        }

        $total_sales = $orders->sum('total_amount');
        $completed_orders = $orders->count();
        $average_order_value = $completed_orders > 0
            ? $total_sales / $completed_orders
            : 0;


        return [
            'total_sales' => $total_sales,
            'completed_orders' => $completed_orders,
            'average_order_value' => $average_order_value,

        ];
    }

    public function orders(Request $request): array
    {
        $orders = Order::query();

        // Date filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $orders->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Seller filter
        if ($request->filled('seller_id')) {
            $orders->where('created_by', $request->seller_id);
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $orders->where('customer_id', $request->customer_id);
        }

        // Optional status filter
        if ($request->filled('status')) {
            $orders->where('status', $request->status);
        }

        $totalOrders = (clone $orders)->count();

        $pendingOrders = (clone $orders)
            ->where('status', OrderStatus::PENDING->value)
            ->count();

        $confirmedOrders = (clone $orders)
            ->where('status', OrderStatus::CONFIRMED->value)
            ->count();

        $completedOrders = (clone $orders)
            ->where('status', OrderStatus::COMPLETED->value)
            ->count();

        $cancelledOrders = (clone $orders)
            ->where('status', OrderStatus::CANCELLED->value)
            ->count();

        $completionRate = $totalOrders > 0
            ? round(($completedOrders / $totalOrders) * 100, 2)
            : 0;

        $cancelRate = $totalOrders > 0
            ? round(($cancelledOrders / $totalOrders) * 100, 2)
            : 0;

        return [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'confirmed_orders' => $confirmedOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'completion_rate' => $completionRate,
            'cancel_rate' => $cancelRate,
        ];
    }

    public function inventory(Request $request): array
    {
        $products = Product::query();

        // Product filter
        if ($request->filled('product_id')) {
            $products->where('id', $request->product_id);
        }

        $totalProducts = (clone $products)->count();

        $totalStock = (clone $products)->sum('quantity');

        $lowStockProducts = (clone $products)
            ->whereBetween('quantity', [1, 10])
            ->count();

        $outOfStockProducts = (clone $products)
            ->where('quantity', 0)
            ->count();


        // Inventory history
        $histories = InventoryHistory::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            $histories->whereBetween('created_at', [
                $startDate,
                $endDate
            ]);
        }

        if ($request->filled('product_id')) {
            $histories->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $histories->where('type', $request->type);
        }

        $stockIn = (clone $histories)
            ->where('quantity_change', '>', 0)
            ->sum('quantity_change');

        $stockOut = abs(
            (clone $histories)
                ->where('quantity_change', '<', 0)
                ->sum('quantity_change')
        );

        return [
            'total_products' => $totalProducts,
            'total_stock' => $totalStock,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'stock_in' => $stockIn,
            'stock_out' => $stockOut,
        ];
    }

    public function products(Request $request)
    {
        $query = OrderItem::query()
            ->selectRaw('
            product_id,
            SUM(order_items.quantity) as units_sold,
            SUM(order_items.subtotal) as revenue
        ')
            ->whereHas('order', function ($q) use ($request) {

                $q->where(
                    'status',
                    OrderStatus::COMPLETED->value
                );

                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();

                    $q->whereBetween('created_at', [
                        $startDate,
                        $endDate
                    ]);
                }

                if ($request->filled('seller_id')) {
                    $q->where(
                        'created_by',
                        $request->seller_id
                    );
                }

                if ($request->filled('customer_id')) {
                    $q->where(
                        'customer_id',
                        $request->customer_id
                    );
                }
            });

        if ($request->filled('product_id')) {
            $query->where(
                'product_id',
                $request->product_id
            );
        }

        return $query
            ->groupBy('product_id')
            ->with([
                'product:id,name,sku,quantity'
            ])
            ->orderByDesc('revenue')
            ->paginate(10);
    }
}
