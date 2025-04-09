<?php

declare(strict_types=1);

namespace App\Entity\Product;

use App\Entity\Brand;
use App\Util\Assert;

readonly class Laptop extends Product
{
    public function __construct(
        string $sku,
        string $name,
        float $price,
        Brand $brand,
        private int $ramSize
    ) {
        parent::__construct($sku, $name, $price, $brand);

        Assert::positiveInt($ramSize, 'RAM size must be positive.');
    }

    public function getRamSize(): int
    {
        return $this->ramSize;
    }

    public function getUniqueAttributes(): array
    {
        return ['RAM (GB)' => $this->ramSize];
    }
}
