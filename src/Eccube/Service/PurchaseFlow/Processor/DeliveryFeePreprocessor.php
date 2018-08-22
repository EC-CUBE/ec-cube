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

namespace Eccube\Service\PurchaseFlow\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 送料明細追加.
 */
class DeliveryFeePreprocessor implements ItemHolderPreprocessor
{
    /** @var BaseInfo */
    protected $BaseInfo;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * DeliveryFeePreprocessor constructor.
     *
     * @param BaseInfoRepository $baseInfoRepository
     * @param EntityManagerInterface $entityManager
     * @param TaxRuleRepository $taxRuleRepository
     * @param DeliveryFeeRepository $deliveryFeeRepository
     */
    public function __construct(
        BaseInfoRepository $baseInfoRepository,
        EntityManagerInterface $entityManager,
        TaxRuleRepository $taxRuleRepository,
        DeliveryFeeRepository $deliveryFeeRepository
    ) {
        $this->BaseInfo = $baseInfoRepository->get();
        $this->entityManager = $entityManager;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->deliveryFeeRepository = $deliveryFeeRepository;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if ($this->containsDeliveryFeeItem($itemHolder) == false) {
            $this->addDeliveryFeeItem($itemHolder);
        }
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @return bool
     */
    private function containsDeliveryFeeItem(ItemHolderInterface $itemHolder)
    {
        foreach ($itemHolder->getItems() as $item) {
            if ($item->isDeliveryFee()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    private function addDeliveryFeeItem(ItemHolderInterface $itemHolder)
    {
        $DeliveryFeeType = $this->entityManager
            ->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $TaxInclude = $this->entityManager
            ->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxion = $this->entityManager
            ->find(TaxType::class, TaxType::TAXATION);

        /** @var Order $Order */
        $Order = $itemHolder;
        /* @var Shipping $Shipping */
        foreach ($Order->getShippings() as $Shipping) {
            // 送料の計算
            $deliveryFeeProduct = 0;
            if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                /** @var OrderItem $orderItem */
                foreach ($Shipping->getOrderItems() as $orderItem) {
                    if (!$orderItem->isProduct()) {
                        continue;
                    }
                    $deliveryFeeProduct += $orderItem->getProductClass()->getDeliveryFee() * $orderItem->getQuantity();
                }
            }

            /** @var DeliveryFee $DeliveryFee */
            $DeliveryFee = $this->deliveryFeeRepository->findOneBy([
                'Delivery' => $Shipping->getDelivery(),
                'Pref' => $Shipping->getPref(),
            ]);

            $Shipping->setShippingDeliveryFee($DeliveryFee->getFee() + $deliveryFeeProduct);
            $Shipping->setFeeId($DeliveryFee->getId());

            $OrderItem = new OrderItem();
            $OrderItem->setProductName(trans('deliveryfeeprocessor.label.shippint_charge'))
                ->setPrice($Shipping->getShippingDeliveryFee())
                ->setQuantity(1)
                ->setOrderItemType($DeliveryFeeType)
                ->setShipping($Shipping)
                ->setOrder($itemHolder)
                ->setTaxDisplayType($TaxInclude)
                ->setTaxType($Taxion);

            $itemHolder->addItem($OrderItem);
            $Shipping->addOrderItem($OrderItem);
        }
    }
}
