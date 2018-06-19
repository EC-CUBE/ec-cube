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
use Eccube\Service\PurchaseFlow\ItemProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 販売価格の変更検知.
 */
class PriceChangeValidator implements ItemProcessor
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @return ProcessResult
     */
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return ProcessResult::success();
        }

        if ($item instanceof OrderItem) {
            $price = $item->getPriceIncTax();
        } else {
            // CartItem::priceは税込金額.
            $price = $item->getPrice();
        }

        $realPrice = $item->getProductClass()->getPrice02IncTax();
        if ($price != $realPrice) {
            $message = trans('cart.product.price.change',
                ['%product%' => $item->getProductClass()->formatedProductName()]);

            if ($item instanceof OrderItem) {
                $item->setPrice($item->getProductClass()->getPrice02());

                return ProcessResult::error($message);
            } else {
                // CartItem::priceは税込金額.
                $item->setPrice($realPrice);

                return ProcessResult::warn($message);
            }
        }

        return ProcessResult::success();
    }
}
