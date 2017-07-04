<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ItemHolderInterface;

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

        // TODO 集計する

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
}
