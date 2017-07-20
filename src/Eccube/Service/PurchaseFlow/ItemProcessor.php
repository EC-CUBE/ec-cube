<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ItemInterface;

interface ItemProcessor
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     * @return ProcessResult
     */
    public function process(ItemInterface $item, PurchaseContext $context);

}