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
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 手数料が変更されたかどうかを検知するバリデータ.
 */
class PaymentChargeChangeValidator extends ItemHolderPostValidator
{
    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        /** @var Order $originHolder */
        $originHolder = $context->getOriginHolder();

        // 受注の生成直後はチェックしない.
        if (!$originHolder->getOrderNo()) {
            return;
        }

        if ($originHolder->getCharge() != $itemHolder->getCharge()) {
            $this->throwInvalidItemException('purchase_flow.charge_update', null, true);
        }
    }
}
