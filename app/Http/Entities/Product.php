<?php

namespace App\Http\Entities;

class Product
{
    private int $id;
    private string $title;
    private string $description;
    private int $amountInCents;
    private bool $isGift;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Product
     */
    public function setId(int $id): Product
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Product
     */
    public function setTitle(string $title): Product
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription(string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }

    /**
     * @param int $amountInCents
     * @return Product
     */
    public function setAmountInCents(int $amountInCents): Product
    {
        $this->amountInCents = $amountInCents;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGift(): bool
    {
        return $this->isGift;
    }

    /**
     * @param bool $isGift
     * @return Product
     */
    public function setIsGift(bool $isGift): Product
    {
        $this->isGift = $isGift;
        return $this;
    }
}
