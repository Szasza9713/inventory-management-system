<?php

declare(strict_types=1);

namespace App\Collection;

use App\Entity\InventoryItem;
use App\Exception\Domain\InsufficientProductQuantityException;
use App\Exception\Domain\ProductNotFoundException;
use App\Util\Assert;
use ArrayIterator;
use IteratorAggregate;

class InventoryCollection implements IteratorAggregate
{
    /** @var array<string, InventoryItem> */
    private array $items = [];

    public function add(InventoryItem $item): void
    {
        $sku = $item->getSku();

        if (isset($this->items[$sku])) {
            $this->items[$sku] = $this->items[$sku]->increaseQuantity($item->getQuantity());
        } else {
            $this->items[$sku] = $item;
        }
    }

    public function remove(string $sku, int $quantity): void
    {
        Assert::positiveInt($quantity, 'Quantity must be positive.');

        if (!isset($this->items[$sku])) {
            throw new ProductNotFoundException("Product $sku not found in warehouse.");
        }

        $item = $this->items[$sku];

        if ($quantity > $item->getQuantity()) {
            throw new InsufficientProductQuantityException("Not enough quantity of product $sku in warehouse.");
        }

        $newItem = $item->decreaseQuantity($quantity);

        if ($newItem->getQuantity() === 0) {
            unset($this->items[$sku]);
        } else {
            $this->items[$sku] = $newItem;
        }
    }

    public function getTotalQuantity(): int
    {
        return array_sum(array_map(
            fn (InventoryItem $item) => $item->getQuantity(),
            $this->items
        ));
    }

    public function getAll(): array
    {
        return array_values($this->items);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
