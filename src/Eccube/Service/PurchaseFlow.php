<?php

namespace Eccube\Service;


use Eccube\Entity\ItemHolderInterface;

class PurchaseFlow
{
    // TODO collection?
    protected $itemHolderProsessors = [];

    protected $itemProsessors = [];

    public function execute(ItemHolderInterface $itemHolder) {
        return $itemHolder;
    }

    public function addItemHolderProcessor(ItemHolderProcessor $prosessor)
    {
        $this->itemHolderProsessors[] = $prosessor;
    }

    public function addItemProcessor(ItemProcessor $prosessor)
    {
        $this->itemProsessors[] = $prosessor;
    }
}