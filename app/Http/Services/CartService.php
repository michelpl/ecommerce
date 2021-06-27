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
    }

    private function loadProductListFromFile() : array
    {
        $fileName = Env::get("PRODUCT_LIST_JSON");
        return LoadFileHelper::getJsonFile($fileName);
    }


    public function cartUpdate(Request $request)
    {
        try {
            return $this->buildProductListFromRequest($request);

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
     * @param Request $request
     */
    private function buildProductListFromRequest(Request $request)
    {
        if (!isset($request->products)) {
            throw new Exception(
                "The products field is missing",
                400
            );
        }

        /*
         * @todo Verificar se a pessoa está tentando adicionar um produto que é gift
         * */

        $notFoundProducts = $this->notFoundProducts($request->products);

        if ($notFoundProducts) {
            throw new Exception(
                "Product(s) not found. id(s): " .
                implode(" , ", array_values($notFoundProducts)),
                404
            );
        }

        return $this->formatProductList($request);
    }

    private function notFoundProducts(array $requestedProducts): array
    {
        $requestedIds = array_column($this->productListFromStorage,'id');
        $productIds = array_column($requestedProducts,'id');

        return array_diff($productIds, $requestedIds);
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    private function formatProductList(Request $request): array
    {
        $products = [];
        foreach ($request->products as $requestedProduct) {

            if (!isset($requestedProduct['id'])) {
                throw new Exception(
                    "The products:id field is missing",
                    400
                );
            }

            $cartItem = $this->cartItemFactory->create($requestedProduct);
            $cartItem->setQuantity($requestedProduct['quantity']);
            //$cartItem->setIsGift();

            $cartItem->setDiscountInCents(
                $this->getDiscountFromService(
                    $cartItem->getId()
                )
            );
            //update total amouint
            //update total discount


            $products[] = $cartItem->getInstance();
        }

        return $products;
    }

    private function getDiscountFromService()
    {
        return 0;
    }
}
