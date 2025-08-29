<?php

namespace App\Actions\Carts;

use App\Models\Carts\Cart;
use Illuminate\Support\Facades\Auth;

class ClearCart
{
    public function handle(): array
    {
        $cart = Cart::where('user_id', Auth::user()->id)->first();

        if (!$cart) 
        {
            return [
                'success'          => true,
                'message'          => 'No cart found to clear.',
                'cart_items_count' => 0,
                'cart_total'       => 0
            ];
        }

        $itemsCount = $cart->cartProducts()->count();

        if ($itemsCount === 0) 
        {
            return [
                'success'          => true,
                'message'          => 'Cart is already empty.',
                'cart_items_count' => 0,
                'cart_total'       => 0
            ];
        }

        $cart->cartProducts()->detach();

        $cart->update(['total_price' => 0]);

        return [
            'success'          => true,
            'message'          => "Cleared {$itemsCount} items from cart.",
            'cart_items_count' => 0,
            'cart_total'       => 0,
            'items_cleared'    => $itemsCount
        ];
    }

    public function handleAndDelete(): array
    {
        $cart = Cart::where('user_id', Auth::user()->id)->first();

        if (!$cart) 
        {
            return [
                'success'      => true,
                'message'      => 'No cart found to clear.',
                'cart_deleted' => false
            ];
        }

        $itemsCount = $cart->cartProducts()->count();

        $cart->delete();

        return [
            'success' => true,
            'message' => $itemsCount > 0 
                ? "Cart cleared and deleted. Removed {$itemsCount} items." 
                : "Empty cart deleted.",
            'cart_deleted'  => true,
            'items_cleared' => $itemsCount
        ];
    }
}