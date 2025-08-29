<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @see \App\Models\Products\ProductStat
 */
class ProductStatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'total_revenue'       => $this->total_revenue,
            'total_sold_in_units' => $this->total_sold_in_units,
            'last_sold_at'        => $this->last_sold_at,
        ];
    }
}
