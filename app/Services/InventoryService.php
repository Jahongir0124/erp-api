<?php





namespace app\Services;

use App\Http\Requests\Inventory\InventoryRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class InventoryService
{
    public function index(InventoryRequest $request)
    {
        $query = Product::query()
            ->with([
                'category:id,name'
            ]);


        if ($request->filled('search'))
            {
                $search = $request->search;
                $query->where(function ($q) use ($search){
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            }

        if ($request->filled('category_id'))
            {
                $query->where('category_id', $request->category_id);
            }

        if ($request->filled('stock_status'))
            {

                if ($request->stock_status === 'out_of_stock')
                    {
                        $query->where('quantity', 0);
                    }

                elseif ($request->stock_status === 'low_stock')
                    {
                        $query->whereBetween('quantity', [1, 10]);
                    }

                else 
                    {
                        $query->where('quantity', '>', 10);
                    }


            }

        return $query->latest()->paginate(10);
    }


    public function adjust(Product $product, array $data)
    {
       $quantity = $data['quantity'];
       $reason = $data['reason'];


       return DB::transaction(function () use ($product, $quantity, $reason) {

            $product = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();


            $quantityBefore = $product->quantity;
            $quantityAfter = $quantityBefore + $quantity;

            if ($quantityAfter < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stock quantity cannot be negative'
                ]);
            }


            $product->update([
                'quantity' => $quantityAfter
            ]);


            $product->inventoryHistories()->create([
                'order_id' => null,
                'created_by' => auth()->id(),
                'quantity_before' => $quantityBefore,
                'quantity_change' => $quantity,
                'quantity_after' => $quantityAfter,
                'type' => 'manual_adjustment',
                'note' => $reason
            ]);


            return $product->fresh();
       });


       
    }
}