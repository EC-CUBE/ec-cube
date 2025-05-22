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
 * 在庫制限チェック.
 */
class StockValidator extends ItemValidator
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
        if ($item->getProductClass()->isStockUnlimited()) {
            return;
        }
        $stock = $item->getProductClass()->getStock();
        $quantity = $item->getQuantity();
        if ($stock == 0) {
            $this->throwInvalidItemException('front.shopping.out_of_stock_zero', $item->getProductClass());
        }
        if ($stock < $quantity) {
            $this->throwInvalidItemException('front.shopping.out_of_stock', $item->getProductClass());
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $stock = $item->getProductClass()->getStock();
        $item->setQuantity($stock);
    }
}
