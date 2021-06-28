<?php

namespace App\Http\Services;

use App\Http\Entities\Cart;
use App\Http\Factories\CartItemFactory;
use App\Http\Helpers\LoadFileHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

final class CartService
{
    private Cart $cart;
    private CartItemFactory $cartItemFactory;
    private array $productListFromStorage;
    private Client $guzzleClient;

    public function __construct(
        Cart $cart,
        CartItemFactory $cartItemFactory,
        Client $guzzleClient,
        DiscountService $discountService
    )
    {
        $this->cart = $cart;
        $this->cartItemFactory = $cartItemFactory;
        $this->productListFromStorage = $this->loadProductListFromFile();
        $this->guzzleClient = $guzzleClient;
        $this->discountService = $discountService;
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

            if ($this->shouldApplyGift()) {
                $this->addGift();
            }

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

    private function findGiftProductId()
    {
        $isGiftFields = array_column($this->productListFromStorage,'is_gift');
        $index = array_search(true, $isGiftFields);
        return array_keys($this->productListFromStorage)[$index];
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

    private function addGift()
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

    private function shouldApplyGift():bool
    {
        $promotionDate = Env::get("BLACK_FRIDAY_DATE");
        return $promotionDate == date("Y-m-d");
    }

    private function getDiscountValue(int $id, int $amount): int
    {
        $discount = $this->discountService->getDiscountFromService($id, $amount);

        return round($amount * $discount);
    }
}
