<?php

namespace App\Entities;

class Cart
{
    private int $totalAmountInCents = 0;
    private int $totalAmountWithDiscountInCents = 0;
    private int $totalDiscountInCents = 0;
    private array $cartItems = [];

    /**
     * @return object
     */
    public function getInstance(): object
    {
        $cart = new \stdClass();
        $cart->total_amount = $this->getTotalAmountInCents();
        $cart->total_amount_with_discount = $this->getTotalAmountWithDiscountInCents();
        $cart->total_discount = $this->getTotalDiscountInCents();
        $cart->products = $this->getCartItems();

        return $cart;
    }

    /**
     * @return int
     */
    public function getTotalAmountInCents(): int
    {
        return $this->totalAmountInCents;
    }

    /**
     * @param int $totalAmountInCents
     * @return Cart
     */
    public function setTotalAmountInCents(int $totalAmountInCents): Cart
    {
        $this->totalAmountInCents = $totalAmountInCents;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalAmountWithDiscountInCents(): int
    {
        return $this->totalAmountWithDiscountInCents;
    }

    /**
     * @param int $totalAmountWithDiscountInCents
     * @return Cart
     */
    public function setTotalAmountWithDiscountInCents(int $totalAmountWithDiscountInCents): Cart
    {
        $this->totalAmountWithDiscountInCents = $totalAmountWithDiscountInCents;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalDiscountInCents(): int
    {
        return $this->totalDiscountInCents;
    }

    /**
     * @param int $totalDiscountInCents
     * @return Cart
     */
    public function setTotalDiscountInCents(int $totalDiscountInCents): Cart
    {
        $this->totalDiscountInCents = $totalDiscountInCents;
        return $this;
    }

    /**
     * @return array
     */
    public function getCartItems(): array
    {
        return $this->cartItems;
    }

    /**
     * @param array $cartItems
     * @return Cart
     */
    public function setCartItems(array $cartItems): Cart
    {
        $this->cartItems = $cartItems;
        return $this;
    }

    /**
     * @param int $productId
     * @return false|CartItem
     */
    public function getCartItem(int $productId)
    {
        $cartItems = $this->getCartItems();
        $indexes = array_column($cartItems,'id');

        $index = array_search($productId, $indexes);

        if (isset($cartItems[(int)$index])) {
            return $cartItems[(int)$index];
        }

        return false;
    }
}
