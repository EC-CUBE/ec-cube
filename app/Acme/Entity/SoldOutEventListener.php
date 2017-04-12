<?php


namespace Acme\Entity;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Eccube\Entity\Event\Annotations\PreUpdate;
use Eccube\Entity\Event\EntityEventListener;
use Eccube\Entity\ProductStock;

/**
 * @PreUpdate("Eccube\Entity\ProductStock")
 */
class SoldOutEventListener implements EntityEventListener
{
    public function execute(LifecycleEventArgs $eventArgs)
    {
        /** @var PreUpdateEventArgs $eventArgs */
        if ($eventArgs->hasChangedField('stock')) {
            /** @var ProductStock $ProductStock */
            $ProductStock = $eventArgs->getEntity();

            // 変更前の在庫数
            $prevStock = $eventArgs->getOldValue('stock');
            // 変更後の在庫数
            $currentStock = $ProductStock->getStock();

            // 在庫数が0になった場合は売り切れ
            if ($currentStock == 0 && $prevStock != 0) {
                // TODO 管理者にメール送信
            }
        }
    }
}