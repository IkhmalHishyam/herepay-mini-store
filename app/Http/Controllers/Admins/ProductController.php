<?php

namespace App\Http\Controllers\Admins;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Actions\Products\GetProduct;
use App\Http\Controllers\Controller;
use App\DTOs\Actions\StoreProductDTO;
use Illuminate\Http\RedirectResponse;
use App\Actions\Products\StoreProduct;
use App\DTOs\Actions\UpdateProductDTO;
use App\Actions\Products\DeleteProduct;
use App\Actions\Products\UpdateProduct;
use App\Actions\Products\GetAllProducts;
use App\Actions\Products\TogglePublishProduct;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;

class ProductController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        try
        {
            $action   = new GetAllProducts;
            $products = $action->handle($request);

            return Inertia::render('Products/Index', [
                'products' => $products,
                'filters' => [
                    'search'          => $request->search,
                    'order_by'        => $request->order_by,
                    'order_direction' => $request->order_direction,
                    'per_page'        => (int) $request->per_page
                ]
            ]);
        }
        catch (\Exception $e)
        {
            return redirect()->back()
                ->with('error', 'An error occurred while fetching products.');
        }
    }

    public function show(string $product_id): Response|RedirectResponse
    {
        try 
        {
            $action  = new GetProduct;
            $product = $action->handle($product_id);

            return Inertia::render('Products/Show', [
                'product' => $product
            ]);
        }
        catch (\Exception $e)
        {
            return redirect()->back()
                ->with('error', 'An error occurred while fetching product details.');
        }
    }

    public function create(): Response|RedirectResponse
    {
        try
        {
            return Inertia::render('Products/Create');
        }
        catch (\Exception $e)
        {
            return redirect()->back()
                ->with('error', 'An error occurred while preparing the product creation form.');
        }
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try
        {
            $action = new StoreProduct;
            $action->handle(new StoreProductDTO($request->validated()));

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
        }
        catch (\Exception $e)
        {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while creating the product.');
        }
    }

    public function edit(string $product_id): Response|RedirectResponse
    {
        try 
        {
            $action  = new GetProduct;
            $product = $action->handle($product_id);

            return Inertia::render('Products/Edit', [
                'product' => $product
            ]);
        }
        catch (\Exception $e)
        {
            return redirect()->back()
                ->with('error', 'An error occurred while fetching product details for editing.');
        }
    }

    public function update(UpdateProductRequest $request, string $product_id): RedirectResponse
    {
        DB::beginTransaction();

        try
        {
            $action  = new UpdateProduct;
            $product = $action->handle(new UpdateProductDTO($request->validated()), $product_id);

            DB::commit();

            return redirect()->route('admin.products.show', $product->id)
                ->with('success', 'Product updated successfully.');
        }
        catch (\Exception $e)
        {
            dd($e->getMessage());
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while updating the product.');
        }
    }

    public function publish(string $product_id): RedirectResponse
    {
        DB::beginTransaction();

        try
        {
            $action = new TogglePublishProduct;
            $action->handle($product_id, true);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product published successfully.');
        }
        catch (\Exception $e)
        {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while publishing the product.');
        }
    }

    public function unpublish(string $product_id): RedirectResponse
    {
        DB::beginTransaction();

        try
        {
            $action = new TogglePublishProduct;
            $action->handle($product_id, false);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product unpublished successfully.');
        }
        catch (\Exception $e)
        {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while unpublishing the product.');
        }
    }

    public function destroy(string $product_id): RedirectResponse
    {
        DB::beginTransaction();

        try
        {
            $action = new DeleteProduct;
            $action->handle($product_id);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');
        }
        catch (\Exception $e)
        {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the product.');
        }
    }
}