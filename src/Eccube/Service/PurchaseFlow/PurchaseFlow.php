<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\PurchaseFlow;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Order;

class PurchaseFlow implements \Stringable
{
    /**
     * @var string
     */
    protected $flowType;

    /**
     * @var ArrayCollection<int, ItemPreprocessor>
     */
    protected $itemPreprocessors;

    /**
     * @var ArrayCollection<int, ItemHolderPreprocessor>
     */
    protected $itemHolderPreprocessors;

    /**
     * @var ArrayCollection<int, ItemValidator>
     */
    protected $itemValidators;

    /**
     * @var ArrayCollection<int, ItemHolderValidator>
     */
    protected $itemHolderValidators;

    /**
     * @var ArrayCollection<int, ItemHolderPostValidator>
     */
    protected $itemHolderPostValidators;

    /**
     * @var ArrayCollection<int, DiscountProcessor>
     */
    protected $discountProcessors;

    /**
     * @var ArrayCollection<int, PurchaseProcessor>
     */
    protected $purchaseProcessors;

    public function __construct()
    {
        $this->purchaseProcessors = new ArrayCollection();
        $this->itemValidators = new ArrayCollection();
        $this->itemHolderValidators = new ArrayCollection();
        $this->itemPreprocessors = new ArrayCollection();
        $this->itemHolderPreprocessors = new ArrayCollection();
        $this->itemHolderPostValidators = new ArrayCollection();
        $this->discountProcessors = new ArrayCollection();
    }

    /**
     * @param string $flowType
     *
     * @return void
     */
    public function setFlowType($flowType): void
    {
        $this->flowType = $flowType;
    }

    /**
     * @param ArrayCollection<int, PurchaseProcessor> $processors
     *
     * @return void
     */
    public function setPurchaseProcessors(ArrayCollection $processors): void
    {
        $this->purchaseProcessors = $processors;
    }

    /**
     * @param ArrayCollection<int, ItemValidator> $itemValidators
     *
     * @return void
     */
    public function setItemValidators(ArrayCollection $itemValidators): void
    {
        $this->itemValidators = $itemValidators;
    }

    /**
     * @param ArrayCollection<int, ItemHolderValidator> $itemHolderValidators
     *
     * @return void
     */
    public function setItemHolderValidators(ArrayCollection $itemHolderValidators): void
    {
        $this->itemHolderValidators = $itemHolderValidators;
    }

    /**
     * @param ArrayCollection<int, ItemPreprocessor> $itemPreprocessors
     *
     * @return void
     */
    public function setItemPreprocessors(ArrayCollection $itemPreprocessors): void
    {
        $this->itemPreprocessors = $itemPreprocessors;
    }

    /**
     * @param ArrayCollection<int, ItemHolderPreprocessor> $itemHolderPreprocessors
     *
     * @return void
     */
    public function setItemHolderPreprocessors(ArrayCollection $itemHolderPreprocessors): void
    {
        $this->itemHolderPreprocessors = $itemHolderPreprocessors;
    }

    /**
     * @param ArrayCollection<int, ItemHolderPostValidator> $itemHolderPostValidators
     *
     * @return void
     */
    public function setItemHolderPostValidators(ArrayCollection $itemHolderPostValidators): void
    {
        $this->itemHolderPostValidators = $itemHolderPostValidators;
    }

