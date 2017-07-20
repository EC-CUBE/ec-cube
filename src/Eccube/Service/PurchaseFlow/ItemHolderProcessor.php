<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ItemHolderInterface;

interface ItemHolderProcessor
{
    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     * @return ProcessResult
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context);
}