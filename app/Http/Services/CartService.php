<?php

namespace App\Http\Services;

use App\Http\Entities\Cart;
use App\Http\Entities\CartItem;
use App\Http\Factories\CartItemFactory;
use App\Http\Helpers\LoadFileHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;

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
        $products = $this->productFactory(LoadFileHelper::getJsonFile($fileName));

        return $products;
    }

    private function productFactory($productList): array
    {
        $newProductList = [];
        foreach ($productList as $product)
        {
            $newProductList[$product->id] = $product;
        }

        return $newProductList;
    }


    public function cartUpdate(Request $request)
    {
        try {
            $this->buildProductListFromRequest($request);

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
        return $this->formatProductList($request);
    }

    private function checkIfAllRequestedProductsExists(array $requestedProducts)
    {
        $requestedIds = array_column($this->productListFromStorage,'id');
        $productIds = array_column($requestedProducts,'id');

        $notFoundProducts = array_diff($productIds, $requestedIds);

        if ($notFoundProducts) {
            throw new Exception(
                "Product(s) not found. id(s): " .
                implode(" , ", array_values($notFoundProducts)),
                404
            );
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    private function formatProductList(Request $request)
    {
        $products = [];

        $this->checkIfAllRequestedProductsExists($request->products);

        foreach ($request->products as $requestedProduct) {

            if (!isset($requestedProduct['id'])) {
                throw new Exception(
                    "The products:id field is missing",
                    400
                );
            }
            $cartItem = $this->cartItemFactory->create($requestedProduct);

            $product = $this->productListFromStorage[$cartItem->getId()];
            $cartItem->setQuantity($requestedProduct['quantity']);
            $cartItem->setIsGift($product->is_gift);
            $cartItem->setUnitAmountInCents($product->amount);

            $cartItem->setDiscountInCents(
                $this->getDiscountFromService(
                    $cartItem->getId(),
                    $cartItem->getTotalAmountInCents()
                )
            );
            $this->updateCartTotals(
                $cartItem->getTotalAmountInCents(),
                $cartItem->getDiscountInCents()
            );

            //Verificar se é black friday

            $products[] = $cartItem->getInstance();
        }

        $this->cart->setCartItems($products);
    }

    private function updateCartTotals(int $unitAmount, int $unitDiscount): void
    {
        $this->cart->setTotalAmountInCents(
            $this->cart->getTotalAmountInCents() +
            $unitAmount
        );

        $this->cart->setTotalDiscountInCents(
            $this->cart->getTotalDiscountInCents() +
            $unitDiscount
        );

        $this->cart->setTotalAmountWithDiscountInCents(
            $this->cart->getTotalAmountInCents() -
            $this->cart->getTotalDiscountInCents()
        );
    }

    private function getDiscountFromService($id, $totalAmount): int
    {
        try {
            /**
             * @implements DISCOUNT SERVICE
             */
            $discountPercentage = rand(0, 10);

            return ($totalAmount * $discountPercentage) / 100;


        } catch (Exception $e) {
            Log::critical("Discount service not available | " . $e->getMessage());
            return 0;
        }
    }
}
