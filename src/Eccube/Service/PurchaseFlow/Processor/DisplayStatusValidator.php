<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

class DisplayStatusValidator extends ValidatableItemProcessor
{
    /**
     * validate
     * @param ItemInterface $item
     * @param PurchaseContext $context
     * @throws InvalidItemException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return;
        }
        $ProductClass = $item->getProductClass();
        if (!$ProductClass->isEnable()) {
            throw new InvalidItemException('cart.product.not.status');
        }
    }

    /**
     * handle
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
