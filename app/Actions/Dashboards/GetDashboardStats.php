<?php

namespace App\Actions\Dashboards;

use App\Models\Orders\Order;
use App\Models\Products\Product;
use App\Enums\OrderStatusEnum;

class GetDashboardStats
{
    public function handle(): array
    {
        $total_orders       = Order::count();
        $completed_orders   = Order::completed()->count();
        $total_revenue      = Order::completed()->sum('total_price');
        $total_products     = Product::where('is_published', true)->count();
        $pending_orders     = Order::where('status', OrderStatusEnum::PENDING->value)->count();
        $this_month_order   = Order::thisMonth()->count();
        $this_month_revenue = Order::completed()
            ->thisMonth()
            ->sum('total_price');
        
        $this_month_completed = Order::completed()
            ->thisMonth()
            ->count();
        
        return [
            'totalOrders'         => $total_orders,
            'completedOrders'     => $completed_orders,
            'totalRevenue'        => $total_revenue,
            'totalProducts'       => $total_products,
            'pendingOrders'       => $pending_orders,
            'thisMonthOrders'     => $this_month_order,
            'thisMonthCompleted'  => $this_month_completed,
            'thisMonthRevenue'    => $this_month_revenue,
        ];
    }
}
