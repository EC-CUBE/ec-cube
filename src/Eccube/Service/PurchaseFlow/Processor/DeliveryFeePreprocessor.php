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
    protected BaseInfo $BaseInfo;

    /**
     * DeliveryFeePreprocessor constructor.
     */
    public function __construct(
        BaseInfoRepository $baseInfoRepository,
        protected EntityManagerInterface $entityManager,
        protected TaxRuleRepository $taxRuleRepository,
        protected DeliveryFeeRepository $deliveryFeeRepository,
    ) {
        $this->BaseInfo = $baseInfoRepository->get();
    }

    /**
     * @param ItemHolderInterface $itemHolder カート or 注文
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    #[\Override]
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        if ($itemHolder instanceof Order) {
            $this->removeDeliveryFeeItem($itemHolder);
            $this->saveDeliveryFeeItem($itemHolder);
        }
    }

    private function removeDeliveryFeeItem(ItemHolderInterface $itemHolder): void
    {
        if ($itemHolder instanceof Order) {
            foreach ($itemHolder->getShippings() as $Shipping) {
                /** @var OrderItem $item */
                foreach ($Shipping->getOrderItems() as $item) {
                    if ($item->getProcessorName() == DeliveryFeePreprocessor::class) {
                        $Shipping->removeOrderItem($item);
                        $itemHolder->removeOrderItem($item);
                        $this->entityManager->remove($item);
                    }
                }
            }
        }
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     */
    private function saveDeliveryFeeItem(ItemHolderInterface $itemHolder): void
    {
        $DeliveryFeeType = $this->entityManager
            ->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $TaxInclude = $this->entityManager
            ->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxation = $this->entityManager
            ->find(TaxType::class, TaxType::TAXATION);

        /** @var Order $Order */
        $Order = $itemHolder;
        /* @var Shipping $Shipping */
        foreach ($Order->getShippings() as $Shipping) {
            // 送料の計算
            $deliveryFeeProduct = '0';
            if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                /** @var OrderItem $item */
                foreach ($Shipping->getOrderItems() as $item) {
                    if (!$item->isProduct()) {
                        continue;
                    }
                    $deliveryFeeProduct = bcadd($deliveryFeeProduct, bcmul((string) $item->getProductClass()->getDeliveryFee(), (string) $item->getQuantity(), 0));
                }
            }

            /** @var DeliveryFee|null $DeliveryFee */
            $DeliveryFee = $this->deliveryFeeRepository->findOneBy([
                'Delivery' => $Shipping->getDelivery(),
                'Pref' => $Shipping->getPref(),
            ]);
            $fee = is_object($DeliveryFee) ? $DeliveryFee->getFee() : '0';

            $OrderItem = new OrderItem();
            $OrderItem->setProductName($DeliveryFeeType->getName())
                ->setPrice(bcadd($fee, $deliveryFeeProduct))
                ->setQuantity('1')
                ->setOrderItemType($DeliveryFeeType)
                ->setShipping($Shipping)
                ->setOrder($Order)
                ->setTaxDisplayType($TaxInclude)
                ->setTaxType($Taxation)
                ->setProcessorName(DeliveryFeePreprocessor::class);

            $itemHolder->addItem($OrderItem);
            $Shipping->addOrderItem($OrderItem);
        }
    }
}
