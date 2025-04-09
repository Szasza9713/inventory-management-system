<?php

declare(strict_types=1);

namespace App\Entity\Product;

use App\Entity\Brand;
use App\Util\Assert;

readonly abstract class Product
{
    public function __construct(private string $sku, private string $name, private float $price, private Brand $brand)
    {
        Assert::notEmpty($sku, 'SKU must not be empty.');
        Assert::notEmpty($name, 'Product name must not be empty.');
        Assert::nonNegativeFloat($price, 'Price must be non-negative.');
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getBrand(): Brand
    {
        return $this->brand;
    }

    abstract public function getUniqueAttributes(): array;
}
