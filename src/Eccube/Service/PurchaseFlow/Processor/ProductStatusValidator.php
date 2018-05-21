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
use Eccube\Entity\Master\ProductStatus;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

/**
 * 商品が公開されているかどうか。
 */
class ProductStatusValidator extends ValidatableItemProcessor
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
            $Product = $item->getProductClass()->getProduct();
            if ($Product->getStatus()->getId() != ProductStatus::DISPLAY_SHOW) {
                $this->throwInvalidItemException('cart.product.not.status');
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
