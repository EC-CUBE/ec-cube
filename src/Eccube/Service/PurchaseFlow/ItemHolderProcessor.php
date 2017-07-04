<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ItemHolderInterface;

interface ItemHolderProcessor
{
    /**
     * @param ItemHolderInterface $itemHolder
     * @return ProcessResult
     */
    public function process(ItemHolderInterface $itemHolder);
}