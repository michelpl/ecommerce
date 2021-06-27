<?php

namespace App\Http\Factories\Interfaces;

use App\Http\Entities\CartItem;

interface IFactory
{
    public function create(array $item);
}
