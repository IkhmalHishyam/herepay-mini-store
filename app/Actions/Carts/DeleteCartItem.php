<?php

namespace App\Actions\Carts;

use Exception;
use App\Models\Carts\Cart;
use Illuminate\Support\Facades\Auth;
use App\DTOs\Actions\DeleteCartItemDTO;

class DeleteCartItem
{
    public function handle(DeleteCartItemDTO $actionDTO): array
    {
        $cart = Cart::where('user_id', Auth::user()->id)->first();

        if (!$cart) 
        {
            throw new Exception('No cart found for user.');
        }

        $cartItem = $cart->cartProducts()
            ->where('product_id', $actionDTO->product_id)
            ->wherePivot('sku', $actionDTO->sku_matrix)
            ->first();

        if (!$cartItem) 
        {
            throw new Exception('Item not found in cart.');
        }

        $cart->cartProducts()
            ->where('product_id', $actionDTO->product_id)
            ->wherePivot('sku', $actionDTO->sku_matrix)
            ->detach();

        $this->recalculateCartTotal($cart);

        $remainingItems = $cart->cartProducts()->count();
        
        if ($remainingItems === 0) 
        {
            $cart->delete();
            
            return [
                'success'          => true,
                'message'          => 'Item removed from cart. Cart is now empty.',
                'cart_items_count' => 0,
                'cart_total'       => 0,
                'cart_deleted'     => true
            ];
        }

        return [
            'success'          => true,
            'message'          => 'Item removed from cart successfully.',
            'cart_items_count' => $remainingItems,
            'cart_total'       => $cart->total_price,
            'cart_deleted'     => false
        ];
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function recalculateCartTotal(Cart $cart): void
    {
        $total = $cart->cartProducts()->sum('cart_products.total_price');

        $cart->update([
            'total_price' => $total
        ]);
    }
}