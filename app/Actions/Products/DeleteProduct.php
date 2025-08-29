<?php

namespace App\Actions\Products;

use App\Models\Products\Product;
use App\Services\FileManager\FileManager;

class DeleteProduct
{
    public function handle(string $product_id): void
    {
        $product = Product::findOrFail($product_id);

        if ($product->files()->exists()) 
        {
            $file_ids = $product->files()->pluck('id')->toArray();

            $fileManager = new FileManager;
            $fileManager->bulkDelete($file_ids);
        }

        $product->forceDelete();
    }
}