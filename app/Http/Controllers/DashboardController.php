<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Actions\Dashboards\GetTopProducts;
use App\Actions\Dashboards\GetRecentOrders;
use App\Actions\Dashboards\GetDashboardStats;

class DashboardController extends Controller
{
    public function index(): Response|RedirectResponse
    {     
        try 
        {
            // TODO - Improve - Implement caching here
            
            $statsAction = new GetDashboardStats;
            $stats       = $statsAction->handle();
            
            $recentOrdersAction = new GetRecentOrders;
            $recentOrders       = $recentOrdersAction->handle();
            
            $topProductsAction = new GetTopProducts;
            $topProducts       = $topProductsAction->handle();
            
            return Inertia::render('Dashboard', [
                'stats'        => $stats,
                'recentOrders' => $recentOrders,
                'topProducts'  => $topProducts,
            ]);
        } 
        catch (\Exception $e)
        {
            return redirect()->back()
                ->with('error', 'An error occurred while fetching products.');
        }
    }
}
