<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ItemHolderInterface;

interface PurchaseProcessor
{
    /**
     * @param ItemHolderInterface $target
     * @param ItemHolderInterface $origin
     * @throws PurchaseException
     */
    public function process(ItemHolderInterface $target, ItemHolderInterface $origin);
}