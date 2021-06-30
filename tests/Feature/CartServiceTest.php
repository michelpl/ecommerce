<?php

namespace Tests\Feature;

use App\Services\CartService;
use Illuminate\Http\Request;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    private CartService $cartService;

    public function setUp(): void
    {
        parent::setUp();

        $this->cartService = $this->app->make('App\Services\CartService');


    }

    public function test_getCartShouldReturnTheApiContract()
    {
        $request = [
            "products" => [
                [
                    "id" => 3,
                    "quantity" => 5
                ]
            ]
        ];

        $request = new Request($request);

        $this->cartService->cartUpdate($request);
        $cart = $this->cartService->getCart();

        $expected = new \stdClass();
        $expected->total_amount = 301780;
        $expected->total_amount_with_discount= 301780;
        $expected->total_discount = 0;

        $product = new \stdClass();
        $product->id = 3;
        $product->quantity = 5;
        $product->unit_amount = 60356;
        $product->total_amount = 301780;
        $product->discount = 0;
        $product->is_gift = false;

        $expected->products[0] = $product;

        $this->assertEquals($expected, $cart);
    }
}
