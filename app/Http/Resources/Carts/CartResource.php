<?php

namespace App\Http\Resources\Carts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @see \App\Models\Carts\Cart
 */
class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'total_price'   => $this->total_price,
            'cart_products' => $this->whenLoaded(CartProductResource::collection($this->cartProducts)->resolve()),
        ];
    }
}
