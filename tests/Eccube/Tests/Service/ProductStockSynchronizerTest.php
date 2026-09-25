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

namespace Eccube\Tests\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Eccube\Entity\ProductClass;
use Eccube\Service\ProductStockSynchronizer;
use Eccube\Tests\EccubeTestCase;

/**
 * ProductStockSynchronizer のテスト.
 *
 * 旧列 (dtb_product_class.stock) を持つスキーマは, 本体のテスト DB へ DDL を流さずに済むよう
 * インメモリの SQLite に最小構成で再現する.
 */
final class ProductStockSynchronizerTest extends EccubeTestCase
{
    public function testSyncCreatesMissingProductStock(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 5, 'stock_unlimited' => 0]);
        $conn->insert('dtb_product_class', ['id' => 2, 'stock' => null, 'stock_unlimited' => 1]);

        $result = (new ProductStockSynchronizer($conn))->syncFromLegacyStockColumn();

        $this->assertSame(['created' => 2, 'deduplicated' => 0, 'mismatched' => 0], $result);
        $this->assertSame([['product_class_id' => 1, 'stock' => '5'], ['product_class_id' => 2, 'stock' => null]], $this->fetchProductStocks($conn));
    }

    /**
     * 重複行は旧列の値で 1 行にまとめる (Version20230928014611 と同じ扱い).
     */
    public function testSyncDeduplicatesWithLegacyValue(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 7, 'stock_unlimited' => 0]);
        $this->insertProductStock($conn, 1, '3');
        $this->insertProductStock($conn, 1, '9');

        $result = (new ProductStockSynchronizer($conn))->syncFromLegacyStockColumn();

        $this->assertSame(['created' => 0, 'deduplicated' => 1, 'mismatched' => 0], $result);
        $this->assertSame([['product_class_id' => 1, 'stock' => '7']], $this->fetchProductStocks($conn));
    }

    /**
     * 値がずれている場合は dtb_product_stock を正とし, 書き換えない.
     */
    public function testSyncKeepsProductStockWhenMismatched(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 10, 'stock_unlimited' => 0]);
        $conn->insert('dtb_product_class', ['id' => 2, 'stock' => 4, 'stock_unlimited' => 0]);
        $this->insertProductStock($conn, 1, '6');
        $this->insertProductStock($conn, 2, '4');

        $result = (new ProductStockSynchronizer($conn))->syncFromLegacyStockColumn();

        $this->assertSame(['created' => 0, 'deduplicated' => 0, 'mismatched' => 1], $result);
        $this->assertSame([['product_class_id' => 1, 'stock' => '6'], ['product_class_id' => 2, 'stock' => '4']], $this->fetchProductStocks($conn));
    }

    public function testSyncDoesNothingWithoutLegacyColumn(): void
    {
        $result = (new ProductStockSynchronizer($this->entityManager->getConnection()))->syncFromLegacyStockColumn();

        $this->assertSame(['created' => 0, 'deduplicated' => 0, 'mismatched' => 0], $result);
    }

    public function testHasLegacyStockColumn(): void
    {
        $this->assertTrue((new ProductStockSynchronizer($this->createLegacyConnection()))->hasLegacyStockColumn());
        $this->assertFalse((new ProductStockSynchronizer($this->createLegacyConnection(withLegacy: false)))->hasLegacyStockColumn());
        $this->assertFalse((new ProductStockSynchronizer($this->entityManager->getConnection()))->hasLegacyStockColumn());
    }

    public function testRecalculateInStockOnLegacySchema(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 5, 'stock_unlimited' => 0, 'in_stock' => 0]);
        $conn->insert('dtb_product_class', ['id' => 2, 'stock' => 0, 'stock_unlimited' => 0, 'in_stock' => 1]);
        $conn->insert('dtb_product_class', ['id' => 3, 'stock' => null, 'stock_unlimited' => 1, 'in_stock' => 0]);

        $synchronizer = new ProductStockSynchronizer($conn);
        $synchronizer->syncFromLegacyStockColumn();

        $this->assertSame(3, $synchronizer->recalculateInStock());
        $this->assertSame([1 => 1, 2 => 0, 3 => 1], $this->fetchInStocks($conn));
    }

    public function testRecalculateInStock(): void
    {
        $Product = $this->createProduct('recalculate-in-stock', 0);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock('3');
        $this->entityManager->flush();

        $conn = $this->entityManager->getConnection();
        // DQL の一括更新やネイティブ SQL で in_stock がずれた状態を再現する
        $conn->executeStatement('UPDATE dtb_product_class SET in_stock = ? WHERE id = ?', [false, $ProductClass->getId()], ['boolean']);

        $updated = (new ProductStockSynchronizer($conn))->recalculateInStock();

        $this->assertGreaterThanOrEqual(1, $updated);
        $value = $conn->fetchOne('SELECT in_stock FROM dtb_product_class WHERE id = ?', [$ProductClass->getId()]);
        $this->assertContains($value, [true, 1, '1', 't', 'true']);
        // 正しい行は更新しない
        $this->assertSame(0, (new ProductStockSynchronizer($conn))->recalculateInStock());
    }

    private function createLegacyConnection(bool $withLegacy = true, bool $withInStock = true): Connection
    {
        $conn = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $columns = ['id INTEGER PRIMARY KEY NOT NULL', 'stock_unlimited BOOLEAN DEFAULT 0 NOT NULL'];
        if ($withLegacy) {
            $columns[] = 'stock NUMERIC(10, 0) DEFAULT NULL';
        }
        if ($withInStock) {
            $columns[] = 'in_stock BOOLEAN DEFAULT 0 NOT NULL';
        }
        $conn->executeStatement('CREATE TABLE dtb_product_class ('.implode(', ', $columns).')');
        $conn->executeStatement('CREATE TABLE dtb_product_stock (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            stock NUMERIC(10, 0) DEFAULT NULL,
            create_date DATETIME NOT NULL,
            update_date DATETIME NOT NULL,
            product_class_id INTEGER DEFAULT NULL,
            creator_id INTEGER DEFAULT NULL,
            discriminator_type VARCHAR(255) NOT NULL
        )');

        return $conn;
    }

    private function insertProductStock(Connection $conn, int $productClassId, ?string $stock): void
    {
        $conn->insert('dtb_product_stock', [
            'product_class_id' => $productClassId,
            'stock' => $stock,
            'create_date' => '2026-01-01 00:00:00',
            'update_date' => '2026-01-01 00:00:00',
            'discriminator_type' => 'productstock',
        ]);
    }

    /**
     * @return list<array{product_class_id: int, stock: string|null}>
     */
    private function fetchProductStocks(Connection $conn): array
    {
        return array_map(
            fn (array $row) => ['product_class_id' => (int) $row['product_class_id'], 'stock' => $row['stock'] === null ? null : (string) $row['stock']],
            $conn->fetchAllAssociative('SELECT product_class_id, stock FROM dtb_product_stock ORDER BY product_class_id, id')
        );
    }

    /**
     * @return array<int, int>
     */
    private function fetchInStocks(Connection $conn): array
    {
        return array_map(intval(...), $conn->fetchAllKeyValue('SELECT id, in_stock FROM dtb_product_class ORDER BY id'));
    }
}
