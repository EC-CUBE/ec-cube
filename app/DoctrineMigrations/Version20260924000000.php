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
use Doctrine\Migrations\AbstractMigration;
use Eccube\Service\ProductStockSynchronizer;

/**
 * 在庫数の正典を dtb_product_stock.stock に一本化する。
 *
 * - 旧列 (dtb_product_class.stock) から dtb_product_stock を補完する
 *   (行の無い規格は作成し, 重複行は旧列の値で 1 行にまとめ, ずれは dtb_product_stock を正としてログに残す)
 * - 旧列を削除する (schema:update では LegacyProductClassStockColumnSubscriber により削除されない)
 * - dtb_product_class.in_stock を再計算する (in_stock 列は Entity 属性が源泉で schema:update が追加する)
 */
final class Version20260924000000 extends AbstractMigration
{
    public const NAME = 'dtb_product_class';

    private const LEGACY_INDEX = 'dtb_product_class_stock_stock_unlimited_idx';

    public function up(Schema $schema): void
    {
        // テーブルが存在しない場合終了
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $synchronizer = new ProductStockSynchronizer($this->connection);

        if ($synchronizer->hasLegacyStockColumn()) {
            // 旧列を削除する前に dtb_product_stock を補完する (即時に実行される)
            $synchronizer->syncFromLegacyStockColumn();

            // 旧列の削除は up() の終了後に SQL として実行される
            $table = $schema->getTable(self::NAME);
            if ($table->hasIndex(self::LEGACY_INDEX)) {
                $table->dropIndex(self::LEGACY_INDEX);
            }
            $table->dropColumn('stock');
        }

        // 在庫の有無は dtb_product_stock と在庫無制限フラグから計算するため, 旧列の削除前でも正しく計算できる
        $synchronizer->recalculateInStock();
    }

    #[\Override]
    public function down(Schema $schema): void
    {
    }
}
