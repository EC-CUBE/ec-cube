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
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 手数料明細の金額とdtb_paymentに登録されている手数料の差異を検知するバリデータ.
 */
class PaymentChargeChangeValidator extends ItemHolderValidator
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

        // 手数料明細がない場合はスキップする.
        if (!$this->hasChargeItems($itemHolder)) {
            return;
        }

        $charge = $itemHolder->getCharge();
        $realCharge = $itemHolder->getPayment()->getCharge();

        if ($charge != $realCharge) {
            foreach ($itemHolder->getOrderItems() as $item) {
                // 手数料明細は支払手数料に紐づく1件のみの想定
                if ($item->isCharge()) {
                    $item->setPrice($realCharge);
                    $this->throwInvalidItemException('手数料が変更されました.', null, true);
                }
            }
        }
    }

    protected function hasChargeItems(Order $Order)
    {
        foreach ($Order->getOrderItems() as $item) {
            if ($item->isCharge()) {
                return true;
            }
        }

        return false;
    }
}
