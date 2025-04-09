<?php

declare(strict_types=1);

namespace App\Entity;

use App\Collection\InventoryCollection;
use App\Entity\Product\Product;
use App\Exception\Domain\WarehouseCapacityExceededException;
use App\Util\Assert;

readonly class Warehouse
{
    private InventoryCollection $items;

    public function __construct(
        private string $name,
        private string $address,
        private int $capacity
    ) {
        Assert::notEmpty($name, 'Warehouse name must not be empty.');
        Assert::notEmpty($address, 'Warehouse address must not be empty.');
        Assert::positiveInt($capacity, 'Capacity must be positive.');

        $this->items = new InventoryCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getFreeCapacity(): int
    {
        return $this->capacity - $this->items->getTotalQuantity();
    }

    public function getContents(): array
    {
        return $this->items->getAll();
    }

    private function canAccept(int $quantity): bool
    {
        return $this->getFreeCapacity() >= $quantity;
    }

    public function addProduct(Product $product, int $quantity): void
    {
        Assert::positiveInt($quantity, 'Quantity must be positive.');

        if (!$this->canAccept($quantity)) {
            throw new WarehouseCapacityExceededException('Not enough space in warehouse.');
        }

        $item = new InventoryItem($product, $quantity);
        $this->items->add($item);
    }

    public function removeProduct(string $sku, int $quantity): void
    {
        Assert::positiveInt($quantity, 'Quantity must be positive.');
        $this->items->remove($sku, $quantity);
    }
}
