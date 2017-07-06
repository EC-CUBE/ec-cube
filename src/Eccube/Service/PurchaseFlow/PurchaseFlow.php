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

        $this->calculateDeliveryFeeTotal($itemHolder);
        $this->calculateCharge($itemHolder);
        $this->calculateDiscount($itemHolder);
        $this->calculateTax($itemHolder);
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
    protected function calculateTotal(ItemHolderInterface $itemHolder)
    {
        // TODO 後方互換のため discount は減算する必要がある？
        // Order::getTotalPrice() の依存をどうにかしなければ
        $total = array_reduce($itemHolder->getItems()->toArray(), function ($sum, ItemInterface $item) {
            $sum += $item->getPrice() * $item->getQuantity();
            return $sum;
        }, 0);
        $itemHolder->setTotal($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateDeliveryFeeTotal(ItemHolderInterface $itemHolder)
    {
        $total = array_reduce($itemHolder->getItems()->filter(
            function (ItemInterface $item) {
                return $item->isDeliveryFee();
            })->toArray(), function ($sum, ItemInterface $item) {
                $sum += $item->getPrice() * $item->getQuantity();
                return $sum;
            }, 0);
        $itemHolder->setDeliveryFeeTotal($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateDiscount(ItemHolderInterface $itemHolder)
    {
        $total = array_reduce($itemHolder->getItems()->filter(
            function (ItemInterface $item) {
                return $item->isDiscount();
            })->toArray(), function ($sum, ItemInterface $item) {
                $sum += $item->getPrice() * $item->getQuantity();
                return $sum;
            }, 0);
        $itemHolder->setDiscount($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateCharge(ItemHolderInterface $itemHolder)
    {
        $total = array_reduce($itemHolder->getItems()->filter(
            function (ItemInterface $item) {
                return $item->isCharge();
            })->toArray(), function ($sum, ItemInterface $item) {
                $sum += $item->getPrice() * $item->getQuantity();
                return $sum;
            }, 0);
        $itemHolder->setCharge($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateTax(ItemHolderInterface $itemHolder)
    {
        $total = array_reduce($itemHolder->getItems()->toArray(), function ($sum, ItemInterface $item) {
            $sum += ($item->getPriceIncTax() - $item->getPrice()) * $item->getQuantity();
            return $sum;
        }, 0);
        $itemHolder->setTax($total);
    }
}
