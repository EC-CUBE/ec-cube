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

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Eccube\Service\ProductStockSynchronizer;

/**
 * 在庫数の正典を dtb_product_stock.stock に一本化する。
 *
 * 1. 旧列 (dtb_product_class.stock) から dtb_product_stock を補完する
 *    (行の無い規格は作成し, 重複行は旧列の値で 1 行にまとめ, ずれは dtb_product_stock を正としてログに残す)
 * 2. in_stock 列が無ければ追加し, 旧列を削除する (schema:update では LegacyProductClassStockColumnSubscriber により削除されない)
 * 3. dtb_product_class.in_stock を再計算する
 *
 * schema:update と migrate のどちらを先に実行しても同じ結果になる.
 * DB の変更はすべて addSql() で登録するため, --dry-run では DB を変更せず, --write-sql にも補完と再計算が含まれる.
 */
final class Version20260924000000 extends AbstractMigration
{
    public const NAME = 'dtb_product_class';

    private const LEGACY_INDEX = 'dtb_product_class_stock_stock_unlimited_idx';

    private const IN_STOCK_INDEX = 'dtb_product_class_in_stock_idx';

    public function up(Schema $schema): void
    {
        // テーブルが存在しない場合終了
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $synchronizer = new ProductStockSynchronizer($this->connection);
        $table = $schema->getTable(self::NAME);

        if ($table->hasColumn('stock')) {
            // 読み取りのみ
            $synchronizer->logLegacyStockMismatches();

            foreach ($synchronizer->getSyncFromLegacyStockColumnSql() as $sql) {
                $this->addSql($sql);
            }
        }

        // $schema の変更による SQL は, addSql() で登録した SQL の後に実行される.
        // in_stock 列の追加を再計算より前に実行するため, $schema は変更せず, 差分の SQL を順に登録する
        $newTable = clone $table;
        if ($newTable->hasIndex(self::LEGACY_INDEX)) {
            $newTable->dropIndex(self::LEGACY_INDEX);
        }
        if ($newTable->hasColumn('stock')) {
            $newTable->dropColumn('stock');
        }
        if (!$newTable->hasColumn('in_stock')) {
            $newTable->addColumn('in_stock', Types::BOOLEAN, ['default' => false, 'notnull' => true]);
            $newTable->addIndex(['in_stock'], self::IN_STOCK_INDEX);
        }

        $diff = $this->connection->createSchemaManager()->createComparator()->compareTables($table, $newTable);
        if (!$diff->isEmpty()) {
            foreach ($this->platform->getAlterTableSQL($diff) as $sql) {
                $this->addSql($sql);
            }
        }

        $this->addSql($synchronizer->getRecalculateInStockSql());
    }

    #[\Override]
    public function down(Schema $schema): void
    {
    }
}
