<?php



namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function store(
        Product $product,
        UploadedFile $image
    ): ProductImage
    {
        $is_primary = !$product->images()->exists();

        $path = $image->store('products', 'public');

        return $product->images()->create([
            
            'image_path' => $path,
            'is_primary' => $is_primary,
            'sort_order' => $product->images()->count() + 1
        ]);
    }

    public function destroy(
        Product $product,
        ProductImage $image
    ): void
    {
        abort_unless(
            $product->id === $image->product->id,
            404
        );
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
    }


    public function setPrimary(
        Product $product,
        ProductImage $image
    ): ProductImage
    {
        abort_unless(
            $image->product->id === $product->id,
            404
        );


        return DB::transaction(function () use ($product, $image) {

            $product->images()->update([
                'is_primary' => false
            ]);

            $image->update([
                'is_primary' => true
            ]);

            return $image->fresh();
        });



    }
}