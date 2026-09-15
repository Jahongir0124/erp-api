<?php




namespace app\Services;


use App\DTOs\OrderData;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;



class OrderService
{


    public function index()
    {

        $query = Order::query()
            ->with([
                "customer:id,name",
                "creator:id,name"
            ]);
        if (!auth()->user()->hasAnyRole(['super-admin', 'manager']))
            {
                $query->where('created_by', auth()->id());
            }
        
        return $query->latest()->get();
    }
    public function storeOrder(OrderData $dto): Order
    {
        $productIds = collect($dto->items)
            ->pluck('product_id');

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $totalAmount = 0;

        foreach ($dto->items as $item) {
            $product = $products->get($item['product_id']);
            
            $subtotal = $product->price * $item['quantity'];

            $totalAmount += $subtotal;
        }

        $order = DB::transaction(function () use (
            $dto,
            $products,
            $totalAmount
        )
        {
            $order = Order::create([
                'customer_id' => $dto->customerId,
                'created_by' => auth()->id(),
                'total_amount' => $totalAmount,
                'status' => OrderStatus::PENDING
            ]);

            $order->order_number = 'ORD-'. str_pad(
                $order->id,
                6,
                '0',
                STR_PAD_LEFT
            );
            $order->save();


            foreach ($dto->items as $item)
                {
                    $product = $products->get($item['product_id']);
                    $quantity = $item['quantity'];
                    $subtotal = $product->price * $quantity;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'subtotal' => $subtotal
                    ]);
                }
            return $order;
        }
        
        );
        return $order->load('items.product');
    }

    public function update(Order $order, OrderData $data)
    {
        $productIds = collect($data->items)->pluck('product_id');
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        $totalAmount = 0;

        foreach($data->items as $item)
            {
                $product = $products->get($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;
            }

        

        $order = DB::transaction(function () use ($data, $order, $totalAmount, $products) {

            $order->update([
                "customer_id" => $data->customerId,
                "total_amount" => $totalAmount,
            ]);

            $order->items()->delete();
            
            foreach ($data->items as $item)
                {
                    $product = $products->get($item['product_id']);
                    $subtotal = $product->price * $item['quantity'];

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'subtotal' => $subtotal
                    ]);

                }
            return $order;

        });

        return $order->load('items.product');

        
    }


    public function confirm(Order $order): Order
    {
        return DB::transaction(function () use ($order) {

            $order->load('items.product');

            foreach($order->items as $item)
                {
                    $product = $item->product;

                    if ($product->quantity < $item->quantity)
                        {
                            throw ValidationException::withMessages([
                                'product' => "Not enough stock {$product->name}"
                            ]);
                        }
                }

            foreach($order->items as $item)
                {
                    $product = $item->product;

                    $product->decrement('quantity', $item->quantity);
                }

            $order->update([
                'status' => OrderStatus::CONFIRMED,
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now()
            ]);

            return $order;
        });
    }
}