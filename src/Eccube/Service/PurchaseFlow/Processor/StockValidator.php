<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\ItemValidateException;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

class StockValidator extends ValidatableItemProcessor
{
    protected function validate(ItemInterface $item)
    {
        $stock = $item->getProductClass()->getStock();
        $quantity = $item->getQuantity();
        if ($stock < $quantity) {
            throw new ItemValidateException();
        }
    }

    protected function handle(ItemInterface $item) {
        $stock = $item->getProductClass()->getStock();
        $item->setQuantity($stock);
    }
}
