<?php

namespace App\Http\Services;

use App\Http\Entities\Cart;
use App\Http\Entities\CartItem;
use App\Http\Factories\CartItemFactory;
use App\Http\Helpers\LoadFileHelper;
use Exception;
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

        /**
         * REMOVER
         */
        $this->cart->mock();
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
            //REFACT
            $requestedProducts = [];
            foreach ($request->products as $requestedProduct) {
                $requestedProducts[$requestedProduct['id']] = $requestedProduct;
            }

            $currentCartItens = $this->cart->getProducts();

            return $this->updateCartProducts($currentCartItens, $requestedProducts);

            //Busca produtos passados no request no products json
            $newProductindexesOnProductList = $this->findProducts(
                $request->products,
                $this->productListFromStorage,
                true
            );

            //Transforma o que foi passado no request em um cart item
            /*$newCartItens = $this->extractCartItemsFromProductList(
                array_keys($newProductindexesOnProductList),
                $this->productListFromStorage
            );*/



            //Interceção entre os produtos novos e o que já estão no carrinho
            $found = $this->findProducts($request->products, $currentCartItens);

            //Pega o que eu achei de repetido e mostra o cart item dele
            $incrementList = [];

            foreach ($found as $index => $value)
            {
                $incrementList[] = $currentCartItens[$index];

            }

            $incrementList = $this->updateQuantity(
                $incrementList,
                $request->products
            );



            return [
                "current" => $currentCartItens,
                "new" => [],
                "found" => $found,
                "nicrementlist" => $incrementList
            ];

            /*$this->extractCartItemsFromProductList(array_values(
                array_intersect($newProductindexesOnProductList, $currentProductIndexesOnProductList)
            ));*/




            return $this->getCart();

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getCart(): array
    {
        return $this->cart->getInstance();
    }

    /**
     * @param array $requestedProducts
     * @param array $list
     * @param bool $validate Thrwo Exception if a product was not found
     * @return array An array of product ids
     * @throws Exception
     */
    private function findProducts(
        array $requestedProducts,
        array $list,
        bool $validate = false
    ): array
    {
        $listIds = array_column($list,'id');
        $requestedIds = array_column($requestedProducts,'id');

        $indexesNotFound = array_diff($requestedIds, $listIds);

        if ($validate && !empty($indexesNotFound)) {
            throw new Exception(
                "Cart item(s) not found. id(s): " .
                implode(" , ", array_values($indexesNotFound)),
                404
            );
        }

        return array_keys(array_intersect($listIds, $requestedIds));
    }

    private function updateCartProducts($currentCartItems, $requestProducts)
    {
        $found = [];
        foreach ($currentCartItems as $currentCartItem) {
            if (isset($requestProducts[$currentCartItem['id']])) {

                $currentCartItem['quantity'] +=
                    $requestProducts[$currentCartItem['id']]['quantity'];

                $found[] = $currentCartItem;
            }
        }

        return ["current" => $currentCartItems, "requested" => $requestProducts, "found" => $found];
    }

    private function updateQuantity(array $list, $request)
    {
        /*foreach ($list as $product) {

            $ids = array_column($request, 'id');


            $newQuantity = $requestId[$index];
            $newQuantities[] = $newQuantity;

        }


        return $newQuantities;*/
    }
    /**
     * @param array $productIds
     * @return array An array of CartItem
     * @throws Exception
     */
    private function extractCartItemsFromProductList(
        array $productIds,
        array $list
    ): array
    {
            $cartItems = [];
            foreach ($productIds as $index) {

                $cartItems[] =
                    $this->cartItemFactory->createFromObject(
                        $list[$index]
                    );
            }

            return $cartItems;
    }
}
