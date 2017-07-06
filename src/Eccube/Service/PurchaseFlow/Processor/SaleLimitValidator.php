<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\ItemValidateException;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

/**
 * 販売制限数チェック.
 */
class SaleLimitValidator extends ValidatableItemProcessor
{
    protected function validate(ItemInterface $item)
    {
        $limit = $item->getProductClass()->getSaleLimit();
        $quantity = $item->getQuantity();
        if ($limit < $quantity) {
            throw new ItemValidateException('販売制限数！');
        }
    }

    protected function handle(ItemInterface $item)
    {
        $limit = $item->getProductClass()->getSaleLimit();
        $item->setQuantity($limit);
    }
}
