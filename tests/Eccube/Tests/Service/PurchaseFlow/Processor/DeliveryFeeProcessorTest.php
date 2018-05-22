<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
        $processor = new DeliveryFeeProcessor($this->entityManager);
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
        $processor = new DeliveryFeeProcessor($this->entityManager);
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
