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
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 在庫制限チェック.
 */
class StockValidator extends ItemValidator
{
    /**
     * @param ItemInterface $item 商品
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @return void
     *
     * @throws InvalidItemException 在庫切れの場合
     */
    #[\Override]
    protected function validate(ItemInterface $item, PurchaseContext $context): void
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

    /**
     * @param ItemInterface $item 商品
     * @param PurchaseContext $context  購入フローのコンテキスト
     *
     * @return void
     */
    #[\Override]
    protected function handle(ItemInterface $item, PurchaseContext $context): void
    {
        $stock = $item->getProductClass()->getStock();
        $item->setQuantity($stock);
    }
}
