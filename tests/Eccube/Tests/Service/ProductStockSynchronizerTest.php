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
    use ProductStockConnectionTrait;

    public function testSyncCreatesMissingProductStock(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 5, 'stock_unlimited' => 0]);
        $conn->insert('dtb_product_class', ['id' => 2, 'stock' => null, 'stock_unlimited' => 1]);

        $result = (new ProductStockSynchronizer($conn))->syncFromLegacyStockColumn();

        $this->assertSame(['created' => 2, 'deduplicated' => 0, 'mismatched' => 0, 'overwritten' => 0], $result);
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

        $this->assertSame(['created' => 0, 'deduplicated' => 1, 'mismatched' => 0, 'overwritten' => 0], $result);
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

        $this->assertSame(['created' => 0, 'deduplicated' => 0, 'mismatched' => 1, 'overwritten' => 0], $result);
        $this->assertSame([['product_class_id' => 1, 'stock' => '6'], ['product_class_id' => 2, 'stock' => '4']], $this->fetchProductStocks($conn));
    }

    public function testSyncDoesNothingWithoutLegacyColumn(): void
    {
        $result = (new ProductStockSynchronizer($this->entityManager->getConnection()))->syncFromLegacyStockColumn();

        $this->assertSame(['created' => 0, 'deduplicated' => 0, 'mismatched' => 0, 'overwritten' => 0], $result);
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

    public function testSyncOverwritesWithLegacyValueWhenPreferred(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 10, 'stock_unlimited' => 0]);
        $this->insertProductStock($conn, 1, '6');

        $result = (new ProductStockSynchronizer($conn))->syncFromLegacyStockColumn(preferLegacy: true);

        $this->assertSame(['created' => 0, 'deduplicated' => 0, 'mismatched' => 0, 'overwritten' => 1], $result);
        $this->assertSame([['product_class_id' => 1, 'stock' => '10']], $this->fetchProductStocks($conn));
    }

    public function testDiagnoseWithLegacyColumn(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 5, 'stock_unlimited' => 0, 'in_stock' => 1]);
        $conn->insert('dtb_product_class', ['id' => 2, 'stock' => 7, 'stock_unlimited' => 0, 'in_stock' => 1]);
        $conn->insert('dtb_product_class', ['id' => 3, 'stock' => 0, 'stock_unlimited' => 0, 'in_stock' => 0]);
        $conn->insert('dtb_product_class', ['id' => 4, 'stock' => 2, 'stock_unlimited' => 0, 'in_stock' => 0]);
        $this->insertProductStock($conn, 2, '3');
        $this->insertProductStock($conn, 2, '9');
        $this->insertProductStock($conn, 3, '4');
        $this->insertProductStock($conn, 4, '2');

        $diagnosis = (new ProductStockSynchronizer($conn))->diagnose();

        $this->assertSame([1], $diagnosis['missing']);
        $this->assertSame([2], $diagnosis['duplicated']);
        // 1: 行が無く在庫なし扱い, 3: 在庫 4 なのに 0, 4: 在庫 2 なのに 0
        $this->assertSame([1, 3, 4], $diagnosis['in_stock_mismatched']);
        $this->assertSame([['product_class_id' => 3, 'product_class_stock' => '0', 'product_stock_stock' => '4']], $diagnosis['legacy_mismatched']);
    }

    public function testDiagnoseWithoutLegacyColumn(): void
    {
        $conn = $this->createLegacyConnection(withLegacy: false);
        $conn->insert('dtb_product_class', ['id' => 1, 'stock_unlimited' => 0, 'in_stock' => 0]);
        $this->insertProductStock($conn, 1, '0');

        $diagnosis = (new ProductStockSynchronizer($conn))->diagnose();

        $this->assertSame(['missing' => [], 'duplicated' => [], 'in_stock_mismatched' => [], 'legacy_mismatched' => null], $diagnosis);
    }

    /**
     * 旧列が無い場合, 行の無い規格は在庫 0 (在庫無制限なら NULL), 重複行は最も少ない在庫数でまとめる.
     */
    public function testRepairWithoutLegacyColumn(): void
    {
        $conn = $this->createLegacyConnection(withLegacy: false);
        $conn->insert('dtb_product_class', ['id' => 1, 'stock_unlimited' => 0, 'in_stock' => 1]);
        $conn->insert('dtb_product_class', ['id' => 2, 'stock_unlimited' => 1, 'in_stock' => 0]);
        $conn->insert('dtb_product_class', ['id' => 3, 'stock_unlimited' => 0, 'in_stock' => 0]);
        $this->insertProductStock($conn, 3, '8');
        $this->insertProductStock($conn, 3, '5');

        $result = (new ProductStockSynchronizer($conn))->repair();

        $this->assertSame(['created' => 2, 'deduplicated' => 1, 'mismatched' => 0, 'overwritten' => 0, 'in_stock_updated' => 3], $result);
        $this->assertSame([
            ['product_class_id' => 1, 'stock' => '0'],
            ['product_class_id' => 2, 'stock' => null],
            ['product_class_id' => 3, 'stock' => '5'],
        ], $this->fetchProductStocks($conn));
        $this->assertSame([1 => 0, 2 => 1, 3 => 1], $this->fetchInStocks($conn));

        $diagnosis = (new ProductStockSynchronizer($conn))->diagnose();
        $this->assertSame([], $diagnosis['missing']);
        $this->assertSame([], $diagnosis['duplicated']);
        $this->assertSame([], $diagnosis['in_stock_mismatched']);
    }

    public function testRepairWithLegacyColumn(): void
    {
        $conn = $this->createLegacyConnection();
        $conn->insert('dtb_product_class', ['id' => 1, 'stock' => 5, 'stock_unlimited' => 0, 'in_stock' => 0]);
        $conn->insert('dtb_product_class', ['id' => 2, 'stock' => 10, 'stock_unlimited' => 0, 'in_stock' => 1]);
        $this->insertProductStock($conn, 2, '0');

        $result = (new ProductStockSynchronizer($conn))->repair(preferLegacy: true);

        $this->assertSame(['created' => 1, 'deduplicated' => 0, 'mismatched' => 0, 'overwritten' => 1, 'in_stock_updated' => 1], $result);
        $this->assertSame([['product_class_id' => 1, 'stock' => '5'], ['product_class_id' => 2, 'stock' => '10']], $this->fetchProductStocks($conn));
        $this->assertSame([1 => 1, 2 => 1], $this->fetchInStocks($conn));
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
}
