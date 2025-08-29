<?php

namespace App\Actions\Orders;

use Exception;
use App\Models\Carts\Cart;
use App\Services\Generator;
use App\Models\Orders\Order;
use App\Models\Payments\Invoice;
use App\Models\Products\Product;
use App\Models\Orders\OrderDetail;
use App\Models\Orders\OrderProduct;
use App\DTOs\Actions\SubmitOrderDTO;
use App\Enums\InvoiceStatusEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\Auth;
use App\Models\Customers\CustomerStat;
use App\Models\Payments\Transaction;
use App\Enums\TransactionStatusEnum;
use App\Models\Products\ProductStat;

class CreateOrder
{
    public function handle(SubmitOrderDTO $orderDTO): Order
    {
        $cart = Cart::where('user_id', Auth::user()->id)->first();
        
        if (!$cart || $cart->cartProducts->count() === 0) 
        {
            throw new Exception('Cart is empty. Please add items before submitting an order.');
        }

        $this->validateStockAvailability($cart);
        
        $order_number = Generator::orderNumber();
        $total_price  = $cart->total_price;
        
        $order = Order::create([
            'order_number' => $order_number,
            'total_price'  => $total_price,
            'user_id'      => Auth::user()->id,
            'status'       => OrderStatusEnum::PENDING->value,
        ]);
        
        OrderDetail::create([
            'customer_name'      => $orderDTO->customer_name,
            'customer_email'     => $orderDTO->customer_email,
            'customer_phone'     => $orderDTO->customer_phone,
            'shipping_address_1' => $orderDTO->address_1,
            'shipping_address_2' => $orderDTO->address_2,
            'shipping_city'      => $orderDTO->city,
            'shipping_state'     => $orderDTO->state,
            'shipping_postcode'  => $orderDTO->postcode,
            'shipping_country'   => $orderDTO->country,
            'order_notes'        => $orderDTO->order_notes,
            'order_id'           => $order->id,
        ]);
        
        foreach ($cart->cartProducts as $cartProduct) 
        {
            $product = Product::find($cartProduct->id);
            $sku     = null;
            
            if ($cartProduct->pivot->sku) 
            {
                $sku = $product->skus()
                    ->where('matrix', $cartProduct->pivot->sku)
                    ->first();
                
                if (!$sku)
                {
                    throw new Exception("SKU with matrix '{$cartProduct->pivot->sku}' not found for product '{$product->name}'");
                }
            }
            
            $orderProduct = OrderProduct::create([
                'name'           => $cartProduct->pivot->name,
                'sku'            => $cartProduct->pivot->sku,
                'description'    => $product->description ?? null,
                'price_per_unit' => $cartProduct->pivot->price_per_unit,
                'quantity'       => $cartProduct->pivot->quantity,
                'total_price'    => $cartProduct->pivot->total_price,
                'order_id'       => $order->id,
            ]);
            
            $this->createImageSnapshots($orderProduct, $product);
        }
        
        $invoice_number = Generator::invoiceNumber();
        $invoice        = Invoice::create([
            'invoice_number' => $invoice_number,
            'amount'         => $total_price,
            'status'         => InvoiceStatusEnum::PENDING->value,
            'order_id'       => $order->id,
            'user_id'        => Auth::user()->id,
        ]);
        
        Transaction::create([
            'transaction_no' => Generator::transactionNumber(),
            'amount'         => $total_price,
            'currency'       => 'MYR',
            'payment_method' => 'mockup',
            'status'         => TransactionStatusEnum::PENDING->value,
            'invoice_id'     => $invoice->id,
        ]);
        
        // Handle success/failure scenarios
        if ($orderDTO->is_success) 
        {
            $this->handleSuccessScenario($order, $invoice, $cart);
        } 
        else 
        {
            $this->handleFailureScenario($order, $invoice, $cart);
        }
        
        return $order;
    }
    
    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function validateStockAvailability(Cart $cart): void
    {
        foreach ($cart->cartProducts as $cartProduct) 
        {
            $product = Product::find($cartProduct->id);
            
            if ($cartProduct->pivot->sku) 
            {
                $sku = $product->skus()
                    ->where('matrix', $cartProduct->pivot->sku)
                    ->first();
                
                if (!$sku) 
                {
                    throw new Exception("SKU with matrix '{$cartProduct->pivot->sku}' not found for product '{$product->name}'");
                }
                
                if ($sku->total_stock < $cartProduct->pivot->quantity) 
                {
                    throw new Exception("Insufficient stock for {$product->name} ({$cartProduct->pivot->sku}). Available: {$sku->total_stock}, Requested: {$cartProduct->pivot->quantity}");
                }
            } 
            else 
            {
                if ($product->total_stock < $cartProduct->pivot->quantity) 
                {
                    throw new Exception("Insufficient stock for {$product->name}. Available: {$product->total_stock}, Requested: {$cartProduct->pivot->quantity}");
                }
            }
        }
    }

