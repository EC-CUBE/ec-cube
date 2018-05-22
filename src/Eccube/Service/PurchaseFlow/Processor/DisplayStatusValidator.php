<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

class DisplayStatusValidator extends ValidatableItemProcessor
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
        $ProductClass = $item->getProductClass();
        if (!$ProductClass->isEnable()) {
            $this->throwInvalidItemException('cart.product.not.status');
        }
    }

    /**
     * handle
     *
     * @param ItemInterface $item
     * @param PurchaseContext $context
     */
    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        if ($item instanceof CartItem) {
            $item->setQuantity(0);
        }
    }
}
