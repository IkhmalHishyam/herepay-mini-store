<?php

namespace App\Actions\Products;

use App\Models\Products\Product;

class GetProduct
{
    public function handle(string $product_id): Product
    {
        return Product::with([
            'productThumbnail',
            'productImages',
            'productCategories',
            'productTags',
            'variantGroups.variants',
            'productStat',
            'skus'
        ])->findOrFail($product_id);
    }
}