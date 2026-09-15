<?php


namespace app\Services;

use App\Models\Product;

use Illuminate\Support\Str;
class ProductService
{
   

    public function update(Product $product, array $data)
    {
        $product->update($data);
        return $product->fresh();
    }

    public function index(object $data)
    {
        $query = Product::query();

        if ($data->filled('search'))
            {
                $search = $data->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            }
        if ($data->filled('category'))
            {
                $query->whereHas('category', function ($q) use($data) {
                    $q->where('name', $data->category);
                });
            }


        return $query->latest()->with('images')->paginate(10);
    }

    public function store(array $data)
    {
        
        $product = Product::create($data);
        return $product;

    }


    public function destroy(Product $product): void
    {
        $product->delete();
    }
}