<?php

namespace App\Http\Factories;

use App\Http\Factories\Interfaces\IProductList;

final class ProductListFactory implements IProductList
{
    public function CreateFromFile(array $productList): array
    {
        $newProductList = [];

        foreach ($productList as $product)
        {
            $newProductList[$product->id] = (array) $product;
        }

        return $newProductList;
    }
}
