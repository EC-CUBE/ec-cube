<?php

namespace Eccube\Service;


use Eccube\Entity\ItemHolderInterface;

class PurchaseFlow
{
    // TODO collection?
    protected $itemHolderProsessors = [];

    protected $itemProsessors = [];

    public function execute(ItemHolderInterface $itemHolder) {
        foreach ($itemHolder->getItems() as $item) {
            foreach ($this->itemProsessors as $itemProsessor) {
                try {
                    $itemProsessor->process($item);
                } catch (ItemValidateException $exception) {
                    $itemHolder->addError($exception);
                }
            }
        }
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