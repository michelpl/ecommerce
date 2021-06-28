<?php

namespace App\Http\Services;

use App\Http\Entities\Cart;
use App\Http\Factories\CartItemFactory;
use App\Http\Helpers\LoadFileHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Env;

final class CartService
{
    private Cart $cart;
    private CartItemFactory $cartItemFactory;
    private array $productListFromStorage;
    private DiscountService $discountService;

    public function __construct(
        Cart $cart,
        CartItemFactory $cartItemFactory,
        DiscountService $discountService
    )
    {
        $this->cart = $cart;
        $this->cartItemFactory = $cartItemFactory;
        $this->productListFromStorage = $this->loadProductListFromFile();
        $this->discountService = $discountService;
    }

    /**
     * @return array
     */
    private function loadProductListFromFile() : array
    {
        $fileName = Env::get("PRODUCT_LIST_JSON");
        return $this->productFactory(LoadFileHelper::getJsonFile($fileName));
    }

    /**
     * @param $productList
     * @return array
     */
    private function productFactory($productList): array
    {
        $newProductList = [];
        foreach ($productList as $product)
        {
            $newProductList[$product->id] = $product;
        }

        return $newProductList;
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function cartUpdate(Request $request): void
    {
        try {
            $this->buildProductListFromRequest($request);

            if ($this->shouldApplyGift()) {
                $this->addGift();
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return array
     */
    public function getCart(): object
    {
        return $this->cart->getInstance();
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function buildProductListFromRequest(Request $request): void
    {
        if (!isset($request->products)) {
            throw new Exception(
                "The products field is missing",
                400
            );
        }
        $this->formatProductList($request);
    }

    /**
     * @param array $requestedProducts
     * @throws Exception
     */
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
     * @return int|string
     */
    private function findGiftProductId()
    {
        $isGiftFields = array_column($this->productListFromStorage,'is_gift');
        $index = array_search(true, $isGiftFields);
        return array_keys($this->productListFromStorage)[$index];
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function formatProductList(Request $request): void
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

            if ($product->is_gift) {
                throw new Exception(
                    "You can't add a gift product",
                    400
                );
            }

            $cartItem->setQuantity($requestedProduct['quantity']);
            $cartItem->setIsGift($product->is_gift);
            $cartItem->setUnitAmountInCents($product->amount);

            $cartItem->setDiscountInCents(
                $this->getDiscountValue(
                    $cartItem->getId(),
                    $cartItem->getTotalAmountInCents()
                )
            );
            $this->updateCartTotals(
                $cartItem->getTotalAmountInCents(),
                $cartItem->getDiscountInCents()
            );

            $products[] = $cartItem->getInstance();
        }
        $this->cart->setCartItems($products);
    }

    /**
     * @param int $unitAmount
     * @param int $unitDiscount
     */
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

    /**
     * @throws Exception
     */
    private function addGift(): void
    {
        $id = $this->findGiftProductId();
        $gift = (array) ($this->productListFromStorage[$id]);

        $item = $this->cartItemFactory->create($gift);
        $item->setQuantity(1);
        $item->setUnitAmountInCents($gift['amount']);
        $item->setDiscountInCents($gift['amount']);
        $item->setIsGift(true);

        $newList = $this->cart->getCartItems() ;
        $newList[] = $item->getInstance();
        $this->cart->setCartItems($newList);

        $this->updateCartTotals(
            $item->getUnitAmountInCents(),
            $item->getDiscountInCents()
        );
    }

    /**
     * @return bool
     */
    private function shouldApplyGift():bool
    {
        $promotionDate = Env::get("BLACK_FRIDAY_DATE");
        return $promotionDate == date("Y-m-d");
    }

    /**
     * @param int $id
     * @param int $amount
     * @return int
     */
    private function getDiscountValue(int $id, int $amount): int
    {
        $discount = $this->discountService->getDiscountFromService($id);

        return round($amount * $discount);
    }
}
