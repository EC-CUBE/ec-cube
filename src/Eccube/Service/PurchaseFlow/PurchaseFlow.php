<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\PurchaseFlow;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;

class PurchaseFlow
{
    /**
     * @var ArrayCollection|ItemPreprocessor[]
     */
    protected $itemPreprocessors;

    /**
     * @var ArrayCollection|ItemHolderPreprocessor[]
     */
    protected $itemHolderPreprocessors;

    /**
     * @var ArrayCollection|ItemValidator[]
     */
    protected $itemValidators;

    /**
     * @var ArrayCollection|ItemHolderValidator[]
     */
    protected $itemHolderValidators;

    /**
     * @var ArrayCollection|PurchaseProcessor[]
     */
    protected $purchaseProcessors;

    public function __construct()
    {
        $this->purchaseProcessors = new ArrayCollection();
        $this->itemValidators = new ArrayCollection();
        $this->itemHolderValidators = new ArrayCollection();
        $this->itemPreprocessors = new ArrayCollection();
        $this->itemHolderPreprocessors = new ArrayCollection();
    }

    public function setPurchaseProcessors(ArrayCollection $processors)
    {
        $this->purchaseProcessors = $processors;
    }

    public function setItemValidators(ArrayCollection $itemValidators)
    {
        $this->itemValidators = $itemValidators;
    }

    public function setItemHolderValidators(ArrayCollection $itemHolderValidators)
    {
        $this->itemHolderValidators = $itemHolderValidators;
    }

    public function setItemPreprocessors(ArrayCollection $itemPreprocessors)
    {
        $this->itemPreprocessors = $itemPreprocessors;
    }

    public function setItemHolderPreprocessors(ArrayCollection $itemHolderPreprocessors)
    {
        $this->itemHolderPreprocessors = $itemHolderPreprocessors;
    }

    public function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $this->calculateAll($itemHolder);

        $flowResult = new PurchaseFlowResult($itemHolder);

        foreach ($itemHolder->getItems() as $item) {
            foreach ($this->itemPreprocessors as $itemPreprocessor) {
                $itemPreprocessor->process($item, $context);
            }
        }

        $this->calculateAll($itemHolder);

        foreach ($this->itemHolderPreprocessors as $holderPreprocessor) {
            $holderPreprocessor->process($itemHolder, $context);
        }

        $this->calculateAll($itemHolder);

        foreach ($itemHolder->getItems() as $item) {
            foreach ($this->itemValidators as $itemValidator) {
                $result = $itemValidator->execute($item, $context);
                $flowResult->addProcessResult($result);
            }
        }

        $this->calculateAll($itemHolder);

        foreach ($this->itemHolderValidators as $itemHolderValidator) {
            $result = $itemHolderValidator->execute($itemHolder, $context);
            $flowResult->addProcessResult($result);
        }

        $this->calculateAll($itemHolder);

        return $flowResult;
    }

    /**
     * 購入フロー仮確定処理.
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext $context
     *
     * @throws PurchaseException
     */
    public function prepare(ItemHolderInterface $target, PurchaseContext $context)
    {
        foreach ($this->purchaseProcessors as $processor) {
            $processor->prepare($target, $context);
        }
    }

    /**
     * 購入フロー確定処理.
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext     $context
     *
     * @throws PurchaseException
     */
    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        foreach ($this->purchaseProcessors as $processor) {
            $processor->commit($target, $context);
        }
    }

    /**
     * 購入フロー仮確定取り消し処理.
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext $context
     */
    public function rollback(ItemHolderInterface $target, PurchaseContext $context)
    {
        foreach ($this->purchaseProcessors as $processor) {
            $processor->rollback($target, $context);
        }
    }

    public function addPurchaseProcessor(PurchaseProcessor $processor)
    {
        $this->purchaseProcessors[] = $processor;
    }

    public function addItemHolderPreprocessor(ItemHolderPreprocessor $holderPreprocessor)
    {
        $this->itemHolderPreprocessors[] = $holderPreprocessor;
    }

    public function addItemPreprocessor(ItemPreprocessor $itemPreprocessor)
    {
        $this->itemPreprocessors[] = $itemPreprocessor;
    }

    public function addItemValidator(ItemValidator $itemValidator)
    {
        $this->itemValidators[] = $itemValidator;
    }

    public function addItemHolderValidator(ItemHolderValidator $itemHolderValidator)
    {
        $this->itemHolderValidators[] = $itemHolderValidator;
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
        if ($itemHolder instanceof Order) {
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
        if ($itemHolder instanceof Order) {
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
                if ($item instanceof OrderItem) {
                    $sum += $item->getTax() * $item->getQuantity();
                } else {
                    $sum += ($item->getPriceIncTax() - $item->getPrice()) * $item->getQuantity();
                }

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
