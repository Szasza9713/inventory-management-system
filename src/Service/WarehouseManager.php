<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product\Product;
use App\Entity\Warehouse;
use App\Exception\Domain\InsufficientProductQuantityException;
use App\Exception\Domain\ProductNotFoundException;
use App\Exception\Domain\WarehouseCapacityExceededException;
use App\Util\Assert;

class WarehouseManager
{
    /** @var Warehouse[] */
    private array $warehouses = [];

    public function addWarehouse(Warehouse $warehouse): void
    {
        $this->warehouses[] = $warehouse;
    }

    public function addProduct(Product $product, int $quantity): void
    {
        Assert::positiveInt($quantity, 'Quantity must be positive.');

        foreach ($this->warehouses as $warehouse) {
            if ($quantity === 0) {
                return;
            }

            $freeCapacity = $warehouse->getFreeCapacity();
            $toAdd = min($quantity, $freeCapacity);

            if ($toAdd > 0) {
                $warehouse->addProduct($product, $toAdd);
                $quantity -= $toAdd;
            }
        }

        if ($quantity > 0) {
            throw new WarehouseCapacityExceededException('Not enough space in all warehouses.');
        }
    }

    public function removeProduct(string $sku, int $quantity): void
    {
        Assert::positiveInt($quantity, 'Quantity must be positive.');

        if (!$this->hasSufficientQuantity($sku, $quantity)) {
            throw new InsufficientProductQuantityException("Not enough quantity of $sku across all warehouses.");
        }

        $this->removeQuantityFromWarehouses($sku, $quantity);
    }

    private function hasSufficientQuantity(string $sku, int $required): bool
    {
        $total = 0;

        foreach ($this->warehouses as $warehouse) {
            foreach ($warehouse->getContents() as $item) {
                if ($item->getSku() === $sku) {
                    $total += $item->getQuantity();
                }
            }
        }

        return $total >= $required;
    }

    private function removeQuantityFromWarehouses(string $sku, int $quantity): void
    {
        foreach ($this->warehouses as $warehouse) {
            try {
                $warehouse->removeProduct($sku, $quantity);
                return;
            } catch (InsufficientProductQuantityException $e) {
                $item = $this->findItemInWarehouse($warehouse, $sku);
                if ($item !== null) {
                    $available = $item->getQuantity();
                    $warehouse->removeProduct($sku, $available);
                    $quantity -= $available;
                }
            } catch (ProductNotFoundException $e) {
                // skip this warehouse
            }
        }
    }

    private function findItemInWarehouse(Warehouse $warehouse, string $sku): ?\App\Entity\InventoryItem
    {
        foreach ($warehouse->getContents() as $item) {
            if ($item->getSku() === $sku) {
                return $item;
            }
        }

        return null;
    }

    /**
     * debug
     */
    public function printWarehouses(): void
    {
        foreach ($this->warehouses as $warehouse) {
            echo "📦 {$warehouse->getName()} "
                . "({$warehouse->getFreeCapacity()} free / {$warehouse->getCapacity()} total):\n";
            foreach ($warehouse->getContents() as $item) {
                echo " - {$item->getSku()} ({$item->getQuantity()} db)\n";
            }
        }
    }
}