    protected function createImageSnapshots(OrderProduct $orderProduct, Product $product): void
    {
        $productImages = $product->productImages;

        $orderProduct->productImages()->attach($productImages->pluck('id')->toArray());
    }

    protected function handleSuccessScenario(Order $order, Invoice $invoice, Cart $cart): void
    {
        $order->update([
            'status' => OrderStatusEnum::PROCESSING->value
        ]);
        
        foreach ($cart->cartProducts as $cartProduct) 
        {
            $product = Product::find($cartProduct->id);
            
            if ($cartProduct->pivot->sku) 
            {
                $sku = $product->skus()
                    ->where('matrix', $cartProduct->pivot->sku)
                    ->first();
                
                if ($sku) 
                {
                    $sku->decrement('total_stock', $cartProduct->pivot->quantity);
                }
            } 
            else 
            {
                $product->decrement('total_stock', $cartProduct->pivot->quantity);
            }
        }
        
        $invoice->update([
            'status' => InvoiceStatusEnum::PAID->value
        ]);
        
        $invoice->transactions()->latest()->first()->update([
            'status' => TransactionStatusEnum::SUCCESSFUL->value,
            'paid_at' => now(),
        ]);
        
        $this->updateCustomerStats($order->total_price);
        $this->updateProductStats($cart);
        
        $this->clearCart($cart);
    }

    protected function handleFailureScenario(Order $order, Invoice $invoice, Cart $cart): void
    {
        $order->update([
            'status' => OrderStatusEnum::CANCELLED->value
        ]);
        
        $invoice->update([
            'status' => InvoiceStatusEnum::CANCELLED->value
        ]);
        
        $invoice->transactions()->latest()->first()->update([
            'status' => TransactionStatusEnum::FAILED->value,
        ]);

        $this->clearCart($cart);
    }

    protected function updateCustomerStats(float $order_total): void
    {
        $customerStat = CustomerStat::firstOrCreate([
            'user_id' => Auth::user()->id,
        ], [
            'user_id'             => Auth::user()->id,
            'total_orders'        => 0,
            'total_spent'         => 0,
            'average_order_value' => 0,
            'last_order_date'     => null
        ]);
        
        $customerStat->increment('total_orders');
        $customerStat->increment('total_spent', $order_total);
        $customerStat->update([
            'average_order_value' => $customerStat->total_spent / $customerStat->total_orders,
            'last_order_date'     => now()
        ]);
    }
    
    protected function clearCart(Cart $cart): void
    {
        $cart->cartProducts()->detach();
        
        $cart->update([
            'total_price' => 0
        ]);
    }

    protected function updateProductStats(Cart $cart): void
    {
        foreach ($cart->cartProducts as $cartProduct) {
            $productStat = ProductStat::firstOrCreate([
                'product_id' => $cartProduct->id,
            ], [
                'total_revenue' => 0,
                'total_sold_in_units' => 0,
                'last_sold_at' => null,
            ]);
            
            $productStat->increment('total_revenue', $cartProduct->pivot->total_price);
            $productStat->increment('total_sold_in_units', $cartProduct->pivot->quantity);
            $productStat->update([
                'last_sold_at' => now(),
            ]);
        }
    }
}