<?php

namespace App\Http\Interfaces;

use Illuminate\Http\Request;

interface ICheckout
{
    function addProductsToCart(Request $request);
}
