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

    public function createFromJson(object $product) : array
    {
        try {
            $this->cartItem
                ->setId($product->id ?? null)
                ->setTotalAmountInCents($product->totalAmount ?? 0)
                ->setIsGift($product->isGift ?? false)
                ->setDiscountInCents($product->discountInCents ?? 0)
                ->setQuantity($product->quantity ?? 0)
                ->setUnitAmountInCents($product->amount ?? 0);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $this->cartItem->getInstance();
    }
}
