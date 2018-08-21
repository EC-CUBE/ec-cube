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
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 販売価格の変更検知.
 */
class PriceChangeValidator extends ItemValidator
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @throws \Eccube\Service\PurchaseFlow\InvalidItemException
     */
    public function validate(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return;
        }

        if ($item instanceof OrderItem) {
            $price = $item->getPriceIncTax();
        } else {
            // CartItem::priceは税込金額.
            $price = $item->getPrice();
        }

        $realPrice = $item->getProductClass()->getPrice02IncTax();
        if ($price != $realPrice) {
            if ($item instanceof OrderItem) {
                $item->setPrice($item->getProductClass()->getPrice02());

                $this->throwInvalidItemException('front.shopping.price_changed', $item->getProductClass());
            } else {
                // CartItem::priceは税込金額.
                $item->setPrice($realPrice);
                $this->throwInvalidItemException('front.shopping.price_changed', $item->getProductClass());
            }
        }
    }
}
