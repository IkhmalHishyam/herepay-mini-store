<?php

namespace App\Http\Resources\Customers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @see \App\Models\Customers\CustomerStat
 */
class CustomerStatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'total_orders'        => $this->total_orders,
            'total_spent'         => $this->total_spent,
            'average_order_value' => $this->average_order_value,
            'last_order_date'     => $this->last_order_date,
        ];
    }
}