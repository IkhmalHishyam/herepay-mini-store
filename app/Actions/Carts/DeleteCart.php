<?php

namespace App\Actions\Carts;

use App\Models\Carts\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteCart
{
    public function handle(): array
    {
        $cart = Cart::where('user_id', Auth::user()->id)->first();

        if (!$cart) 
        {
            return [
                'success'      => true,
                'message'      => 'No cart found to delete.',
                'cart_deleted' => false
            ];
        }

        $itemsCount = $cart->cartProducts()->count();

        $cart->delete();

        return [
            'success' => true,
            'message' => $itemsCount > 0 
                ? "Cart deleted with {$itemsCount} items." 
                : "Empty cart deleted.",
            'cart_deleted' => true,
            'items_count'  => $itemsCount
        ];
    }

    public function forceDelete(): array
    {
        $cart = Cart::where('user_id', Auth::user()->id)->first();

        if (!$cart) 
        {
            return [
                'success'      => true,
                'message'      => 'No cart found to delete.',
                'cart_deleted' => false
            ];
        }

        $itemsCount = $cart->cartProducts()->count();

        $cart->forceDelete();

        return [
            'success' => true,
            'message' => $itemsCount > 0 
                ? "Cart permanently deleted with {$itemsCount} items." 
                : "Empty cart permanently deleted.",
            'cart_deleted' => true,
            'items_count'  => $itemsCount
        ];
    }
}