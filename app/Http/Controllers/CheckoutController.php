<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Interfaces\ICheckout;
use App\Http\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckoutController extends Controller implements ICheckout
{
    private array $validationRules = [
        'id' => 'required|integer',
        'quantity' => 'required|integer'
    ];

    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Add products to cart
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addProductsToCart(Request $request)
    {
        try {
            return $this->cartService->addProducts($request);


            return response()->json($this->cartService->getCart());

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }
}
