<?php

declare(strict_types=1);

namespace App\Entity\Product;

use App\Entity\Brand;
use App\Util\Assert;

readonly class Chair extends Product
{
    public function __construct(
        string $sku,
        string $name,
        float $price,
        Brand $brand,
        private string $material
    ) {
        parent::__construct($sku, $name, $price, $brand);

        Assert::notEmpty($material, 'Material must not be empty.');
    }

    public function getMaterial(): string
    {
        return $this->material;
    }

    public function getUniqueAttributes(): array
    {
        return ['Material' => $this->material];
    }
}
