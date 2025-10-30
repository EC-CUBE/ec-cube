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

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Payment;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\TaxDisplayTypeRepository;
use Eccube\Repository\Master\TaxTypeRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;

class PaymentChargePreprocessor implements ItemHolderPreprocessor
{
    protected OrderItemTypeRepository $orderItemTypeRepository;

    protected TaxDisplayTypeRepository $taxDisplayTypeRepository;

    protected TaxTypeRepository $taxTypeRepository;

    /**
     * PaymentChargePreprocessor constructor.
     */
    public function __construct(
        OrderItemTypeRepository $orderItemTypeRepository,
        TaxDisplayTypeRepository $taxDisplayTypeRepository,
        TaxTypeRepository $taxTypeRepository,
    ) {
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->taxDisplayTypeRepository = $taxDisplayTypeRepository;
        $this->taxTypeRepository = $taxTypeRepository;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        if (!$itemHolder instanceof Order) {
            return;
        }
        if (!$itemHolder->getPayment() instanceof Payment || !$itemHolder->getPayment()->getId()) {
            return;
        }
        /** @var OrderItem $item */
        foreach ($itemHolder->getItems() as $item) {
            if ($item->getProcessorName() == PaymentChargePreprocessor::class) {
                $item->setPrice($itemHolder->getPayment()->getCharge());

                return;
            }
        }

        $this->addChargeItem($itemHolder);
    }

    /**
     * Add charge item to item holder
     */
    protected function addChargeItem(ItemHolderInterface $itemHolder): void
    {
        /** @var Order $itemHolder */
        /** @var OrderItemType $OrderItemType */
        $OrderItemType = $this->orderItemTypeRepository->find(OrderItemType::CHARGE);
        /** @var TaxDisplayType $TaxDisplayType */
        $TaxDisplayType = $this->taxDisplayTypeRepository->find(TaxDisplayType::INCLUDED);
        /** @var TaxType $Taxation */
        $Taxation = $this->taxTypeRepository->find(TaxType::TAXATION);
        $item = new OrderItem();
        $item->setProductName($OrderItemType->getName())
            ->setQuantity('1')
            ->setPrice($itemHolder->getPayment()?->getCharge())
            ->setOrderItemType($OrderItemType)
            ->setOrder($itemHolder)
            ->setTaxDisplayType($TaxDisplayType)
            ->setTaxType($Taxation)
            ->setProcessorName(PaymentChargePreprocessor::class);
        $itemHolder->addItem($item);
    }
}
