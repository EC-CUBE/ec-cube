<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 受注情報の日付更新.
 */
class UpdateDateProcessor extends AbstractPurchaseProcessor
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * UpdateDatePurchaseProcessor constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function commit(ItemHolderInterface $TargetOrder, PurchaseContext $context)
    {
        $dateTime = new \DateTime();
        $OriginOrder = $context->getOriginHolder();

        /* @var Order $TargetOrder */
        if (!$TargetOrder->getOrderStatus()) {
            return ProcessResult::success();
        }

        // 編集
        if ($TargetOrder->getId()) {
            // 発送済
            if ($TargetOrder->getOrderStatus()->getId() == OrderStatus::DELIVERED) {
                // 編集前と異なる場合のみ更新
                if ($TargetOrder->getOrderStatus()->getId() != $OriginOrder->getOrderStatus()->getId()) {
                    // お届け先情報の発送日も更新する.
                    $Shippings = $TargetOrder->getShippings();
                    foreach ($Shippings as $Shipping) {
                        $Shipping->setShippingDate($dateTime);
                    }
                }
                // 入金済
            } elseif ($TargetOrder->getOrderStatus()->getId() == OrderStatus::PAID) {
                // 編集前と異なる場合のみ更新
                if ($TargetOrder->getOrderStatus()->getId() != $OriginOrder->getOrderStatus()->getId()) {
                    $TargetOrder->setPaymentDate($dateTime);
                }
            }
            // 新規
        } else {
            // 発送済
            if ($TargetOrder->getOrderStatus()->getId() == OrderStatus::DELIVERED) {
                // お届け先情報の発送日も更新する.
                $Shippings = $TargetOrder->getShippings();
                foreach ($Shippings as $Shipping) {
                    $Shipping->setShippingDate($dateTime);
                }
                // 入金済
            } elseif ($TargetOrder->getOrderStatus()->getId() == OrderStatus::PAID) {
                $TargetOrder->setPaymentDate($dateTime);
            }
            // 受注日時
            $TargetOrder->setOrderDate($dateTime);
        }

        return ProcessResult::success();
    }
}
