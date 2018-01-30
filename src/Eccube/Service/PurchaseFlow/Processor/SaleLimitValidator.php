<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

/**
 * 販売制限数チェック.
 */
class SaleLimitValidator extends ValidatableItemProcessor
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     * @throws InvalidItemException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return;
        }

        $limit = $item->getProductClass()->getSaleLimit();
        if (is_null($limit)) {
            return;
        }

        $quantity = $item->getQuantity();
        if ($limit < $quantity) {
            $this->throwInvalidItemException('cart.over.sale_limit', $item->getProductClass());
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $limit = $item->getProductClass()->getSaleLimit();
        $item->setQuantity($limit);
    }
}
