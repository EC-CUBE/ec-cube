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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\Processor\PaymentChargePreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PaymentChargePreprocessorTest extends EccubeTestCase
{
    public function testProcess()
    {
        $processor = self::$container->get(PaymentChargePreprocessor::class);
        $Order = $this->createOrder($this->createCustomer());
        /*
         * @var OrderItem
         */
        foreach ($Order->getOrderItems() as $OrderItem) {
            if ($OrderItem->isCharge()) {
                $Order->getOrderItems()->removeElement($OrderItem);
            }
        }
        $processor->process($Order, new PurchaseContext());
        self::assertNotEmpty($this->getChargesItems($Order));
    }

    /**
     * すでに送料がある場合は送料を追加しない.
     */
    public function testProcessWithPaymentCharge()
    {
        $processor = self::$container->get(PaymentChargePreProcessor::class);
        $Order = $this->createOrder($this->createCustomer());
        /*
         * @var OrderItem
         */
        foreach ($Order->getOrderItems() as $OrderItem) {
            if ($OrderItem->isCharge()) {
                $Order->getOrderItems()->removeElement($OrderItem);
            }
        }

        $ChargeItem = new OrderItem();
        $OrderItemType = new OrderItemType();
        $OrderItemType->setId(OrderItemType::CHARGE);
        $ChargeItem->setOrderItemType($OrderItemType);
        $ChargeItem->setProcessorName(PaymentChargePreprocessor::class);
        $Order->addItem($ChargeItem);

        $processor->process($Order, new PurchaseContext());

        $ChargeItems = $this->getChargesItems($Order);
        self::assertCount(1, $ChargeItems);
        self::assertSame($ChargeItem, array_shift($ChargeItems));
    }

    private function getChargesItems(Order $Order)
    {
        return array_filter($Order->getOrderItems()->toArray(), function ($OrderItem) {
            return $OrderItem->isCharge();
        });
    }
}
