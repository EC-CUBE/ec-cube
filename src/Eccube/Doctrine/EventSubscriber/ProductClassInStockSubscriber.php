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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;

/**
 * ProductClass::in_stock を在庫数 (ProductStock::stock) と在庫無制限フラグから再計算する.
 *
 * 読み込んだ時点の in_stock は, 別のトランザクションが書き換えている可能性がある.
 * そのため, 読み込んだ値と比べて書き込みを省くことはせず, 次のように判定する.
 *
 * - 在庫をロックして更新した場合 (StockReduceProcessor): ロック後に読み直した在庫数の変化で,
 *   在庫の有無が切り替わったときだけ書き込む. 在庫が 0 をまたがない購入では dtb_product_class を更新しない
 * - それ以外 (管理画面や CSV 取り込み, 在庫無制限フラグの変更等): 在庫数と同じトランザクションで毎回書き込む
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
                $oid = spl_object_id($ProductClass);
                if ($uow->isScheduledForInsert($entity)) {
                    $targets[$oid] = [$ProductClass, $entity];
                    continue;
                }
                $changeSet = $uow->getEntityChangeSet($entity);
                if (!array_key_exists('stock', $changeSet)) {
                    continue;
                }
                $stockChange = $changeSet['stock'];
                if ($this->isLocked($entity) && !isset($targets[$oid]) && is_array($stockChange)) {
                    $unlimited = $ProductClass->isStockUnlimited();
                    if ($this->calculate($unlimited, $stockChange[0]) === $this->calculate($unlimited, $stockChange[1])) {
                        continue;
                    }
                }
                $targets[$oid] = [$ProductClass, $entity];
            }
        }

        foreach ($targets as [$ProductClass, $ProductStock]) {
            $this->write($em, $uow, $ProductClass, $this->calculate($ProductClass->isStockUnlimited(), $ProductStock?->getStock()));
        }
    }

    /**
     * 在庫のロック後に読み直した ProductStock かどうか. StockReduceProcessor がロック済みの目印を付ける.
     */
    private function isLocked(ProductStock $ProductStock): bool
    {
        return $ProductStock->getProductClassId() !== null;
    }

    private function calculate(bool $unlimited, ?string $stock): bool
    {
        return $unlimited || ($stock !== null && bccomp($stock, '1') >= 0);
    }

    private function write(EntityManagerInterface $em, UnitOfWork $uow, ProductClass $ProductClass, bool $inStock): void
    {
        $ProductClass->setInStock($inStock);

        if ($uow->isScheduledForInsert($ProductClass)) {
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata($ProductClass::class), $ProductClass);

            return;
        }

        if (!$uow->isInIdentityMap($ProductClass)) {
            // 管理されていない ProductClass は flush の対象外
            return;
        }

        $uow->recomputeSingleEntityChangeSet($em->getClassMetadata($ProductClass::class), $ProductClass);

        // 読み込んだ時点の値と同じでも, DB の値は別のトランザクションが書き換えている可能性があるため書き込む
        if (!array_key_exists('in_stock', $uow->getEntityChangeSet($ProductClass))) {
            $uow->scheduleExtraUpdate($ProductClass, ['in_stock' => [$inStock, $inStock]]);
        }
    }
}
