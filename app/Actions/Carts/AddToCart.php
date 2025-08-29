<?php

namespace App\Actions\Carts;

use App\DTOs\Actions\AddToCartDTO;
use App\Models\Carts\Cart;
use App\Models\Products\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class AddToCart
{
    public function handle(AddToCartDTO $actionDTO): array
    {
        $cart = Cart::firstOrCreate(
            ['user_id' => Auth::user()->id],
            ['total_price' => 0]
        );

        $product = Product::where('id', $actionDTO->product_id)
            ->where('is_published', true)
            ->firstOrFail();

        $sku             = null;
        $price           = $product->price;
        $sku_matrix      = null;
        $available_stock = 0;

        if ($actionDTO->sku_matrix) 
        {
            $sku = $product->skus()
                ->where('matrix', $actionDTO->sku_matrix
                )->first();
            
            if (!$sku)
            {
                throw new Exception("Selected product variant is not available.");
            }
            
            $price           = $sku->price;
            $sku_matrix      = $sku->matrix;
            $available_stock = $sku->total_stock;
        } 
        else 
        {
            $available_stock = $product->skus->sum('total_stock');
        }

        if ($available_stock < $actionDTO->quantity) 
        {
            throw new Exception("Only {$available_stock} units available in stock.");
        }

        $existingCartItem = $cart->cartProducts()
            ->where('product_id', $product->id)
            ->wherePivot('sku', $sku_matrix)
            ->first();

        if ($existingCartItem) 
        {
            $new_quantity = $existingCartItem->pivot->quantity + $actionDTO->quantity;
            
            if ($available_stock < $new_quantity) 
            {
                throw new Exception("Cannot add {$actionDTO->quantity} more units. Only {$available_stock} total available, you already have {$existingCartItem->pivot->quantity} in cart.");
            }

            $new_total_price = $price * $new_quantity;

            DB::table('cart_products')
                ->where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('sku', $sku_matrix)
                ->update([
                    'quantity' => $new_quantity,
                    'total_price' => $new_total_price,
                    'price_per_unit' => $price,
                    'updated_at' => now(),
                ]);

            $message = "Updated quantity in cart";
        } 
        else 
        {
            $total_price = $price * $actionDTO->quantity;

            $cart->cartProducts()->attach($product->id, [
                'name'           => $product->name,
                'sku'            => $sku_matrix,
                'price_per_unit' => $price,
                'quantity'       => $actionDTO->quantity,
                'total_price'    => $total_price
            ]);

            $message = "Added to cart";
        }

        $this->recalculateCartTotal($cart);

        return [
            'success'          => true,
            'message'          => $message,
            'cart_items_count' => $cart->cartProducts->count(),
            'cart_total'       => $cart->total_price
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