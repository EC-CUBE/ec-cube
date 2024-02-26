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
use Eccube\Entity\Master\ProductStatus;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 商品が公開されているかどうか。
 */
class ProductStatusValidator extends ItemValidator
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        if ($item->isProduct()) {
            $ProductClass = $item->getProductClass();
            if (!$item->getProductClass()->isVisible()) {
                $this->throwInvalidItemException('front.shopping.not_purchase_product_class', $ProductClass);
            }

            $Product = $ProductClass->getProduct();
            if ($Product->getStatus()->getId() != ProductStatus::DISPLAY_SHOW) {
                $this->throwInvalidItemException('front.shopping.not_purchase');
            }
        }
    }

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     */
    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $item->setQuantity(0);
    }
}
