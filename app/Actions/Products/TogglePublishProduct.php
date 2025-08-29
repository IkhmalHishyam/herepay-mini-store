<?php

namespace App\Actions\Products;

use App\Models\Products\Product;

class TogglePublishProduct
{
    public function handle(string $product_id, bool $is_published): void
    {
        $product = Product::findOrFail($product_id);

        $product->update([
            'is_published' => $is_published
        ]);
    }
}