<?php

namespace App\Actions\Dashboards;

use Illuminate\Support\Facades\DB;

class GetTopProducts
{
    public function handle(): array
    {
        return DB::table('product_stats')
            ->join('products', 'product_stats.product_id', '=', 'products.id')
            ->select([
                'products.name',
                'products.id',
                'product_stats.total_revenue',
                'product_stats.total_sold_in_units',
                'product_stats.last_sold_at',
            ])
            ->where('products.is_published', true)
            ->orderBy('product_stats.total_revenue', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }
}