    /**
     * @param ArrayCollection<int, DiscountProcessor> $discountProcessors
     *
     * @return void
     */
    public function setDiscountProcessors(ArrayCollection $discountProcessors): void
    {
        $this->discountProcessors = $discountProcessors;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @return PurchaseFlowResult
     */
    public function validate(ItemHolderInterface $itemHolder, PurchaseContext $context): PurchaseFlowResult
    {
        $context->setFlowType($this->flowType);

        $this->calculateAll($itemHolder);

        $flowResult = new PurchaseFlowResult($itemHolder);

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

        foreach ($itemHolder->getItems() as $item) {
            foreach ($this->itemPreprocessors as $itemPreprocessor) {
                $itemPreprocessor->process($item, $context);
            }
        }

        $this->calculateAll($itemHolder);

        foreach ($this->itemHolderPreprocessors as $holderPreprocessor) {
            $holderPreprocessor->process($itemHolder, $context);
            $this->calculateAll($itemHolder);
        }

        foreach ($this->discountProcessors as $discountProcessor) {
            $discountProcessor->removeDiscountItem($itemHolder, $context);
        }

        $this->calculateAll($itemHolder);

        foreach ($this->discountProcessors as $discountProcessor) {
            $result = $discountProcessor->addDiscountItem($itemHolder, $context);
            if ($result) {
                $flowResult->addProcessResult($result);
            }
            $this->calculateAll($itemHolder);
        }

        foreach ($this->itemHolderPostValidators as $itemHolderPostValidator) {
            $result = $itemHolderPostValidator->execute($itemHolder, $context);
            $flowResult->addProcessResult($result);

            $this->calculateAll($itemHolder);
        }

        return $flowResult;
    }

    /**
     * 購入フロー仮確定処理.
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext $context
     *
     * @return void
     *
     * @throws PurchaseException
     */
    public function prepare(ItemHolderInterface $target, PurchaseContext $context): void
    {
        $context->setFlowType($this->flowType);

        foreach ($this->purchaseProcessors as $processor) {
            $processor->prepare($target, $context);
        }
    }

    /**
     * 購入フロー確定処理.
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext $context
     *
     * @return void
     *
     * @throws PurchaseException
     */
    public function commit(ItemHolderInterface $target, PurchaseContext $context): void
    {
        $context->setFlowType($this->flowType);

        foreach ($this->purchaseProcessors as $processor) {
            $processor->commit($target, $context);
        }
    }

    /**
     * 購入フロー仮確定取り消し処理.
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext $context
     *
     * @return void
     */
    public function rollback(ItemHolderInterface $target, PurchaseContext $context): void
    {
        $context->setFlowType($this->flowType);

        foreach ($this->purchaseProcessors as $processor) {
            $processor->rollback($target, $context);
        }
    }

    /**
     * @param PurchaseProcessor $purchaseProcessor
     *
     * @return void
     */
    public function addPurchaseProcessor(PurchaseProcessor $purchaseProcessor): void
    {
        $this->purchaseProcessors[] = $purchaseProcessor;
    }

    /**
     * @param ItemHolderPreprocessor $itemHolderPreprocessor
     *
     * @return void
     */
    public function addItemHolderPreprocessor(ItemHolderPreprocessor $itemHolderPreprocessor): void
    {
        $this->itemHolderPreprocessors[] = $itemHolderPreprocessor;
    }

    /**
     * @param ItemPreprocessor $itemPreprocessor
     *
     * @return void
     */
    public function addItemPreprocessor(ItemPreprocessor $itemPreprocessor): void
    {
        $this->itemPreprocessors[] = $itemPreprocessor;
    }

    /**
     * @param ItemValidator $itemValidator
     *
     * @return void
     */
    public function addItemValidator(ItemValidator $itemValidator): void
    {
        $this->itemValidators[] = $itemValidator;
    }

    /**
     * @param ItemHolderValidator $itemHolderValidator
     *
     * @return void
     */
    public function addItemHolderValidator(ItemHolderValidator $itemHolderValidator): void
    {
        $this->itemHolderValidators[] = $itemHolderValidator;
    }

    /**
     * @param ItemHolderPostValidator $itemHolderPostValidator
     *
     * @return void
     */
    public function addItemHolderPostValidator(ItemHolderPostValidator $itemHolderPostValidator): void
    {
        $this->itemHolderPostValidators[] = $itemHolderPostValidator;
    }

    /**
     * @param DiscountProcessor $discountProcessor
     *
     * @return void
     */
    public function addDiscountProcessor(DiscountProcessor $discountProcessor): void
    {
        $this->discountProcessors[] = $discountProcessor;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @return void
     */
    protected function calculateTotal(ItemHolderInterface $itemHolder): void
    {
        $total = array_reduce($itemHolder->getItems()->toArray(), function ($sum, ItemInterface $item) {
            $sum = bcadd($sum, bcmul($item->getPriceIncTax(), $item->getQuantity(), 2), 2);

            return $sum;
        }, '0');
        $itemHolder->setTotal($total);
        // TODO
        if ($itemHolder instanceof Order) {
            // Order には PaymentTotal もセットする
            $itemHolder->setPaymentTotal($total);
        }
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @return void
     */
    protected function calculateSubTotal(ItemHolderInterface $itemHolder): void
    {
        $total = $itemHolder->getItems()
            ->getProductClasses()
            ->reduce(function ($sum, ItemInterface $item) {
                $sum = bcadd($sum, bcmul($item->getPriceIncTax(), $item->getQuantity(), 2), 2);

                return $sum;
            }, '0');
        // TODO
        if ($itemHolder instanceof Order) {
            // Order の場合は SubTotal をセットする
            $itemHolder->setSubTotal($total);
        }
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @return void
     */
    protected function calculateDeliveryFeeTotal(ItemHolderInterface $itemHolder): void
    {
        $total = $itemHolder->getItems()
            ->getDeliveryFees()
            ->reduce(function ($sum, ItemInterface $item) {
                $sum = bcadd($sum, bcmul($item->getPriceIncTax(), $item->getQuantity(), 2), 2);

                return $sum;
            }, '0');
        $itemHolder->setDeliveryFeeTotal($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @return void
     */
    protected function calculateDiscount(ItemHolderInterface $itemHolder): void
    {
        $total = $itemHolder->getItems()
            ->getDiscounts()
            ->reduce(function ($sum, ItemInterface $item) {
                $sum = bcadd($sum, bcmul($item->getPriceIncTax(), $item->getQuantity(), 2), 2);

                return $sum;
            }, '0');
        // TODO 後方互換のため discount には正の整数を代入する
        $itemHolder->setDiscount(bcmul((string) $total, '-1', 2));
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @return void
     */
    protected function calculateCharge(ItemHolderInterface $itemHolder): void
    {
        $total = $itemHolder->getItems()
            ->getCharges()
            ->reduce(function ($sum, ItemInterface $item) {
                $sum = bcadd($sum, bcmul($item->getPriceIncTax(), $item->getQuantity(), 2), 2);

                return $sum;
            }, '0');
        $itemHolder->setCharge($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @return void
     */
    protected function calculateTax(ItemHolderInterface $itemHolder): void
    {
        if ($itemHolder instanceof Order) {
            $total = array_reduce($itemHolder->getTaxByTaxRate(), function ($sum, $tax) {
                return bcadd($sum, $tax, 2);
            }, '0');
        } else {
            $total = $itemHolder->getItems()
                ->reduce(function ($sum, ItemInterface $item) {
                    $taxPerItem = bcsub($item->getPriceIncTax(), $item->getPrice(), 2);
                    $sum = bcadd($sum, bcmul($taxPerItem, $item->getQuantity(), 2), 2);

                    return $sum;
                }, '0');
        }
        $itemHolder->setTax($total);
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @return void
     */
    protected function calculateAll(ItemHolderInterface $itemHolder): void
    {
        $this->calculateDeliveryFeeTotal($itemHolder);
        $this->calculateCharge($itemHolder);
        $this->calculateDiscount($itemHolder);
        $this->calculateSubTotal($itemHolder); // Order の場合のみ
        $this->calculateTax($itemHolder);
        $this->calculateTotal($itemHolder);
    }

    /**
     * PurchaseFlow をツリー表示します.
     *
     * @return string
     */
    public function dump(): string
    {
        /** @var \Closure(mixed): mixed $callback */
        $callback = function ($processor) {
            return $processor::class;
        };
        $flows = [
            0 => $this->flowType.' flow',
            'ItemValidator' => $this->itemValidators->map($callback)->toArray(),
            'ItemHolderValidator' => $this->itemHolderValidators->map($callback)->toArray(),
            'ItemPreprocessor' => $this->itemPreprocessors->map($callback)->toArray(),
            'ItemHolderPreprocessor' => $this->itemHolderPreprocessors->map($callback)->toArray(),
            'DiscountProcessor' => $this->discountProcessors->map($callback)->toArray(),
            'ItemHolderPostValidator' => $this->itemHolderPostValidators->map($callback)->toArray(),
        ];
        $tree = new \RecursiveTreeIterator(new \RecursiveArrayIterator($flows));
        $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_RIGHT, ' ');
        $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_MID_LAST, '　');
        $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_MID_HAS_NEXT, '│');
        $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_HAS_NEXT, '├');
        $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_LAST, '└');
        $out = '';
        foreach ($tree as $key => $value) {
            if (is_numeric($key)) {
                $out .= $value.PHP_EOL;
            } else {
                $out .= $key.PHP_EOL;
            }
        }

        return $out;
    }

    /**
     * @return string
     */
    #[\Override]
    public function __toString(): string
    {
        return $this->dump();
    }
}
