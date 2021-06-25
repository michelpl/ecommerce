<?php

namespace App\Http\Controllers\Interfaces;

use Illuminate\Http\Request;

interface ICheckout
{
    function addProductsToCart(Request $request);
}
