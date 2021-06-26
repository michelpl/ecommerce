<?php

namespace App\Http\Factories;

use App\Http\Entities\CartItem;
use App\Http\Factories\Interfaces\IFactory;

class CartItemFactory implements IFactory
{
    private CartItem $cartItem;

    public function __construct(CartItem $cartItem)
    {
        $this->cartItem = $cartItem;
    }

    public function createFromObject(object $item) : array
    {
        try {
            $this->cartItem
                ->setId($item->id ?? null)
                ->setTotalAmountInCents($item->totalAmount ?? 0)
                ->setIsGift($item->isGift ?? false)
                ->setDiscountInCents($item->discountInCents ?? 0)
                ->setQuantity($item->quantity ?? 0)
                ->setUnitAmountInCents($item->amount ?? 0);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $this->cartItem->getInstance();
    }

    public function createFromArray(array $item) : array
    {
        try {
            $this->cartItem
                ->setId($item['id'] ?? null)
                ->setTotalAmountInCents($item['totalAmount'] ?? 0)
                ->setIsGift($item['isGift'] ?? false)
                ->setDiscountInCents($item['discountInCents'] ?? 0)
                ->setQuantity($item['quantity'] ?? 0)
                ->setUnitAmountInCents($item['amount'] ?? 0);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $this->cartItem->getInstance();
    }
}
