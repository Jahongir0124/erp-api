<?php





namespace app\Services;

use App\Http\Requests\Inventory\InventoryRequest;
use App\Models\Product;
use Illuminate\Http\Request;


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
}