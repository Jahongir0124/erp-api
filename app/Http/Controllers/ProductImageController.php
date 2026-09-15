<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProductImage\StoreProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductImageService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Override;

class ProductImageController extends Controller implements HasMiddleware
{
    
    public function __construct(
        protected readonly ProductImageService $productImageService
        ){}

    #[Override]
    public static function middleware()
    {
        return [
            new Middleware(
                'permission:create-product',
                only: ['store']
            )
        ];
    }


    public function store(
        Product $product,
        StoreProductImageRequest $request
    )
    {
        $image = $this->productImageService->store(
            $product,
            $request->file('image')
        );

        return new ProductImageResource($image);
    }

    public function destroy(
        Product $product,
        ProductImage $image
    ){
        $this->productImageService->destroy($product, $image);
        return response()->json([
            'message' => 'Product image deleted successfully'
        ]);
    }


    public function setPrimary(
        Product $product,
        ProductImage $image
    )
    {
        return new ProductImageResource(
            $this->productImageService->setPrimary($product, $image)
        );
    }
}
