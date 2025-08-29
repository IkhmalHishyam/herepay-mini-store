<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\StoreController;
use App\Http\Controllers\Admins\ProductController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\DashboardController;

Route::get('/', [StoreController::class, 'index'])
    ->name('home');

Route::middleware(['auth'])->group(function () 
{
    Route::prefix('cart')->group(function ()
    {
        Route::controller(CartController::class)->group(function ()
        {
            Route::get('/', 'index')
                ->name('cart.index');

            Route::get('/summary', 'summary')
                ->name('cart.summary');

            Route::post('/add', 'store')
                ->name('cart.add');

            Route::put('/update', 'update')
                ->name('cart.update');

            Route::delete('/item', 'destroyItem')
                ->name('cart.remove-item');

            Route::delete('/clear', 'clear')
                ->name('cart.clear');

            Route::delete('/', 'destroy')   
                ->name('cart.destroy');
        });
    });

    Route::prefix('orders')->group(function ()
    {
        Route::controller(StoreController::class)->group(function ()
        {
            Route::post('/submit', 'submitOrder')
                ->name('orders.submit');
        });
    });

    Route::middleware(['role.redirect'])->group(function () 
    {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('product')->group(function ()
        {
            Route::controller(ProductController::class)->group(function ()
            {
                Route::get('/', 'index')
                    ->name('admin.products.index');

                Route::get('/create', 'create')
                    ->name('admin.products.create');

                Route::post('/', 'store')
                    ->name('admin.products.store');

                Route::get('/{product_id}', 'show')
                    ->name('admin.products.show');

                Route::get('/{product_id}/edit', 'edit')
                    ->name('admin.products.edit');

                Route::post('/{product_id}', 'update')
                    ->name('admin.products.update');

                Route::post('/{product_id}/publish', 'publish')
                    ->name('admin.products.publish');

                Route::post('/{product_id}/unpublish', 'unpublish')
                    ->name('admin.products.unpublish');

                Route::delete('/{product_id}', 'destroy')
                    ->name('admin.products.destroy');
            });
        });
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
