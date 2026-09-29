<?php

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

namespace Eccube\Service;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

/**
 * 在庫数の正典 (dtb_product_stock.stock) と dtb_product_class の整合を取る.
 *
 * 在庫数は 4.3 まで dtb_product_class.stock と dtb_product_stock.stock に二重に保持されていた.
 * 4.4 で dtb_product_class.stock (旧列) はマッピングから外れる.
 * 旧列はスキーマ更新では削除せず (LegacyProductClassStockColumnSubscriber),
 * マイグレーションで dtb_product_stock を補完してから削除する.
 */
class ProductStockSynchronizer
{
    private const PRODUCT_CLASS_TABLE = 'dtb_product_class';

    private const LEGACY_STOCK_COLUMN = 'stock';

    private const IN_STOCK_COLUMN = 'in_stock';

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * 旧列 dtb_product_class.stock が残っているかどうか.
     */
    public function hasLegacyStockColumn(): bool
    {
        return in_array(self::LEGACY_STOCK_COLUMN, $this->getProductClassColumns(), true);
    }

    /**
     * 旧列 dtb_product_class.stock から dtb_product_stock を補完する.
     *
     * - dtb_product_stock の行が無い規格は, 旧列の値で行を作成する
     * - dtb_product_stock の行が重複している規格は, 旧列の値で 1 行にまとめる (Version20230928014611 と同じ扱い)
     * - 値がずれている規格は dtb_product_stock を正とし, ずれをログに残す
     *
     * 旧列が存在しない場合は何もしない.
     *
     * @return array{created: int, deduplicated: int, mismatched: int}
     */
    public function syncFromLegacyStockColumn(): array
    {
        $result = ['created' => 0, 'deduplicated' => 0, 'mismatched' => 0];

        if (!$this->hasLegacyStockColumn()) {
            return $result;
        }

        $this->connection->transactional(function (Connection $conn) use (&$result): void {
            $missing = $conn->fetchAllAssociative(
                'SELECT pc.id, pc.stock, pc.stock_unlimited FROM dtb_product_class pc
                  WHERE NOT EXISTS (SELECT 1 FROM dtb_product_stock ps WHERE ps.product_class_id = pc.id)'
            );
            foreach ($missing as $row) {
                $this->insertProductStock($conn, (int) $row['id'], $this->legacyStock($row));
                $result['created']++;
            }

            $duplicated = $conn->fetchAllAssociative(
                'SELECT pc.id, pc.stock, pc.stock_unlimited FROM dtb_product_class pc
                  WHERE (SELECT COUNT(*) FROM dtb_product_stock ps WHERE ps.product_class_id = pc.id) > 1'
            );
            foreach ($duplicated as $row) {
                $conn->executeStatement('DELETE FROM dtb_product_stock WHERE product_class_id = ?', [(int) $row['id']]);
                $this->insertProductStock($conn, (int) $row['id'], $this->legacyStock($row));
                $result['deduplicated']++;
            }

            $mismatched = $conn->fetchAllAssociative(
                'SELECT pc.id, pc.stock AS product_class_stock, ps.stock AS product_stock_stock
                   FROM dtb_product_class pc
                   JOIN dtb_product_stock ps ON ps.product_class_id = pc.id
                  WHERE pc.stock_unlimited = ?
                    AND (pc.stock <> ps.stock OR (pc.stock IS NULL AND ps.stock IS NOT NULL) OR (pc.stock IS NOT NULL AND ps.stock IS NULL))',
                [false],
                ['boolean']
            );
            foreach ($mismatched as $row) {
                log_warning('[ProductStockSynchronizer] dtb_product_class.stock と dtb_product_stock.stock がずれているため dtb_product_stock を正とします', [
                    'product_class_id' => (int) $row['id'],
                    'dtb_product_class.stock' => $row['product_class_stock'],
                    'dtb_product_stock.stock' => $row['product_stock_stock'],
                ]);
                $result['mismatched']++;
            }
        });

        if ($result['created'] > 0 || $result['deduplicated'] > 0) {
            log_info('[ProductStockSynchronizer] dtb_product_stock を補完しました', $result);
        }

        return $result;
    }

    /**
     * dtb_product_class.in_stock を dtb_product_stock.stock と在庫無制限フラグから再計算する.
     *
     * in_stock 列が存在しない場合は何もしない.
     *
     * @return int 更新した行数
     */
    public function recalculateInStock(): int
    {
        if (!in_array(self::IN_STOCK_COLUMN, $this->getProductClassColumns(), true)) {
            return 0;
        }

        // 在庫無制限, または在庫数が 1 以上
        $inStock = '(pc.stock_unlimited OR EXISTS (SELECT 1 FROM dtb_product_stock ps WHERE ps.product_class_id = pc.id AND ps.stock >= 1))';

        // 値が変わる行だけを更新する
        $rows = $this->connection->fetchAllAssociative(
            'SELECT pc.id, '.$inStock.' AS in_stock FROM dtb_product_class pc WHERE pc.in_stock <> '.$inStock
        );

        $idsByValue = [0 => [], 1 => []];
        foreach ($rows as $row) {
            // ドライバにより true / 1 / 't' のいずれかで返る
            $value = in_array($row['in_stock'], [true, 1, '1', 't', 'true'], true) ? 1 : 0;
            $idsByValue[$value][] = (int) $row['id'];
        }

        $updated = 0;
        foreach ($idsByValue as $value => $ids) {
            foreach (array_chunk($ids, 500) as $chunk) {
                $updated += (int) $this->connection->executeStatement(
                    'UPDATE dtb_product_class SET in_stock = ? WHERE id IN (?)',
                    [(bool) $value, $chunk],
                    ['boolean', ArrayParameterType::INTEGER]
                );
            }
        }

        return $updated;
    }

    /**
     * @return list<string> dtb_product_class の列名 (小文字). テーブルが無い場合は空配列
     */
    private function getProductClassColumns(): array
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tableExists(self::PRODUCT_CLASS_TABLE)) {
            return [];
        }

        $columns = [];
        foreach ($schemaManager->introspectTableColumnsByUnquotedName(self::PRODUCT_CLASS_TABLE) as $column) {
            $columns[] = strtolower($column->getObjectName()->getIdentifier()->getValue());
        }

        return $columns;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function legacyStock(array $row): ?string
    {
        if (in_array($row['stock_unlimited'], [true, 1, '1', 't', 'true'], true)) {
            // 在庫無制限時はnullを設定
            return null;
        }

        return $row['stock'] === null ? null : (string) $row['stock'];
    }

    private function insertProductStock(Connection $conn, int $productClassId, ?string $stock): void
    {
        $now = new \DateTime();
        $conn->insert('dtb_product_stock', [
            'product_class_id' => $productClassId,
            'creator_id' => null,
            'stock' => $stock,
            'create_date' => $now,
            'update_date' => $now,
            'discriminator_type' => 'productstock',
        ], [
            'create_date' => 'datetimetz',
            'update_date' => 'datetimetz',
        ]);
    }
}
