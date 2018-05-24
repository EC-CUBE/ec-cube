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
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;

/**
 * 利用ポイントの減算処理
 */
class UsePointToCustomerPurchaseProcessor implements PurchaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $Order = $itemHolder;
        $Customer = $context->getUser();
        if (!$Customer) {
            return ProcessResult::success();
        }
        if ($Customer->getPoint() < $Order->getUsePoint()) {
            return ProcessResult::error('利用ポイントが所有ポイントを上回っています.');
        }
        $Customer->setPoint($Customer->getPoint() + $Order->getUsePoint() * -1);

        return ProcessResult::success();
    }
}
