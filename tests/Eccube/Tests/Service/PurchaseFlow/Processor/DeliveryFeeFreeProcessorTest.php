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


use Eccube\Entity\BaseInfo;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeFreeProcessor;
use Eccube\Service\PurchaseFlow\Processor\PurchaseContext;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Tests\EccubeTestCase;

class DeliveryFeeFreeProcessorTest extends EccubeTestCase
{
    /**
     * @var DeliveryFeeFreeProcessor
     */
    protected $processor;

    /**
     * @var BaseInfo
     */
    protected $Order;

    public function setUp()
    {
        parent::setUp();

        $this->processor = new DeliveryFeeFreeProcessor($this->app);
        $this->Order = $this->createOrder($this->createCustomer());
    }

    public function testNewInstance()
    {
        self::assertInstanceOf(DeliveryFeeFreeProcessor::class, $this->processor);
    }

    /**
     * 送料無料条件に合致しない場合
     */
    public function testProcess()
    {
        $result = $this->processor->process($this->Order, PurchaseContext::create($this->app));
        self::assertInstanceOf(ProcessResult::class, $result);
        self::assertFalse($result->isError());

        $items = $this->getDeliveryFeeItems($this->Order);
        foreach ($items as $item) {
            self::assertEquals(1, $item->getQuantity());
        }
    }

    /**
     * 送料無料条件(金額)に合致する場合, 送料明細の個数は0になる
     */
    public function testProcessWithAmount()
    {
        /** @var BaseInfo $BaseInfo */
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setDeliveryFreeAmount(1); // 1円以上で送料無料

        $result = $this->processor->process($this->Order, PurchaseContext::create($this->app));
        self::assertInstanceOf(ProcessResult::class, $result);
        self::assertFalse($result->isError());

        $items = $this->getDeliveryFeeItems($this->Order);
        foreach ($items as $item) {
            self::assertEquals(0, $item->getQuantity());
        }
    }

    /**
     * 送料無料条件(個数)に合致する場合, 送料明細の個数は0になる
     */
    public function testProcessWithQuantity()
    {
        /** @var BaseInfo $BaseInfo */
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setDeliveryFreeQuantity(1); // 1個以上で送料無料

        $result = $this->processor->process($this->Order, PurchaseContext::create($this->app));
        self::assertInstanceOf(ProcessResult::class, $result);
        self::assertFalse($result->isError());

        $items = $this->getDeliveryFeeItems($this->Order);
        foreach ($items as $item) {
            self::assertEquals(0, $item->getQuantity());
        }
    }

    private function getDeliveryFeeItems(Order $Order)
    {
        $deliveryFeeItems = [];
        foreach ($Order->getShipmentItems() as $item) {
            if ($item->isDeliveryFee()) {
                $deliveryFeeItems[] = $item;
            }
        }

        return $deliveryFeeItems;
    }
}
