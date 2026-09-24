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

namespace Eccube\Doctrine\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use Eccube\Entity\ProductClass;
use Eccube\Service\ProductStockSynchronizer;

/**
 * 在庫数の旧列 (dtb_product_class.stock) がスキーマ更新で削除されないようにする.
 *
 * 旧列はマッピングから外れているため, そのままでは doctrine:schema:update や
 * 管理画面からのプラグインの導入・有効化で削除され, dtb_product_stock に無い在庫数が失われる.
 * 旧列が DB に残っている間はスキーマ生成の結果にも旧列を残し,
 * マイグレーション (Version20260924000000) で dtb_product_stock を補完してから削除する.
 */
#[AsDoctrineListener(event: ToolEvents::postGenerateSchemaTable)]
class LegacyProductClassStockColumnSubscriber
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $args): void
    {
        if (!is_a($args->getClassMetadata()->getName(), ProductClass::class, true)) {
            return;
        }

        $table = $args->getClassTable();
        if ($table->hasColumn('stock')) {
            return;
        }

        if (!(new ProductStockSynchronizer($this->connection))->hasLegacyStockColumn()) {
            return;
        }

        $table->addColumn('stock', Types::DECIMAL, [
            'precision' => 10,
            'scale' => 0,
            'notnull' => false,
        ]);
    }
}
