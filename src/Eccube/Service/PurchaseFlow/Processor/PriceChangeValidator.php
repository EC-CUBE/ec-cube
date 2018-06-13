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

use Eccube\Entity\ItemInterface;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

/**
 * 販売価格の変更検知.
 */
class PriceChangeValidator extends ValidatableItemProcessor
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @throws \Eccube\Service\PurchaseFlow\InvalidItemException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return;
        }

        if ($item instanceof OrderItem) {
            $price = $item->getPriceIncTax();
        } else {
            $price = $item->getPrice();
        }

        $realPrice = $item->getProductClass()->getPrice02IncTax();

        if ($price !== $realPrice) {
            $this->throwInvalidItemException('cart.product.price.change', $item->getProductClass());
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $price = $item->getProductClass()->getPrice02IncTax();
        $item->setPrice($price);
    }
}
