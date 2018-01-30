<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

/**
 * 在庫制限チェック.
 */
class StockValidator extends ValidatableItemProcessor
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
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
            $this->throwInvalidItemException('cart.zero.stock', $item->getProductClass());
        }
        if ($stock < $quantity) {
            $this->throwInvalidItemException('cart.over.stock', $item->getProductClass());
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $stock = $item->getProductClass()->getStock();
        $item->setQuantity($stock);
    }
}
