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
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

class ValidatableEmptyProcessor extends ValidatableItemProcessor
{
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        $error = false;
        if ($error) {
            throw new InvalidItemException('ValidatableEmptyProcessorのエラーです');
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $item->setQuantity(100);
    }
}
