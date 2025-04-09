<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Product\Product;
use App\Util\Assert;

readonly class InventoryItem
{
    public function __construct(
        private Product $product,
        private int $quantity
    ) {
        Assert::nonNegativeInt($quantity, 'Quantity must be non-negative.');
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getSku(): string
    {
        return $this->product->getSku();
    }

    public function increaseQuantity(int $amount): self
    {
        Assert::nonNegativeInt($amount, 'Cannot increase by a negative amount.');

        return new self($this->product, $this->quantity + $amount);
    }

    public function decreaseQuantity(int $amount): self
    {
        Assert::nonNegativeInt($amount, 'Cannot decrease by a negative amount.');
        Assert::maxInt($amount, $this->quantity, 'Cannot decrease more than current quantity.');

        return new self($this->product, $this->quantity - $amount);
    }
}
