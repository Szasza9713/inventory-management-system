<?php

declare(strict_types=1);

namespace App\Entity;

use App\Util\Assert;

final class Brand
{
    public function __construct(private string $name, private readonly int $quality)
    {
        Assert::notEmpty($name, 'Brand name must not be empty.');
        Assert::inRange($quality, 1, 5, 'Quality must be between 1 and 5.');

        $this->name = trim($name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function equals(self $other): bool
    {
        return $this->name === $other->getName() &&
            $this->quality === $other->getQuality();
    }
}
