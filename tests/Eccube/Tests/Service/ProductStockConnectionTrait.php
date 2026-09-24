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

/**
 * 在庫のテーブルを最小構成で持つインメモリの SQLite を用意する.
 *
 * 旧列 (dtb_product_class.stock) を持つスキーマを, 本体のテスト DB へ DDL を流さずに再現するために使う.
 */
trait ProductStockConnectionTrait
{
    protected function createLegacyConnection(bool $withLegacy = true, bool $withInStock = true): Connection
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

    protected function insertProductStock(Connection $conn, int $productClassId, ?string $stock): void
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
    protected function fetchProductStocks(Connection $conn): array
    {
        return array_map(
            fn (array $row) => ['product_class_id' => (int) $row['product_class_id'], 'stock' => $row['stock'] === null ? null : (string) $row['stock']],
            $conn->fetchAllAssociative('SELECT product_class_id, stock FROM dtb_product_stock ORDER BY product_class_id, id')
        );
    }

    /**
     * @return array<int, int>
     */
    protected function fetchInStocks(Connection $conn): array
    {
        return array_map(intval(...), $conn->fetchAllKeyValue('SELECT id, in_stock FROM dtb_product_class ORDER BY id'));
    }
}
