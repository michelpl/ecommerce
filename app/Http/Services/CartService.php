<?php

namespace App\Http\Services;

use App\Http\Entities\Cart;
use App\Http\Entities\CartItem;
use App\Http\Factories\CartItemFactory;
use App\Http\Helpers\LoadFileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Env;

class CartService
{
    private Cart $cart;
    private CartItemFactory $cartItemFactory;
    private array $productListFromStorage;

    public function __construct(Cart $cart, CartItemFactory $cartItemFactory)
    {
        $this->cart = $cart;
        $this->cartItemFactory = $cartItemFactory;
        $this->productListFromStorage = $this->loadProductListFromFile();
    }

    private function loadProductListFromFile() : array
    {
        $fileName = Env::get("PRODUCT_LIST_JSON");
        $fileContent = LoadFileHelper::getJsonFile($fileName);

        return $fileContent;
    }


    public function cartUpdate(Request $request)
    {
        try {
            $newProducts = $this->findProducts(
                $request->products,
                $this->productListFromStorage
            );

            $this->cart->setProducts($newProducts);


            return $this->getCart();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getCart(): array
    {
        return $this->cart->getInstance();
    }

    /**
     * @param array $requestProducts
     * @return array An array of product ids
     * @throws \Exception
     */
    private function findProducts(array $requestedProducts, array $list): array
    {
        $listIds = array_column($list,'id');
        $requestedIds = array_column($requestedProducts,'id');
        $indexesNotFound = array_diff($requestedIds, $listIds);

        if (!empty($indexesNotFound)) {
            throw new \Exception(
                "Cart item(s) not found. id(s): " .
                implode(" , ", array_values($indexesNotFound)),
                404
            );
        }
        return array_keys(array_intersect($listIds, $requestedIds));
    }

    /**
     * @param array $productIds
     * @return array An array of CartItem
     * @throws \Exception
     */
    private function extractCartItemsFromProductList(array $productIds): array
    {
            $cartItems = [];
            foreach ($productIds as $index) {
                $cartItems[] =
                    $this->cartItemFactory->createFromJson(
                        $this->productListFromStorage[$index]
                    );
            }

            return $cartItems;
    }
}
