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
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemHolderProcessor;

/**
 * 合計金額のマイナスチェック.
 */
class PaymentTotalNegativeValidator extends ValidatableItemHolderProcessor
{
    /**
     * @param ItemHolderInterface $item
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemHolderInterface $item, PurchaseContext $context)
    {
        if ($item->getTotal() < 0) {
            $this->throwInvalidItemException('shopping.total.price');
        }
    }
}
