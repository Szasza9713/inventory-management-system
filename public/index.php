<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\Brand;
use App\Entity\Product\Chair;
use App\Entity\Product\Laptop;
use App\Entity\Product\TV;
use App\Entity\Warehouse;
use App\Service\WarehouseManager;
use App\Exception\DomainException;

echo "Inventory Manager running...\n";

$brand = new Brand('TechPro', 4);

// Termékek
$laptop = new Laptop('LAP123', 'UltraBook', 1200.00, $brand, 16);
$tv = new TV('TV456', 'SmartTV 55"', 900.00, $brand, 55);
$chair = new Chair('CHA789', 'ErgoChair', 200.00, $brand, 'Leather');

// Raktárak
$warehouse1 = new Warehouse('Budapest Raktár', '1111 Budapest, Raktár u. 1.', 10);
$warehouse2 = new Warehouse('Debrecen Raktár', '4024 Debrecen, Tároló krt. 2.', 8);

// Kezelő
$manager = new WarehouseManager();
$manager->addWarehouse($warehouse1);
$manager->addWarehouse($warehouse2);

// Hozzáadás – többet mint az első raktár tudna fogadni
try {
    echo "\n🟢 Hozzáadás 12 db Laptop...\n";
    $manager->addProduct($laptop, 12); // első raktár: 10, második: 2
} catch (DomainException $e) {
    echo "❌ Hiba: " . $e->getMessage() . PHP_EOL;
}

// Hozzáadás – teljes kapacitáson túl
try {
    echo "\n🟢 Hozzáadás 10 db TV...\n";
    $manager->addProduct($tv, 10); // túl sok → kivétel
} catch (DomainException $e) {
    echo "❌ Hiba: " . $e->getMessage() . PHP_EOL;
}

// Kivétel – több raktárból együtt
try {
    echo "\n🔻 Kivétel 5 db Laptop...\n";
    $manager->removeProduct('LAP123', 5);
} catch (DomainException $e) {
    echo "❌ Hiba: " . $e->getMessage() . PHP_EOL;
}

// Kiírás
echo "\n📦 Raktárak tartalma:\n";
$manager->printWarehouses();
