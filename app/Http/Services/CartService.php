<?php

namespace App\Http\Services;

use App\Http\Entities\Cart;
use App\Http\Entities\Product;
use App\Http\Helpers\LoadFileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Env;

class CartService
{
    private Cart $cart;
    private array $productListFromStorage;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;

        $fileName = Env::get("PRODUCT_LIST_JSON");
        $productList = LoadFileHelper::getJsonFile($fileName);
        $this->productListFromStorage = $productList;
    }

    public function addProducts(Request $request)
    {
        try {
            $currentProducts = $this->cart->getProducts();

            $foundOnStorage = [];
            foreach ($request->products as $productToAdd)
            {

                $index = array_keys(
                    array_column(
                        $this->productListFromStorage,
                        'id'
                    ),
                    $productToAdd['id']
                );

                if (empty($index)) {
                    throw new \Exception(
                        "Product {$productToAdd['id']} not found",
                        404
                    );
                }

                $index = intval(current($index));

                if (isset($this->productListFromStorage[$index])) {
                    $foundOnStorage[] = $this->productListFromStorage[$index];
                }
            }

            $this->cart->setProducts($foundOnStorage);

            return $this->getCart();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getCart(): array
    {
        return $this->cart->getInstance();
    }
}
