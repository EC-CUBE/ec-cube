<?php

namespace Eccube\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Service\MailService;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ServiceSubscriberInterface;

class UpdatePointEventSubscriber implements EventSubscriber, ServiceSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $eccubeConfig;

    public function __construct($eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        if (!$eventArgs->getObject() instanceof Order) {
            return;
        }

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
                    $mailService = $this->container->get(MailService::class);
                    $mailService->sendPointNotifyMail($newOrder, $Customer->getPoint(), $addCustomerPoint);
                }
                $Customer->setPoint($newPoint);
                $entityManager = $eventArgs->getObjectManager();
                // この時点で Customer は Doctrine の更新対象となっていないので, 更新対象に設定する
                $meta = $entityManager->getClassMetadata(Customer::class);
                // Customer の変更内容を設定する
                $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $Customer);
                // Customer の ChangeSet を scheduleExtraUpdate に設定する
                $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($Customer);
                $entityManager->getUnitOfWork()->scheduleExtraUpdate($Customer, $changeSet);
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

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
        ];
    }

    /**
     * Returns an array of service types required by such instances, optionally keyed by the service names used internally.
     *
     * For mandatory dependencies:
     *
     *  * array('logger' => 'Psr\Log\LoggerInterface') means the objects use the "logger" name
     *    internally to fetch a service which must implement Psr\Log\LoggerInterface.
     *  * array('Psr\Log\LoggerInterface') is a shortcut for
     *  * array('Psr\Log\LoggerInterface' => 'Psr\Log\LoggerInterface')
     *
     * otherwise:
     *
     *  * array('logger' => '?Psr\Log\LoggerInterface') denotes an optional dependency
     *  * array('?Psr\Log\LoggerInterface') is a shortcut for
     *  * array('Psr\Log\LoggerInterface' => '?Psr\Log\LoggerInterface')
     *
     * @return array The required service types, optionally keyed by service names
     */
    public static function getSubscribedServices()
    {
        return [MailService::class];
    }
}
