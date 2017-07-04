<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ItemInterface;

interface ItemProcessor
{
    /**
     * @param ItemInterface $item
     * @return ProcessResult
     */
    public function process(ItemInterface $item);
}