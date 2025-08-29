<?php

namespace App\Http\Controllers\Public;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Models\References\Tag;
use Illuminate\Support\Facades\DB;
use App\Actions\Orders\CreateOrder;
use App\Models\References\Category;
use App\DTOs\Actions\SubmitOrderDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Actions\Products\GetPublishedProducts;
use App\Http\Requests\Orders\SubmitOrderRequest;

class StoreController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        try
        {
            $action   = new GetPublishedProducts;
            $products = $action->handle($request);

            $categories = Category::select('id', 'name')
                ->whereHas('productCategories')
                ->orderBy('name')
                ->get();

            $tags = Tag::select('id', 'name')
                ->whereHas('productTags')
                ->orderBy('name')
                ->get();

            return Inertia::render('Welcome', [
                'products' => $products,
                'filters'  => [
                    'categories'   => $categories,
                    'tags'         => $tags,
                    'search'       => $request->input('search', ''),
                    'category_ids' => $request->input('category_ids', []),
                    'tag_ids'      => $request->input('tag_ids', []),
                    'min_price'    => $request->input('min_price'),
                    'max_price'    => $request->input('max_price'),
                    'stock_status' => $request->input('stock_status'),
                    'order_by'     => $request->input('order_by'),
                ]
            ]);
        }
        catch (\Exception $e)
        {            
            return redirect()->back()
                ->with('error', 'An error occurred while loading products: ' . $e->getMessage());
        }
    }

    public function submitOrder(SubmitOrderRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try
        {
            $action = new CreateOrder;
            $order  = $action->handle(new SubmitOrderDTO($request->validated()));
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', "Order #{$order->order_number} submitted successfully! We will contact you shortly.");
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}