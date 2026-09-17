<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Override;



class ProductController extends Controller implements HasMiddleware
{
    public function __construct(protected readonly ProductService $productService){}

    #[Override]
    public static function middleware(): array
    {
        return [
            new Middleware(
                'permission:view-product',
            ),
            new Middleware(
                'permission:create-product',
                only: ['store'] 
            ),
            new Middleware(
                'permission:update-product',
                only: ['update']
            ),
            new Middleware(
                'permission:delete-product',
                only: ['destroy']
            )
        ];
    }

    public function index(UserRequest $request)
    {
        return ProductResource::collection($this->productService->index($request));
    }

    public function show(Product $product)
    {
        $product->load('images');
        return new ProductResource($product);
    }

    public function store(ProductStoreRequest $request)
    {
        $product = $this->productService->store($request->validated());
        return new ProductResource($product);
    }

    public function update(
        ProductUpdateRequest $request,
        Product $product
    )
    {
        $this->productService->update($product, $request->validated());
        return response()->json([
            'msg' => 'Updated Succesfully'
        ]);
    }
    public function destroy(Product $product) 
    {
        $this->productService->destroy($product);
        return response()->json([
            'msg' => 'Deleted Successfully'
        ]);
    }   

}
