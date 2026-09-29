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

    /**
     * 在庫の有無 (在庫無制限, または在庫数が 1 以上) を求める SQL の式.
     */
    private const IN_STOCK_EXPRESSION = '(pc.stock_unlimited OR EXISTS (SELECT 1 FROM dtb_product_stock ps WHERE ps.product_class_id = pc.id AND ps.stock >= 1))';

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
     * 在庫の整合を診断する. DB は変更しない.
     *
     * - missing: dtb_product_stock の行が無い規格の ID
     * - duplicated: dtb_product_stock の行が重複している規格の ID
     * - in_stock_mismatched: in_stock が在庫数と合っていない規格の ID (in_stock 列が無い場合は null)
     * - legacy_mismatched: 旧列と dtb_product_stock の値がずれている規格 (旧列が無い場合は null)
     *
     * @return array{
     *     missing: list<int>,
     *     duplicated: list<int>,
     *     in_stock_mismatched: list<int>|null,
     *     legacy_mismatched: list<array{product_class_id: int, product_class_stock: string|null, product_stock_stock: string|null}>|null
     * }
     */
    public function diagnose(): array
    {
        $columns = $this->getProductClassColumns();

        $inStockMismatched = null;
        if (in_array(self::IN_STOCK_COLUMN, $columns, true)) {
            $inStockMismatched = array_map(intval(...), $this->connection->fetchFirstColumn(
                'SELECT pc.id FROM dtb_product_class pc WHERE pc.in_stock <> '.self::IN_STOCK_EXPRESSION.' ORDER BY pc.id'
            ));
        }

        $legacyMismatched = null;
        if (in_array(self::LEGACY_STOCK_COLUMN, $columns, true)) {
            $legacyMismatched = array_map(fn (array $row): array => [
                'product_class_id' => (int) $row['id'],
                'product_class_stock' => $row['product_class_stock'] === null ? null : (string) $row['product_class_stock'],
                'product_stock_stock' => $row['product_stock_stock'] === null ? null : (string) $row['product_stock_stock'],
            ], $this->findLegacyMismatched($this->connection));
        }

        return [
            'missing' => array_map(fn (array $row): int => (int) $row['id'], $this->findMissing($this->connection)),
            'duplicated' => array_map(fn (array $row): int => (int) $row['id'], $this->findDuplicated($this->connection)),
            'in_stock_mismatched' => $inStockMismatched,
            'legacy_mismatched' => $legacyMismatched,
        ];
    }

    /**
     * 在庫の不整合を修復する.
     *
     * 旧列が残っている場合は syncFromLegacyStockColumn() で補完する. 旧列が無い場合は次のとおり補完する.
     * - dtb_product_stock の行が無い規格は, 在庫数 0 (在庫無制限なら null) で作成する
     * - dtb_product_stock の行が重複している規格は, 最も少ない在庫数で 1 行にまとめる (売り越しを防ぐため)
     *
     * 最後に in_stock を再計算する.
     *
     * @param bool $preferLegacy 旧列と値がずれている場合に旧列の値を採用するかどうか
     *
     * @return array{created: int, deduplicated: int, mismatched: int, overwritten: int, in_stock_updated: int}
     */
    public function repair(bool $preferLegacy = false): array
    {
        if ($this->hasLegacyStockColumn()) {
            $result = $this->syncFromLegacyStockColumn($preferLegacy);
        } else {
            $result = ['created' => 0, 'deduplicated' => 0, 'mismatched' => 0, 'overwritten' => 0];
            $this->connection->transactional(function (Connection $conn) use (&$result): void {
                foreach ($this->findMissing($conn) as $row) {
                    $this->insertProductStock($conn, (int) $row['id'], $this->isTrue($row['stock_unlimited']) ? null : '0');
                    $result['created']++;
                }

                foreach ($this->findDuplicated($conn) as $row) {
                    $id = (int) $row['id'];
                    $stock = $this->isTrue($row['stock_unlimited'])
                        ? null
                        : $conn->fetchOne('SELECT MIN(stock) FROM dtb_product_stock WHERE product_class_id = ?', [$id]);
                    $conn->executeStatement('DELETE FROM dtb_product_stock WHERE product_class_id = ?', [$id]);
                    $this->insertProductStock($conn, $id, $stock === null || $stock === false ? null : (string) $stock);
                    $result['deduplicated']++;
                }
            });

            if ($result['created'] > 0 || $result['deduplicated'] > 0) {
                log_info('[ProductStockSynchronizer] dtb_product_stock を補完しました', $result);
            }
        }

        $result['in_stock_updated'] = $this->recalculateInStock();

        return $result;
    }

    /**
     * 旧列 dtb_product_class.stock から dtb_product_stock を補完する.
     *
     * - dtb_product_stock の行が無い規格は, 旧列の値で行を作成する
     * - dtb_product_stock の行が重複している規格は, 旧列の値で 1 行にまとめる (Version20230928014611 と同じ扱い)
     * - 値がずれている規格は dtb_product_stock を正とし, ずれをログに残す.
     *   $preferLegacy が true の場合は旧列の値で dtb_product_stock を上書きする
     *
     * 旧列が存在しない場合は何もしない.
     *
     * @return array{created: int, deduplicated: int, mismatched: int, overwritten: int}
     */
    public function syncFromLegacyStockColumn(bool $preferLegacy = false): array
    {
        $result = ['created' => 0, 'deduplicated' => 0, 'mismatched' => 0, 'overwritten' => 0];

        if (!$this->hasLegacyStockColumn()) {
            return $result;
        }

        $this->connection->transactional(function (Connection $conn) use (&$result, $preferLegacy): void {
            foreach ($this->findMissing($conn, withLegacy: true) as $row) {
                $this->insertProductStock($conn, (int) $row['id'], $this->legacyStock($row));
                $result['created']++;
            }

            foreach ($this->findDuplicated($conn, withLegacy: true) as $row) {
                $conn->executeStatement('DELETE FROM dtb_product_stock WHERE product_class_id = ?', [(int) $row['id']]);
                $this->insertProductStock($conn, (int) $row['id'], $this->legacyStock($row));
                $result['deduplicated']++;
            }

            foreach ($this->findLegacyMismatched($conn) as $row) {
                $context = [
                    'product_class_id' => (int) $row['id'],
                    'dtb_product_class.stock' => $row['product_class_stock'],
                    'dtb_product_stock.stock' => $row['product_stock_stock'],
                ];
                if ($preferLegacy) {
                    $conn->executeStatement(
                        'UPDATE dtb_product_stock SET stock = ? WHERE product_class_id = ?',
                        [$row['product_class_stock'], (int) $row['id']]
                    );
                    log_warning('[ProductStockSynchronizer] dtb_product_class.stock の値で dtb_product_stock.stock を上書きしました', $context);
                    $result['overwritten']++;
                } else {
                    log_warning('[ProductStockSynchronizer] dtb_product_class.stock と dtb_product_stock.stock がずれているため dtb_product_stock を正とします', $context);
                    $result['mismatched']++;
                }
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

        // 値が変わる行だけを更新する
        $rows = $this->connection->fetchAllAssociative(
            'SELECT pc.id, '.self::IN_STOCK_EXPRESSION.' AS in_stock FROM dtb_product_class pc WHERE pc.in_stock <> '.self::IN_STOCK_EXPRESSION
        );

        $idsByValue = [0 => [], 1 => []];
        foreach ($rows as $row) {
            $value = $this->isTrue($row['in_stock']) ? 1 : 0;
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
     * dtb_product_stock の行が無い規格.
     *
     * @return list<array<string, mixed>>
     */
    private function findMissing(Connection $conn, bool $withLegacy = false): array
    {
        return $conn->fetchAllAssociative(
            'SELECT pc.id, pc.stock_unlimited'.($withLegacy ? ', pc.stock' : '').' FROM dtb_product_class pc
              WHERE NOT EXISTS (SELECT 1 FROM dtb_product_stock ps WHERE ps.product_class_id = pc.id)
              ORDER BY pc.id'
        );
    }

    /**
     * dtb_product_stock の行が重複している規格.
     *
     * @return list<array<string, mixed>>
     */
    private function findDuplicated(Connection $conn, bool $withLegacy = false): array
    {
        return $conn->fetchAllAssociative(
            'SELECT pc.id, pc.stock_unlimited'.($withLegacy ? ', pc.stock' : '').' FROM dtb_product_class pc
              WHERE (SELECT COUNT(*) FROM dtb_product_stock ps WHERE ps.product_class_id = pc.id) > 1
              ORDER BY pc.id'
        );
    }

    /**
     * 旧列と dtb_product_stock の値がずれている規格 (在庫無制限を除く). 重複行がある規格は除く.
     *
     * @return list<array<string, mixed>>
     */
    private function findLegacyMismatched(Connection $conn): array
    {
        return $conn->fetchAllAssociative(
            'SELECT pc.id, pc.stock AS product_class_stock, ps.stock AS product_stock_stock
               FROM dtb_product_class pc
               JOIN dtb_product_stock ps ON ps.product_class_id = pc.id
              WHERE pc.stock_unlimited = ?
                AND (pc.stock <> ps.stock OR (pc.stock IS NULL AND ps.stock IS NOT NULL) OR (pc.stock IS NOT NULL AND ps.stock IS NULL))
                AND (SELECT COUNT(*) FROM dtb_product_stock ps2 WHERE ps2.product_class_id = pc.id) = 1
              ORDER BY pc.id',
            [false],
            ['boolean']
        );
    }

    /**
     * ドライバにより true / 1 / 't' のいずれかで返る真偽値を判定する.
     */
    private function isTrue(mixed $value): bool
    {
        return in_array($value, [true, 1, '1', 't', 'true'], true);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function legacyStock(array $row): ?string
    {
        if ($this->isTrue($row['stock_unlimited'])) {
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
