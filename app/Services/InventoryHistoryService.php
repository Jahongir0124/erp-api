<?php




namespace app\Services;

use Illuminate\Http\Request;
use App\Models\InventoryHistory;



class InventoryHistoryService
{
    public function index(Request $request)
    {

        $query = InventoryHistory::query()
            ->with([
                'product:id,name,sku',
                'creator:id,name',
                'order:id,order_number'
            ]);
        if ($request->filled('search'))
            {
                $search = $request->search;
                $query->where(function ($q) use ($search){
                    $q->where('quantity_before', 'like', "%{$search}%")
                    ->orWhere('quantity_change', 'like', "%{$search}%")
                    ->orWhere('quantity_after', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search){
                        $q->where('name', 'like', "%{$search}%");
                    });
                });
            }

        if ($request->filled('type'))
            {
                $query->where('type', $request->type);
            }
        return $query->latest()->paginate(10);
    }
}