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
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 会員の購入情報更新.
 */
class CustomerPurchaseInfoProcessor extends AbstractPurchaseProcessor
{
    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        if (!$target instanceof Order) {
            return;
        }

        $Customer = $target->getCustomer();
        if (!$Customer) {
            return;
        }

        $now = new \DateTime();
        $firstBuyDate = $Customer->getFirstBuyDate();
        if (empty($firstBuyDate)) {
            $Customer->setFirstBuyDate($now);
        }
        $Customer->setLastBuyDate($now);

        $Customer->setBuyTimes($Customer->getBuyTimes() + 1);
        $Customer->setBuyTotal($Customer->getBuyTotal() + $target->getTotal());
    }
}
