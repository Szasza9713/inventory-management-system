<?php

declare(strict_types=1);

use App\Entity\Product\Laptop;
use App\Entity\Product\TV;
use App\Exception\Domain\InsufficientProductQuantityException;
use App\Exception\Domain\WarehouseCapacityExceededException;
use PHPUnit\Framework\TestCase;
use App\Entity\Brand;
use App\Entity\Warehouse;
use App\Service\WarehouseManager;

class WarehouseManagerTest extends TestCase
{
    private Warehouse $warehouse1;
    private Warehouse $warehouse2;
    private WarehouseManager $manager;
    private Laptop $laptop;
    private TV $tv;

    protected function setUp(): void
    {
        $brand = new Brand('ProBrand', 5);
        $this->laptop = new Laptop('LAP123', 'LaptopX', 999.99, $brand, 8);
        $this->tv = new TV('TV456', 'SuperTV', 699.99, $brand, 55);

        $this->warehouse1 = new Warehouse('Raktár 1', '1111 BP', 5);
        $this->warehouse2 = new Warehouse('Raktár 2', '2222 Pécs', 5);

        $this->manager = new WarehouseManager();
        $this->manager->addWarehouse($this->warehouse1);
        $this->manager->addWarehouse($this->warehouse2);
    }

    public function testAddMultipleProductsAndListContents(): void
    {
        $brand = new Brand('FlexBrand', 3);
        $laptop = new Laptop('L1', 'Laptop Basic', 499.99, $brand, 8);
        $tv = new TV('T1', 'SmartTV', 799.99, $brand, 50);

        $warehouse = new Warehouse('Teszt Raktár', '1000 Valahol', 10);
        $manager = new WarehouseManager();
        $manager->addWarehouse($warehouse);

        $manager->addProduct($laptop, 3);
        $manager->addProduct($tv, 2);

        $contents = $warehouse->getContents();

        $this->assertCount(2, $contents);

        $skuMap = [];
        foreach ($contents as $item) {
            $skuMap[$item->getSku()] = $item->getQuantity();
        }

        $this->assertEquals(3, $skuMap['L1']);
        $this->assertEquals(2, $skuMap['T1']);
    }

    public function testAddProductAcrossMultipleWarehouses(): void
    {
        $this->manager->addProduct($this->laptop, 8);

        $this->assertEquals(5, $this->warehouse1->getContents()[0]->getQuantity());
        $this->assertEquals(3, $this->warehouse2->getContents()[0]->getQuantity());
    }

    public function testAddProductFailsIfNotEnoughSpace(): void
    {
        $this->expectException(WarehouseCapacityExceededException::class);

        $this->manager->addProduct($this->laptop, 11); // 5+5 = 10 hely van
    }

    public function testRemoveProductAcrossMultipleWarehouses(): void
    {
        $this->manager->addProduct($this->tv, 6);
        $this->manager->removeProduct('TV456', 6);

        $this->assertSame([], $this->warehouse1->getContents());
        $this->assertSame([], $this->warehouse2->getContents());
    }

    public function testRemoveProductFailsIfNotEnoughAvailable(): void
    {
        $this->manager->addProduct($this->tv, 4);

        $this->expectException(InsufficientProductQuantityException::class);
        $this->manager->removeProduct('TV456', 6);
    }
}
