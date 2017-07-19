<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\ItemValidateException;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

class DisplayStatusValidator extends ValidatableItemProcessor
{
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return;
        }
        $ProductClass = $item->getProductClass();
        if (!$ProductClass->isEnable()) {
            throw new ItemValidateException('cart.product.not.status');
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        if ($item instanceof CartItem) {
            $item->setQuantity(0);
        }
    }
}
