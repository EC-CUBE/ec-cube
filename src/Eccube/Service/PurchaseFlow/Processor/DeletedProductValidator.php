<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\ItemValidateException;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

class DeletedProductValidator extends ValidatableItemProcessor
{
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        $ProductClass = $item->getProductClass();
        $Product = $ProductClass->getProduct();
        if ($Product->getDelFlg()) {
            throw new ItemValidateException('cart.product.delete');
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        if ($item instanceof CartItem) {
            $item->setQuantity(0);
        }
    }
}
