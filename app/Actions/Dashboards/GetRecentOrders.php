<?php

namespace App\Actions\Dashboards;

use App\Models\Orders\Order;

class GetRecentOrders
{
    public function handle(): array
    {
        return Order::with(['orderDetail', 'invoice'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id'             => $order->id,
                    'order_number'   => $order->order_number,
                    'total_price'    => $order->total_price,
                    'status'         => $order->status,
                    'created_at'     => $order->created_at,
                    'customer_name'  => $order->orderDetail?->customer_name ?? 'N/A',
                    'invoice_status' => $order->invoice?->status ?? 'N/A',
                ];
            })
            ->toArray();
    }
}
