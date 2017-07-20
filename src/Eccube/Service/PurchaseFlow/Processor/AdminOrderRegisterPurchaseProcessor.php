<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 管理画面/受注登録/編集画面の完了処理
 */
class AdminOrderRegisterPurchaseProcessor implements PurchaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ItemHolderInterface $target, PurchaseContext $context)
    {
        // 画面上で削除された明細をremove
        foreach ($context->getOriginHolder()->getItems() as $ShipmentItem) {
            if (false === $target->getShipmentItems()->contains($ShipmentItem)) {
                $ShipmentItem->setOrder(null);
            }
        }

        foreach ($target->getItems() as $ShipmentItem) {
            $ShipmentItem->setOrder($target);
        }
    }
}
