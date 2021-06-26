<?php

namespace App\Http\Entities;

class Cart
{
    private int $totalAmountInCents = 0;
    private int $totalAmountWithDiscountInCents = 0;
    private int $totalDiscountInCents = 0;
    private array $products = [];

    public function mock()
    {
        $item1 = new CartItem();
        $item2 = new CartItem();
        $item3 = new CartItem();
        $item4 = new CartItem();

        $item1
            ->setId(5)
            ->setQuantity(3)
            ->setUnitAmountInCents(100)
            ->setTotalAmountInCents(100)
            ->setIsGift(false)
            ->setDiscountInCents(0);
        $item2
            ->setId(871)
            ->setQuantity(11)
            ->setUnitAmountInCents(100)
            ->setTotalAmountInCents(100)
            ->setIsGift(false)
            ->setDiscountInCents(0);


        $item3
            ->setId(196)
            ->setQuantity(200)
            ->setUnitAmountInCents(100)
            ->setTotalAmountInCents(100)
            ->setIsGift(false)
            ->setDiscountInCents(0);

        $item4
            ->setId(555)
            ->setQuantity(19)
            ->setUnitAmountInCents(100)
            ->setTotalAmountInCents(100)
            ->setIsGift(false)
            ->setDiscountInCents(0);

        $array = [
            $item1->getInstance(),
            $item2->getInstance(),
            $item3->getInstance(),
            $item4->getInstance()
        ];


        $this->setProducts($array);
    }

    /**
     * @return array
     */
    public function getInstance(): array
    {
        $cart['total_amount'] = $this->getTotalAmountInCents();
        $cart['total_amount_with_discount'] = $this->getTotalAmountWithDiscountInCents();
        $cart['total_discount'] = $this->getTotalDiscountInCents();
        $cart['products'] = $this->getProducts();

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
