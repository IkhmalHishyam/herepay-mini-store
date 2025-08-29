<?php

namespace App\Actions\Products;

use Illuminate\Http\Request;
use App\Models\Products\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class GetAllProducts
{
    public function handle(Request $request): LengthAwarePaginator
    {
        $search          = $request->input('search');
        $per_page        = $request->input('per_page', 10);
        $order_by        = $request->input('order_by');
        $order_direction = $request->input('order_direction');

        return Product::query()
            ->with([
                'productThumbnail',
                'productCategories',
                'variantGroups.variants',
                'skus'
            ])
            ->searchable($search)
            ->when($order_by && $order_direction, function ($query) use ($order_by, $order_direction) {
                $query->orderBy($order_by, $order_direction);
            }, function ($query) {
                $query->orderBy('created_at', 'DESC')
                    ->orderBy('is_published', 'DESC');
            })
            ->paginate($per_page);
    }
}