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

namespace Plugin\PurchaseProcessors\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\ItemProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ProcessResult;

class EmptyProcessor implements ItemProcessor
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @return ProcessResult
     */
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        log_info('empty processor executed', [__METHOD__]);

        return ProcessResult::success();
    }
}
