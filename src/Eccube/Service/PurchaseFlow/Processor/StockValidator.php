<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\ItemValidateException;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

/**
 * 在庫制限チェック.
 */
class StockValidator extends ValidatableItemProcessor
{
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return;
        }
        if ($item->getProductClass()->getStockUnlimited()) {
            return;
        }
        $stock = $item->getProductClass()->getStock();
        $quantity = $item->getQuantity();
        if ($stock == 0) {
            throw new ItemValidateException('cart.zero.stock', ['%product%' => $item->getProductClass()->getProduct()->getName()]);
        }
        if ($stock < $quantity) {
            throw new ItemValidateException('cart.over.stock', ['%product%' => $item->getProductClass()->getProduct()->getName()]);
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context) {
        $stock = $item->getProductClass()->getStock();
        $item->setQuantity($stock);
    }
}
