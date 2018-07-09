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

use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 管理画面/受注登録/編集画面の完了処理.
 */
class AdminOrderRegisterPurchaseProcessor implements ItemHolderPreprocessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ItemHolderInterface $target, PurchaseContext $context)
    {
        if (is_null($context->getOriginHolder())) {
            return;
        }

        // 画面上で削除された明細をremove
        foreach ($context->getOriginHolder()->getItems() as $OrderItem) {
            if (false === $target->getOrderItems()->contains($OrderItem)) {
                $OrderItem->setOrder(null);
            }
        }

        foreach ($target->getItems() as $OrderItem) {
            $OrderItem->setOrder($target);
        }
    }
}
