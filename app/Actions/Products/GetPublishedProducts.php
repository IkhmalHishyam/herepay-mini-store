<?php

namespace App\Actions\Products;

use Illuminate\Http\Request;
use App\Models\Products\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetPublishedProducts
{
    public function handle(Request $request): LengthAwarePaginator
    {
        $search          = $request->input('search');
        $per_page        = $request->input('per_page', 12);
        $order_by        = $request->input('order_by');
        $order_direction = $request->input('order_direction');
        $category_ids    = $request->input('category_ids', []);
        $tag_ids         = $request->input('tag_ids', []);
        $min_price       = $request->input('min_price');
        $max_price       = $request->input('max_price');
        $stock_status    = $request->input('stock_status');

        $products = Product::query()
            ->with([
                'productThumbnail',
                'productImages',
                'productCategories',
                'productTags',
                'variantGroups.variants',
                'skus'
            ])
            ->searchable($search)
            ->published()
            ->byCategories($category_ids)
            ->byTags($tag_ids)
            ->priceRange($min_price, $max_price)
            ->when($order_by && $order_direction, function ($query) use ($order_by, $order_direction) 
            {
                if ($order_by === 'price_asc') 
                {
                    $query->orderBy('price', 'ASC');
                } 
                elseif ($order_by === 'price_desc') 
                {
                    $query->orderBy('price', 'DESC');
                } 
                elseif ($order_by === 'name_asc') 
                {
                    $query->orderBy('name', 'ASC');
                } 
                elseif ($order_by === 'name_desc') 
                {
                    $query->orderBy('name', 'DESC');
                } 
                elseif ($order_by === 'newest') 
                {
                    $query->orderBy('created_at', 'DESC');
                } 
                elseif ($order_by === 'oldest') 
                {
                    $query->orderBy('created_at', 'ASC');
                } 
                else 
                {
                    $query->orderBy($order_by, $order_direction);
                }
            }, function ($query) {
                $query->orderBy('created_at', 'DESC')
                    ->orderBy('is_published', 'DESC');
            })
            ->paginate($per_page);

        $products->getCollection()->transform(function ($product) 
        {
            $totalStock = $product->skus->sum('total_stock');
            
            $product->total_stock  = $totalStock;
            $product->is_in_stock  = $totalStock > 0;
            $product->stock_status = $this->getStockStatus($totalStock);
            
            return $product;
        });

        if ($stock_status) 
        {
            $filteredProducts = $products->getCollection()->filter(function ($product) use ($stock_status) 
            {
                return $product->stock_status === $stock_status;
            });

            $products->setCollection($filteredProducts);
        }

        return $products;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function getStockStatus(int $totalStock): string
    {
        if ($totalStock === 0) 
        {
            return 'out_of_stock';
        } 
        elseif ($totalStock <= 10) 
        {
            return 'low_stock';
        } 
        else 
        {
            return 'in_stock';
        }
    }
}