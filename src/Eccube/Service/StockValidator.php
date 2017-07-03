<?php

namespace Eccube\Service;

use Eccube\Entity\ItemInterface;

class StockValidator extends ValidatableItemProcessor
{
    protected function validate(ItemInterface $item)
    {
        $stock = $item->getObject()->getStock();
        $quantity = $item->getQuantity();
        if ($stock < $quantity) {
            throw new ItemValidateException();
        }
    }

    protected function handle(ItemInterface $item) {
        $stock = $item->getObject()->getStock();
        $item->setQuantity($stock);
    }
}
