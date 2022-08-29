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

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 商品規格の公開状態チェック
 */
class ClassCategoryValidator extends ItemValidator
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     * @return void
     * @throws \Eccube\Service\PurchaseFlow\InvalidItemException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context): void
    {
        if (!$item->isProduct()) {
            return;
        }

        if ($item->getProductClass()->getClassCategory1()) {
            if (!$item->getProductClass()->getClassCategory1()->isVisible()) {
                $this->throwInvalidItemException('front.shopping.not_purchase_product_class', $item->getProductClass());
            }
        }

        if ($item->getProductClass()->getClassCategory2()) {
            if (!$item->getProductClass()->getClassCategory2()->isVisible()) {
                $this->throwInvalidItemException('front.shopping.not_purchase_product_class', $item->getProductClass());
            }
        }
    }

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     * @return void
     */
    protected function handle(ItemInterface $item, PurchaseContext $context): void
    {
        $item->setQuantity(0);
    }
}
