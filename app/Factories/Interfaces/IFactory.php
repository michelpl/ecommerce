<?php

namespace App\Factories\Interfaces;

use App\Entities\CartItem;

interface IFactory
{
    public function create(array $item);
}
