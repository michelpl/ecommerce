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
    private array $cartProductList;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
        $this->productListFromStorage = $this->loadProductListFromFile();
    }

    private function loadProductListFromFile() : array
    {
        $fileName = Env::get("PRODUCT_LIST_JSON");
        $fileContent = LoadFileHelper::getJsonFile($fileName);
        return $this->productListFactory($fileContent);
    }

    private function productListFactory($fileContent): array
    {
        $productList = [];

        foreach ($fileContent as $product) {
            $productList[$product->id] = $product;
        }

        return $productList;
    }

    public function cartUpdate(Request $request)
    {
        try {
            $newProducts = $this->findProducts($request->products);




            return $this->getCart();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getCart(): array
    {
        return $this->cart->getInstance();
    }

    private function findProducts(array $requestProducts)
    {
        $fromStorageIndexes = array_column(
            $this->productListFromStorage,
            'id'
        );

        $fromRequestIndexes = array_column(
            $requestProducts,
            'id'
        );

        $indexesNotFound = array_diff($fromRequestIndexes, $fromStorageIndexes);

        if (!empty($indexesNotFound)) {
            throw new \Exception(
                "Product(s) not found id(s): " .
                implode(" , ", array_values($indexesNotFound)),
                404
            );
        }

        return $fromRequestIndexes;
    }

    /**
     * @param array $fromRequestIndexes
     * @param array $foundOnStorage
     * @return array
     */
    private function fillProductList(array $fromRequestIndexes, array $foundOnStorage): array
    {
        foreach ($fromRequestIndexes as $index) {
            $foundOnStorage[] = $this->productListFromStorage[$index];
        }
        return $foundOnStorage;
    }

}
