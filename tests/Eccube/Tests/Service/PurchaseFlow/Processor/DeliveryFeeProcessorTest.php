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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DeliveryFeeProcessorTest extends EccubeTestCase
{
    public function testProcess()
    {
        $processor = $this->container->get(DeliveryFeeProcessor::class);
        $Order = $this->createOrder($this->createCustomer());
        /*
         * @var OrderItem
         */
        foreach ($Order->getOrderItems() as $OrderItem) {
            if ($OrderItem->isDeliveryFee()) {
                $Order->getOrderItems()->removeElement($OrderItem);
            }
        }
        $processor->process($Order, new PurchaseContext());
        self::assertNotEmpty($this->getDeliveryFees($Order));
    }

    /**
     * すでに送料がある場合は送料を追加しない.
     */
    public function testProcessWithDeliveryFee()
    {
        $processor = $this->container->get(DeliveryFeeProcessor::class);
        $Order = $this->createOrder($this->createCustomer());
        /*
         * @var OrderItem
         */
        foreach ($Order->getOrderItems() as $OrderItem) {
            if ($OrderItem->isDeliveryFee()) {
                $Order->getOrderItems()->removeElement($OrderItem);
            }
        }

        $DeliveryFee = new OrderItem();
        $OrderItemType = new OrderItemType();
        $OrderItemType->setId(OrderItemType::DELIVERY_FEE);
        $DeliveryFee->setOrderItemType($OrderItemType);
        $Order->addItem($DeliveryFee);

        $processor->process($Order, new PurchaseContext());

        $DeliveryFeeList = $this->getDeliveryFees($Order);
        self::assertCount(1, $DeliveryFeeList);
        self::assertSame($DeliveryFee, array_shift($DeliveryFeeList));
    }

    private function getDeliveryFees(Order $Order)
    {
        return array_filter($Order->getOrderItems()->toArray(), function ($OrderItem) {
            return $OrderItem->isDeliveryFee();
        });
    }
}
