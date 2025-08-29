<?php

namespace App\Http\Controllers\Public;

use Exception;
use App\Actions\Carts\GetCart;
use App\Actions\Carts\AddToCart;
use App\Actions\Carts\ClearCart;
use App\Actions\Carts\DeleteCart;
use App\Actions\Carts\UpdateCart;
use Illuminate\Http\JsonResponse;
use App\DTOs\Actions\AddToCartDTO;
use Illuminate\Support\Facades\DB;
use App\DTOs\Actions\UpdateCartDTO;
use App\Http\Controllers\Controller;
use App\Actions\Carts\DeleteCartItem;
use App\DTOs\Actions\DeleteCartItemDTO;
use App\Http\Requests\Carts\AddToCartRequest;
use App\Http\Requests\Carts\DeleteCartItemRequest;
use App\Http\Requests\Carts\UpdateCartRequest;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        try 
        {
            $action = new GetCart();
            $cart   = $action->handle();
            
            return response()->json([
                'success' => true,
                'cart'    => $cart
            ]);
        } 
        catch (Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(): JsonResponse
    {
        try 
        {
            $action  = new GetCart();
            $summary = $action->getCartSummary();

            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);
        } 
        catch (Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try 
        {
            $action = new AddToCart();
            $result = $action->handle(new AddToCartDTO($request->validated()));

            DB::commit();

            return response()->json($result);
        } 
        catch (Exception $e) 
        {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(UpdateCartRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try 
        {
            $action = new UpdateCart();
            $result = $action->handle(new UpdateCartDTO($request->validated()));

            DB::commit();

            return response()->json($result);
        } 
        catch (Exception $e) 
        {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroyItem(DeleteCartItemRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try 
        {
            $action = new DeleteCartItem();
            $result = $action->handle(new DeleteCartItemDTO($request->validated()));

            DB::commit();

            return response()->json($result);
        } 
        catch (Exception $e) 
        {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function clear(): JsonResponse
    {
        DB::beginTransaction();

        try 
        {
            $action = new ClearCart();
            $result = $action->handle();

            DB::commit();

            return response()->json($result);
        } 
        catch (Exception $e) 
        {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(): JsonResponse
    {
        DB::beginTransaction();

        try 
        {
            $action = new DeleteCart();
            $result = $action->handle();

            DB::commit();

            return response()->json($result);
        } 
        catch (Exception $e) 
        {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}