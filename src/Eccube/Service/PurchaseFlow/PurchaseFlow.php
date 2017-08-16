<?php

namespace Eccube\Service\PurchaseFlow;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;

class PurchaseFlow
{
    /**
     * @var ArrayCollection|ItemHolderProcessor[]
     */
    protected $itemHolderProcessors;

    /**
     * @var ArrayCollection|ItemProcessor[]
     */
    protected $itemProcessors;

    /**
     * @var ArrayCollection|PurchaseProcessor[]
     */
    protected $purchaseProcessors;

    /** @var ArrayCollection|ItemComparer[] */
    protected $itemComparers;

    public function __construct()
    {
        $this->itemProcessors = new ArrayCollection();
        $this->itemHolderProcessors = new ArrayCollection();
        $this->purchaseProcessors = new ArrayCollection();
        $this->itemComparers = new ArrayCollection();
    }

    public function setItemProcessors(ArrayCollection $processors)
    {
        $this->itemProcessors = $processors;
    }

    public function setItemHolderProcessors(ArrayCollection $processors)
    {
        $this->itemHolderProcessors = $processors;
    }

    public function setPurchaseProcessors(ArrayCollection $processors)
    {
        $this->purchaseProcessors = $processors;
    }

    /**
     * @param ArrayCollection|PurchaseProcessor[] $itemComparers
     */
    public function setItemComparers($itemComparers)
    {
        $this->itemComparers = $itemComparers;
    }

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     */
    public function addItem(ItemInterface $item, PurchaseContext $context)
    {
        $originHolder = $context->getOriginHolder();
        $flowResult = new PurchaseFlowResult($originHolder);

        foreach ($this->itemComparers as $itemComparer) {

            $flowResult->addProcessResult($itemComparer->process($item, $context));

            if ($flowResult->hasError()) {
                break;
            }
        }

        if (!$flowResult->hasError()) {
            $originHolder->addItem($item);
        }
    }

    public function calculate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $this->calculateAll($itemHolder);

        $flowResult = new PurchaseFlowResult($itemHolder);

        foreach ($itemHolder->getItems() as $item) {
            foreach ($this->itemProcessors as $itemProsessor) {
                $result = $itemProsessor->process($item, $context);
                $flowResult->addProcessResult($result);
            }
        }

        $this->calculateAll($itemHolder);

        foreach ($this->itemHolderProcessors as $holderProcessor) {
            $result = $holderProcessor->process($itemHolder, $context);
            $flowResult->addProcessResult($result);
        }

        $this->calculateAll($itemHolder);

        return $flowResult;
    }

    /**
     * @param ItemHolderInterface $target
     * @param PurchaseContext     $context
     *
     * @throws PurchaseException
     */
    public function purchase(ItemHolderInterface $target, PurchaseContext $context)
    {
        foreach ($this->purchaseProcessors as $processor) {
            $processor->process($target, $context);
        }
    }

    public function addPurchaseProcessor(PurchaseProcessor $processor)
    {
        $this->purchaseProcessors[] = $processor;
    }

    public function addItemHolderProcessor(ItemHolderProcessor $prosessor)
    {
        $this->itemHolderProcessors[] = $prosessor;
    }

    public function addItemProcessor(ItemProcessor $prosessor)
    {
        $this->itemProcessors[] = $prosessor;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateTotal(ItemHolderInterface $itemHolder)
    {
        $total = $itemHolder->getItems()->reduce(function ($sum, ItemInterface $item) {
            $sum += $item->getPriceIncTax() * $item->getQuantity();

            return $sum;
        }, 0);
        $itemHolder->setTotal($total);
        // TODO
        if ($itemHolder instanceof \Eccube\Entity\Order) {
            // Order には PaymentTotal もセットする
            $itemHolder->setPaymentTotal($total);
        }
    }

    protected function calculateSubTotal(ItemHolderInterface $itemHolder)
    {
        $total = $itemHolder->getItems()
            ->getProductClasses()
            ->reduce(function ($sum, ItemInterface $item) {
                $sum += $item->getPriceIncTax() * $item->getQuantity();

                return $sum;
            }, 0);
        // TODO
        if ($itemHolder instanceof \Eccube\Entity\Order) {
            // Order の場合は SubTotal をセットする
            $itemHolder->setSubTotal($total);
        }
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateDeliveryFeeTotal(ItemHolderInterface $itemHolder)
    {
        $total = $itemHolder->getItems()
            ->getDeliveryFees()
            ->reduce(function ($sum, ItemInterface $item) {
                $sum += $item->getPriceIncTax() * $item->getQuantity();

                return $sum;
            }, 0);
        $itemHolder->setDeliveryFeeTotal($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateDiscount(ItemHolderInterface $itemHolder)
    {
        $total = $itemHolder->getItems()
            ->getDiscounts()
            ->reduce(function ($sum, ItemInterface $item) {
                $sum += $item->getPriceIncTax() * $item->getQuantity();

                return $sum;
            }, 0);
        // TODO 後方互換のため discount には正の整数を代入する
        $itemHolder->setDiscount($total * -1);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateCharge(ItemHolderInterface $itemHolder)
    {
        $total = $itemHolder->getItems()
            ->getCharges()
            ->reduce(function ($sum, ItemInterface $item) {
                $sum += $item->getPriceIncTax() * $item->getQuantity();

                return $sum;
            }, 0);
        $itemHolder->setCharge($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateTax(ItemHolderInterface $itemHolder)
    {
        $total = $itemHolder->getItems()
            ->reduce(function ($sum, ItemInterface $item) {
                $sum += ($item->getPriceIncTax() - $item->getPrice()) * $item->getQuantity();

                return $sum;
            }, 0);
        $itemHolder->setTax($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     */
    protected function calculateAll(ItemHolderInterface $itemHolder)
    {
        $this->calculateDeliveryFeeTotal($itemHolder);
        $this->calculateCharge($itemHolder);
        $this->calculateDiscount($itemHolder);
        $this->calculateSubTotal($itemHolder); // Order の場合のみ
        $this->calculateTax($itemHolder);
        $this->calculateTotal($itemHolder);
    }
}
