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

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 税率が変更されたかどうかを検知するバリデータ.
 */
class TaxRateChangeValidator extends ItemHolderPostValidator
{

    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        /** @var Order $originHolder */
        $originHolder = $context->getOriginHolder();

        // 受注の生成直後はチェックしない
        if (!$originHolder->getOrderNo()) {
            return;
        }

        // 複数配送の場合、$originHolderに税率が設定されないのでチェックしない
        if (0 == current($originHolder->getTaxByTaxRate())) {
            return;
        }

        if (!empty(array_diff(array_keys($originHolder->getTaxByTaxRate()), array_keys($itemHolder->getTaxByTaxRate())))) {
            $this->throwInvalidItemException('purchase_flow.tax_rate_update', null, true);
        }
    }
}
