<?php

namespace App\Http\Entities;

class Cart
{
    private int $totalAmountInCents = 0;
    private int $totalAmountWithDiscountInCents = 0;
    private int $totalDiscountInCents = 0;
    private array $products = [];

    /**
     * @return array
     */
    public function getInstance(): array
    {
        $cart['total_amount'] = $this->getTotalAmountInCents();
        $cart['total_amount_with_discount'] = $this->getTotalAmountWithDiscountInCents();
        $cart['total_discount'] = $this->getTotalDiscountInCents();
        $cart['product'] = $this->getProducts();

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
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param array $products
     * @return Cart
     */
    public function setProducts(array $products): Cart
    {
        $this->products = $products;
        return $this;
    }
}
