<?php

declare(strict_types=1);

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Command;

use Doctrine\DBAL\Connection;
use Eccube\Command\DoctorProductStockCommand;
use Eccube\Command\ProductStockSyncCommand;
use Eccube\Service\ProductStockSynchronizer;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Service\ProductStockConnectionTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * eccube:doctor:product-stock / eccube:product-stock:sync の出力と終了コードを検証する.
 */
final class ProductStockCommandTest extends EccubeTestCase
{
    use ProductStockConnectionTrait;

    public function testDoctorReturnsSuccessWhenConsistent(): void
    {
        $conn = $this->createLegacyConnection(withLegacy: false);
        $conn->insert('dtb_product_class', ['id' => 1, 'stock_unlimited' => 0, 'in_stock' => 1]);
        $this->insertProductStock($conn, 1, '3');

        $tester = $this->doctor($conn);

        $this->assertSame(0, $tester->execute([]));
        $this->assertStringContainsString('不整合はありません', $tester->getDisplay());
    }

    public function testDoctorReturnsFailureWhenInconsistent(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 5, 'stock_unlimited' => 0, 'in_stock' => 1]);
        $conn->insert('dtb_product_class', ['id' => 2, 'stock' => 0, 'stock_unlimited' => 0, 'in_stock' => 0]);
        $this->insertProductStock($conn, 2, '4');

        $tester = $this->doctor($conn);

        $this->assertSame(1, $tester->execute([]));
        $display = $tester->getDisplay();
        $this->assertStringContainsString('eccube:product-stock:sync', $display);
        $this->assertStringContainsString('--source=product-class', $display);
    }

    public function testDoctorJson(): void
    {
        $conn = $this->createLegacyConnection(withLegacy: false);
        $conn->insert('dtb_product_class', ['id' => 1, 'stock_unlimited' => 0, 'in_stock' => 1]);

        $tester = $this->doctor($conn);

        $this->assertSame(1, $tester->execute(['--format' => 'json']));
        $this->assertSame([
            'missing' => [1],
            'duplicated' => [],
            'in_stock_mismatched' => [1],
            'legacy_mismatched' => null,
            'ok' => false,
        ], json_decode($tester->getDisplay(), true));
    }

    public function testDoctorRejectsInvalidFormat(): void
    {
        $this->assertSame(Command::INVALID, $this->doctor($this->createLegacyConnection())->execute(['--format' => 'xml']));
    }

    public function testSyncRepairs(): void
    {
        $conn = $this->createLegacyConnection(withLegacy: false);
        $conn->insert('dtb_product_class', ['id' => 1, 'stock_unlimited' => 0, 'in_stock' => 1]);

        $tester = $this->sync($conn);

        $this->assertSame(0, $tester->execute([]));
        $this->assertSame([['product_class_id' => 1, 'stock' => '0']], $this->fetchProductStocks($conn));
        $this->assertSame([1 => 0], $this->fetchInStocks($conn));
    }

    public function testSyncDryRunDoesNotChangeDatabase(): void
    {
        $conn = $this->createLegacyConnection(withLegacy: false);
        $conn->insert('dtb_product_class', ['id' => 1, 'stock_unlimited' => 0, 'in_stock' => 1]);

        $tester = $this->sync($conn);

        $this->assertSame(0, $tester->execute(['--dry-run' => true]));
        $this->assertStringContainsString('dry-run', $tester->getDisplay());
        $this->assertSame([], $this->fetchProductStocks($conn));
        $this->assertSame([1 => 1], $this->fetchInStocks($conn));
    }

    public function testSyncWithProductClassSource(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 10, 'stock_unlimited' => 0, 'in_stock' => 1]);
        $this->insertProductStock($conn, 1, '0');

        $tester = $this->sync($conn);

        $this->assertSame(0, $tester->execute(['--source' => 'product-class']));
        $this->assertSame([['product_class_id' => 1, 'stock' => '10']], $this->fetchProductStocks($conn));
    }

    public function testSyncRejectsProductClassSourceWithoutLegacyColumn(): void
    {
        $tester = $this->sync($this->createLegacyConnection(withLegacy: false));

        $this->assertSame(Command::INVALID, $tester->execute(['--source' => 'product-class']));
    }

    public function testSyncRejectsInvalidSource(): void
    {
        $tester = $this->sync($this->createLegacyConnection());

        $this->assertSame(Command::INVALID, $tester->execute(['--source' => 'unknown']));
    }

    private function doctor(Connection $conn): CommandTester
    {
        return new CommandTester(new DoctorProductStockCommand(new ProductStockSynchronizer($conn)));
    }

    private function sync(Connection $conn): CommandTester
    {
        return new CommandTester(new ProductStockSyncCommand(new ProductStockSynchronizer($conn)));
    }
}
