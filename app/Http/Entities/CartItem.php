<?php

namespace App\Http\Entities;

class CartItem
{
    private ?int $id = null;
    private int $quantity = 0;
    private int $unitAmountInCents = 0;
    private int $totalAmountInCents = 0;
    private int $discountInCents = 0;
    private bool $isGift = false;

    /**
     * @return array
     */
    public function getInstance()
    {
        $cart = new \stdClass();
        $cart->id = $this->getId();
        $cart->quantity = $this->getQuantity();
        $cart->unit_amount = $this->getUnitAmountInCents();
        $cart->total_amount = $this->getUnitAmountInCents();
        $cart->discount = $this->getTotalAmountInCents();
        $cart->discount = $this->getDiscountInCents();
        $cart->is_gift = $this->getIsGift();

        return $cart;
    }
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return CartItem
     */
    public function setId(int $id): CartItem
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return CartItem
     */
    public function setQuantity(int $quantity): CartItem
    {
        if ($quantity < 0) {
            throw new \Exception(
                "Quantity must be greater than 0",
                400
            );
        }

        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return int
     */
    public function getUnitAmountInCents(): int
    {
        return $this->unitAmountInCents;
    }

    /**
     * @param int $unitAmountInCents
     * @return CartItem
     */
    public function setUnitAmountInCents(int $unitAmountInCents): CartItem
    {
        if ($unitAmountInCents < 0) {
            throw new \Exception(
                "Unity amount must be greater than 0",
                400
            );
        }
        $this->unitAmountInCents = $unitAmountInCents;
        return $this;
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
     * @return CartItem
     */
    public function setTotalAmountInCents(int $totalAmountInCents): CartItem
    {
        if ($totalAmountInCents < 0) {
            throw new \Exception(
                "Total amount must be greater than 0",
                400
            );
        }
        $this->totalAmountInCents = $totalAmountInCents;
        return $this;
    }

    /**
     * @return int
     */
    public function getDiscountInCents(): int
    {
        return $this->discountInCents;
    }

    /**
     * @param int $discountInCents
     * @return CartItem
     */
    public function setDiscountInCents(int $discountInCents): CartItem
    {
        if ($discountInCents < 0) {
            throw new \Exception(
                "Discount must be greater than 0",
                400
            );
        }
        $this->discountInCents = $discountInCents;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsGift(): bool
    {
        return $this->isGift;
    }

    /**
     * @param bool $isGift
     * @return CartItem
     */
    public function setIsGift(bool $isGift): CartItem
    {
        $this->isGift = $isGift;
        return $this;
    }
}
