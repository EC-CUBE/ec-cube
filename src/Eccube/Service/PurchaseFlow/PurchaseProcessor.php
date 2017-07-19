<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\Processor\PurchaseContext;

interface PurchaseProcessor
{
    /**
     * @param ItemHolderInterface $target
     * @param PurchaseContext $context
     * @throws PurchaseException
     */
    public function process(ItemHolderInterface $target, PurchaseContext $context);
}