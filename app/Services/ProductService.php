<?php


namespace app\Services;

use App\Http\Requests\User\UserRequest;
use App\Models\Product;

use Illuminate\Support\Str;
class ProductService
{
   

    public function update(Product $product, array $data)
    {
        $product->update($data);
        return $product->fresh();
    }

    public function index(UserRequest $request)
    {
        $query = Product::query();

        if ($request->filled('search'))
            {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
                });
            }
        if ($request->filled('category'))
            {
                $query->whereHas('category', function ($q) use($request) {
                    $q->where('name', $request->category);
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