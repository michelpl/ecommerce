<?php

namespace App\Http\Factories\Interfaces;

interface IProductList
{
    public function CreateFromFile(array $productList): array;
}
