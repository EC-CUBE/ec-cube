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

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\SaleType;
use Eccube\Repository\DeliveryRepository;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemHolderProcessor;

/**
 * 支払い方法が一致しない明細がないかどうか.
 */
class PaymentProcessor extends ValidatableItemHolderProcessor
{
    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * PaymentProcessor constructor.
     *
     * @param DeliveryRepository $deliveryRepository
     */
    public function __construct(DeliveryRepository $deliveryRepository)
    {
        $this->deliveryRepository = $deliveryRepository;
    }

    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // 明細の個数が1以下の場合はOK
        if (count($itemHolder->getItems()) <= 1) {
            return;
        }

        // a, ab, c
        $i = 0;
        $paymentIds = [];
        foreach ($itemHolder->getItems() as $item) {
            if (false === $item->isProduct()) {
                continue;
            }
            $Deliveries = $this->getDeliveries($item->getProductClass()->getSaleType());
            $Payments = $this->getPayments($Deliveries);

            $ids = [];
            foreach ($Payments as $Payment) {
                $ids[] = $Payment->getId();
            }
            if ($i === 0) {
                $paymentIds = $ids;
                ++$i;
                continue;
            }

            $paymentIds = array_intersect($paymentIds, $ids);
        }

        // 共通項がなければエラー
        if (empty($paymentIds)) {
            throw new InvalidItemException(trans('paymentprocessor.label.different_payment_method'));
        }
    }

    private function getDeliveries(SaleType $SaleType)
    {
        $Deliveries = $this->deliveryRepository->findBy(
            [
                'SaleType' => $SaleType,
                'visible' => true,
            ]
        );

        return $Deliveries;
    }

    private function getPayments($Deliveries)
    {
        $Payments = new ArrayCollection();
        foreach ($Deliveries as $Delivery) {
            $PaymentOptions = $Delivery->getPaymentOptions();
            foreach ($PaymentOptions as $PaymentOption) {
                $Payments->add($PaymentOption->getPayment());
            }
        }

        return $Payments;
    }
}
