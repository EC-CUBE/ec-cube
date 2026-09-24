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
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;

/**
 * ProductClass::in_stock を在庫数 (ProductStock::stock) と在庫無制限フラグから再計算する.
 *
 * 値が変わった場合だけ ProductClass の更新を登録するため,
 * 在庫が 0 をまたがない在庫の増減では dtb_product_class を更新しない.
 */
#[AsDoctrineListener(event: Events::onFlush)]
class ProductClassInStockSubscriber
{
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        /** @var array<int, array{ProductClass, ProductStock|null}> $targets */
        $targets = [];

        $entities = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates());

        foreach ($entities as $entity) {
            if ($entity instanceof ProductClass) {
                $changeSet = $uow->getEntityChangeSet($entity);
                if ($uow->isScheduledForInsert($entity)
                    || array_key_exists('stock_unlimited', $changeSet)
                    || array_key_exists('in_stock', $changeSet)) {
                    $targets[spl_object_id($entity)] ??= [$entity, $entity->getProductStock()];
                }
            }
        }

        // ProductStock 側から辿った組み合わせを優先する.
        // 呼び出し側が inverse side (ProductClass::$ProductStock) を付け替えていない場合でも正しく計算するため.
        foreach ($entities as $entity) {
            if ($entity instanceof ProductStock) {
                $ProductClass = $entity->getProductClass();
                if ($ProductClass === null) {
                    continue;
                }
                if ($uow->isScheduledForInsert($entity) || array_key_exists('stock', $uow->getEntityChangeSet($entity))) {
                    $targets[spl_object_id($ProductClass)] = [$ProductClass, $entity];
                }
            }
        }

        foreach ($targets as [$ProductClass, $ProductStock]) {
            $inStock = $ProductClass->isStockUnlimited()
                || ($ProductStock !== null && $ProductStock->getStock() !== null && bccomp($ProductStock->getStock(), '1') >= 0);

            if ($ProductClass->isInStock() === $inStock) {
                continue;
            }

            $ProductClass->setInStock($inStock);

            if (!$uow->isInIdentityMap($ProductClass) && !$uow->isScheduledForInsert($ProductClass)) {
                // 管理されていない ProductClass は flush の対象外
                continue;
            }

            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata($ProductClass::class), $ProductClass);
        }
    }
}
