<?php

namespace App\Http\Controllers;

use App\Http\Helpers\LoadFileHelper;
use App\Http\Interfaces\ICheckout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Env;

class CheckoutController extends Controller implements ICheckout
{
    protected array $validationRules = [
        'id' => 'required|integer',
        'quantity' => 'required|integer'
    ];

    /**
     * Add products to cart
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addProductsToCart(Request $request)
    {
        LoadFileHelper::getJsonFile(Env::get("PRODUCT_LIST_JSON"));

        return response()->json($request, Response::HTTP_OK);
    }
}
