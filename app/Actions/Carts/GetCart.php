<?php

namespace App\Actions\Carts;

use App\Models\Carts\Cart;
use Illuminate\Support\Facades\Auth;

class GetCart
{
    public function handle(): ?array
    {
        $cart = Cart::where('user_id', Auth::user()->id)
            ->with(['cartProducts' => function ($query) {
                $query->with(['productThumbnail', 'productImages']);
            }])
            ->first();

        if (!$cart || $cart->cartProducts->count() === 0) 
        {
            return null;
        }

        $cartItems = $cart->cartProducts->map(function ($product) 
        {
            return [
                'id'             => $product->id,
                'name'           => $product->pivot->name,
                'sku_matrix'     => $product->pivot->sku,
                'price_per_unit' => $product->pivot->price_per_unit,
                'quantity'       => $product->pivot->quantity,
                'total_price'    => $product->pivot->total_price,
                'product' => [
                    'id'        => $product->id,
                    'name'      => $product->name,
                    'thumbnail' => $product->productThumbnail?->file_url,
                    'images'    => $product->productImages->map(fn($img) => $img->file_url)->toArray(),
                ],
                'added_at'   => $product->pivot->created_at,
                'updated_at' => $product->pivot->updated_at,
            ];
        });

        return [
            'id'          => $cart->id,
            'items'       => $cartItems,
            'items_count' => $cartItems->count(),
            'total_price' => $cart->total_price,
            'created_at'  => $cart->created_at,
            'updated_at'  => $cart->updated_at,
        ];
    }

    public function getCartSummary(): array
    {
        $cart = Cart::where('user_id', Auth::user()->id)->first();

        if (!$cart) 
        {
            return [
                'items_count' => 0,
                'total_price' => 0,
                'has_items'   => false
            ];
        }

        $itemsCount = $cart->cartProducts()->count();

        return [
            'items_count' => $itemsCount,
            'total_price' => $cart->total_price,
            'has_items'   => $itemsCount > 0
        ];
    }
}