<?php

declare(strict_types=1);

namespace App\Entity\Product;

use App\Entity\Brand;
use App\Util\Assert;

readonly class TV extends Product
{
    public function __construct(
        string $sku,
        string $name,
        float $price,
        Brand $brand,
        private int $screenSizeInch
    ) {
        parent::__construct($sku, $name, $price, $brand);

        Assert::positiveInt($screenSizeInch, 'Screen size must be positive.');
    }

    public function getScreenSizeInch(): int
    {
        return $this->screenSizeInch;
    }

    public function getUniqueAttributes(): array
    {
        return ['Screen Size (inch)' => $this->screenSizeInch];
    }
}
