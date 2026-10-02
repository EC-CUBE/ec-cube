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
            $result['created'] = (int) $conn->fetchOne(
                'SELECT COUNT(*) FROM dtb_product_class pc
                  WHERE NOT EXISTS (SELECT 1 FROM dtb_product_stock ps WHERE ps.product_class_id = pc.id)'
            );
            $result['deduplicated'] = (int) $conn->fetchOne(
                'SELECT COUNT(*) FROM (SELECT product_class_id FROM dtb_product_stock
                  WHERE product_class_id IS NOT NULL GROUP BY product_class_id HAVING COUNT(*) > 1) duplicated'
            );
            $result['mismatched'] = $this->logLegacyStockMismatches();

            foreach ($this->getSyncFromLegacyStockColumnSql() as $sql) {
                $conn->executeStatement($sql);
            }
        });

        if ($result['created'] > 0 || $result['deduplicated'] > 0) {
            log_info('[ProductStockSynchronizer] dtb_product_stock を補完しました', $result);
        }

        return $result;
    }

    /**
     * 旧列 dtb_product_class.stock から dtb_product_stock を補完する SQL を返す.
     *
     * データを読まずに SQL だけで完結させる. マイグレーションの --dry-run / --write-sql でも同じ処理を出力するため.
     * 旧列が存在することを前提とする.
     *
     * @return list<string>
     */
    public function getSyncFromLegacyStockColumnSql(): array
    {
        // 在庫無制限時は null
        $legacyStock = 'CASE WHEN pc.stock_unlimited THEN NULL ELSE pc.stock END';

        return [
            // 重複行を旧列の値にそろえる
            // (MySQL は更新対象のテーブルをサブクエリで直接参照できないため, 集計した派生テーブルを経由する)
            'UPDATE dtb_product_stock
                SET stock = (SELECT '.$legacyStock.' FROM dtb_product_class pc WHERE pc.id = dtb_product_stock.product_class_id),
                    update_date = CURRENT_TIMESTAMP
              WHERE product_class_id IN (
                    SELECT product_class_id FROM (
                        SELECT product_class_id FROM dtb_product_stock
                         WHERE product_class_id IS NOT NULL GROUP BY product_class_id HAVING COUNT(*) > 1
                    ) duplicated
              )',
            // 重複行を 1 行にまとめる
            'DELETE FROM dtb_product_stock
              WHERE product_class_id IS NOT NULL
                AND id NOT IN (
                    SELECT id FROM (
                        SELECT MIN(id) AS id FROM dtb_product_stock
                         WHERE product_class_id IS NOT NULL GROUP BY product_class_id
                    ) kept
                )',
            // 行の無い規格は旧列の値で作成する
            'INSERT INTO dtb_product_stock (product_class_id, creator_id, stock, create_date, update_date, discriminator_type)
             SELECT pc.id, NULL, '.$legacyStock.', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, \'productstock\'
               FROM dtb_product_class pc
              WHERE NOT EXISTS (SELECT 1 FROM dtb_product_stock ps WHERE ps.product_class_id = pc.id)',
        ];
    }

    /**
     * 旧列 dtb_product_class.stock と dtb_product_stock.stock のずれをログに残す. DB は変更しない.
     *
     * 重複行のある規格は, 補完で旧列の値にそろえるため対象外とする.
     * 旧列が存在することを前提とする.
     *
     * @return int ずれている規格の数
     */
    public function logLegacyStockMismatches(): int
    {
        $mismatched = $this->connection->fetchAllAssociative(
            'SELECT pc.id, pc.stock AS product_class_stock, ps.stock AS product_stock_stock
               FROM dtb_product_class pc
               JOIN dtb_product_stock ps ON ps.product_class_id = pc.id
              WHERE pc.stock_unlimited = ?
                AND (SELECT COUNT(*) FROM dtb_product_stock ps2 WHERE ps2.product_class_id = pc.id) = 1
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
        }

        return count($mismatched);
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

        return (int) $this->connection->executeStatement($this->getRecalculateInStockSql());
    }

    /**
     * dtb_product_class.in_stock を再計算する SQL を返す. 値が変わる行だけを更新する.
     *
     * in_stock 列が存在することを前提とする.
     */
    public function getRecalculateInStockSql(): string
    {
        // 在庫無制限, または在庫数が 1 以上
        $inStock = '(dtb_product_class.stock_unlimited OR EXISTS (SELECT 1 FROM dtb_product_stock ps WHERE ps.product_class_id = dtb_product_class.id AND ps.stock >= 1))';

        return 'UPDATE dtb_product_class SET in_stock = '.$inStock.' WHERE in_stock <> '.$inStock;
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
}
