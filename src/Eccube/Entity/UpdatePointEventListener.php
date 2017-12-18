<?php

namespace Eccube\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Eccube\Annotation\Inject;
use Eccube\Annotation\PreUpdate;
use Eccube\Entity\Event\EntityEventListener;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Service\MailService;

/**
 * @PreUpdate("Eccube\Entity\Order")
 */
class UpdatePointEventListener implements EntityEventListener
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject(MailService::class)
     * @var MailService
     */
    protected $mailService;

    public function execute(LifecycleEventArgs $eventArgs)
    {
        /** @var PreUpdateEventArgs $eventArgs */
        if ($eventArgs->hasChangedField('OrderStatus')) {
            $addCustomerPoint = 0;

            /** @var OrderStatus $prevStatus */
            $prevStatus = $eventArgs->getOldValue('OrderStatus');

            /** @var Order $newOrder */
            $newOrder = $eventArgs->getEntity();
            /** @var OrderStatus $newStatus */
            $newStatus = $newOrder->getOrderStatus();

            /*
             * 2.13系の処理を移植
             * @see https://github.com/EC-CUBE/eccube-2_13/blob/eccube-2.13.5/data/class/helper/SC_Helper_Purchase.php#L1134-L1163
             */

            // 使用ポイントの更新
            // 変更前の対応状況が利用対象の場合、変更前の使用ポイント分を戻す
            if ($this->isUsePoint($prevStatus)) {
                if ($eventArgs->hasChangedField('use_point')) {
                    $addCustomerPoint += $eventArgs->getOldValue('use_point');
                } else {
                    $addCustomerPoint += $newOrder->getUsePoint();
                }
            }

            // 変更後の対応状況が利用対象の場合、変更後の使用ポイント分を引く
            if ($this->isUsePoint($newStatus)) {
                $addCustomerPoint -= $newOrder->getUsePoint();
            }

            // 加算ポイントの更新
            // 変更前の対応状況が加算対象の場合、変更前の加算ポイント分を戻す
            if ($this->isAddPoint($prevStatus)) {
                if ($eventArgs->hasChangedField('add_point')) {
                    $addCustomerPoint -= $eventArgs->getOldValue('add_point');
                } else {
                    $addCustomerPoint -= $newOrder->getAddPoint();
                }
            }

            // 変更後の対応状況が加算対象の場合、変更後の加算ポイント分を足す
            if ($this->isAddPoint($newStatus)) {
                $addCustomerPoint += $newOrder->getAddPoint();
            }

            if ($addCustomerPoint != 0) {
                /** @var Customer $Customer */
                $Customer = $newOrder->getCustomer();
                $newPoint = $Customer->getPoint() + $addCustomerPoint;
                if ($newPoint < 0) {
                    // ポイントがマイナスになるためメールを送信する
                    $this->mailService->sendPointNotifyMail($newOrder, $Customer->getPoint(), $addCustomerPoint);
                }
                $Customer->setPoint($newPoint);
                // この時点で Customer は Doctrine の更新対象となっていないので, 更新対象に設定する
                $meta = $this->entityManager->getClassMetadata(Customer::class);
                // Customer の変更内容を設定する
                $this->entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $Customer);
                // Customer の ChangeSet を scheduleExtraUpdate に設定する
                $changeSet = $this->entityManager->getUnitOfWork()->getEntityChangeSet($Customer);
                $this->entityManager->getUnitOfWork()->scheduleExtraUpdate($Customer, $changeSet);
            }
        }
    }

    protected function isUsePoint(OrderStatus $Status)
    {
        if ($Status->getId() == OrderStatus::CANCEL) {
            return false;
        }
        return true;
    }

    protected function isAddPoint(OrderStatus $Status)
    {
        if ($Status->getId() == OrderStatus::DELIVERED) {
            return true;
        }
        return false;
    }
}
