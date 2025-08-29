<?php

namespace App\Actions\Carts;

use App\DTOs\Actions\UpdateCartDTO;
use App\Models\Carts\Cart;
use App\Models\Products\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateCart
{
    public function handle(UpdateCartDTO $actionDTO): array
    {
        $new_quantity = $actionDTO->quantity;
        $cart         = Cart::where('user_id', Auth::user()->id)->first();

        if (!$cart) 
        {
            throw new Exception('No cart found for user.');
        }

        if ($new_quantity < 0) 
        {
            throw new Exception('Quantity cannot be negative.');
        }

        $product = Product::where('id', $actionDTO->product_id)
            ->where('is_published', true)
            ->firstOrFail();

        $cartItem = $cart->cartProducts()
            ->where('product_id', $actionDTO->product_id)
            ->wherePivot('sku', $actionDTO->sku_matrix)
            ->first();

        if (!$cartItem) 
        {
            throw new Exception('Item not found in cart.');
        }

        if ($new_quantity === 0) 
        {
            return $this->removeCartItem($cart, $actionDTO->product_id, $actionDTO->sku_matrix);
        }

        $available_stock = $this->getAvailableStock($product, $actionDTO->sku_matrix);

        if ($available_stock < $new_quantity) 
        {
            throw new Exception("Only {$available_stock} units available in stock.");
        }

        $new_total_price = $cartItem->pivot->price_per_unit * $new_quantity;

        // Update the pivot record directly
        DB::table('cart_products')
            ->where('cart_id', $cart->id)
            ->where('product_id', $actionDTO->product_id)
            ->where('sku', $actionDTO->sku_matrix)
            ->update([
                'quantity' => $new_quantity,
                'total_price' => $new_total_price,
                'updated_at' => now(),
            ]);

        $this->recalculateCartTotal($cart);

        return [
            'success'          => true,
            'message'          => 'Cart updated successfully',
            'cart_items_count' => $cart->cartProducts->count(),
            'cart_total'       => $cart->total_price
        ];
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function removeCartItem(Cart $cart, string $product_id, ?string $sku_matrix): array
    {
        $cart->cartProducts()
            ->where('product_id', $product_id)
            ->wherePivot('sku', $sku_matrix)
            ->detach();

        $this->recalculateCartTotal($cart);

        if ($cart->cartProducts->count() === 0) 
        {
            $cart->delete();
            
            return [
                'success'          => true,
                'message'          => 'Item removed from cart',
                'cart_items_count' => 0,
                'cart_total'       => 0
            ];
        }

        return [
            'success'          => true,
            'message'          => 'Item removed from cart',
            'cart_items_count' => $cart->cartProducts->count(),
            'cart_total'       => $cart->total_price
        ];
    }

    protected function getAvailableStock(Product $product, ?string $sku_matrix): int
    {
        if ($sku_matrix) 
        {
            $sku = $product->skus()
                ->where('matrix', $sku_matrix)
                ->first();

            return $sku ? $sku->total_stock : 0;
        }

        return $product->skus->sum('total_stock');
    }

    protected function recalculateCartTotal(Cart $cart): void
    {
        $total = $cart->cartProducts()->sum('cart_products.total_price');

        $cart->update([
            'total_price' => $total
        ]);
    }
}