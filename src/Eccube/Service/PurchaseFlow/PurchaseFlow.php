<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;

class PurchaseFlow
{
    // TODO collection?
    /**
     * @var ItemHolderProcessor[]
     */
    protected $itemHolderProsessors = [];

    /**
     * @var ItemProcessor[]
     */
    protected $itemProsessors = [];

    public function execute(ItemHolderInterface $itemHolder) {

        $this->calculateTotal($itemHolder);

        foreach ($itemHolder->getItems() as $item) {
            foreach ($this->itemProsessors as $itemProsessor) {
                $result = $itemProsessor->process($item);
                if ($result->isError()) {
                    $itemHolder->addError($result->getErrorMessage());
                }
            }
        }

        foreach ($this->itemHolderProsessors as $holderProcessor) {
            $result = $holderProcessor->process($itemHolder);
            if ($result->isError()) {
                $itemHolder->addError($result->getErrorMessage());
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

    /**
     * @param ItemHolderInterface $itemHolder
     */
    private function calculateTotal(ItemHolderInterface $itemHolder)
    {
        $total = array_reduce($itemHolder->getItems(), function ($sum, ItemInterface $item) {
            $sum += $item->getPrice() * $item->getQuantity();
            return $sum;
        }, 0);
        $itemHolder->setTotal($total);
    }
}
