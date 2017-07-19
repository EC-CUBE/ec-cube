<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\Processor\PurchaseContext;

interface ItemProcessor
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     * @return ProcessResult
     */
    public function process(ItemInterface $item, PurchaseContext $context);

}