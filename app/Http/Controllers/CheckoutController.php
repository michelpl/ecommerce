<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Interfaces\ICheckout;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller implements ICheckout
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Add products to cart
     * @param Request $request
     * @return JsonResponse
     */
    public function addProductsToCart(Request $request): JsonResponse
    {
        try {
            $this->cartService->cartUpdate($request);

            return response()->json($this->cartService->getCart(), 201);

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }
}
