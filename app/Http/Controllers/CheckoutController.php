<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\ICheckout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckoutController extends Controller implements ICheckout
{
    protected array $validationRules = [
        'id' => 'required|integer',
        'quantity' => 'required|integer',

    ];

    /**
     * Add products to cart
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request):object
    {
        return response()->json($request, Response::HTTP_OK);
    }
}
