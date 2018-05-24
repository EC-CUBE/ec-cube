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

use Eccube\Entity\BaseInfo;
use Eccube\Service\PurchaseFlow\Processor\UsePointProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;

class UsePointProcessorTest extends EccubeTestCase
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var Order
     */
    protected $Order;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->BaseInfo = $this->container->get(BaseInfo::class);
        $this->BaseInfo->setPointConversionRate(10);
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
    }

    public function testProcess()
    {
        $this->Order->setUsePoint(10);
        $OriginalOrder = clone $this->Order;
        $processor = new UsePointProcessor($this->entityManager, $this->BaseInfo);
        $processor->process($this->Order, new PurchaseContext($OriginalOrder, $this->Customer));
        $OrderItem = $this->Order->getOrderItems()->filter(
            function ($OrderItem) {
                return $OrderItem->getProductName() == 'ポイント値引';
            }
        )->first();

        $this->expected = -100;
        $this->actual = $OrderItem->getPrice();
        $this->verify();
    }

    public function testProcessFailureWithCustomerPointOver()
    {
        $this->Order->setUsePoint($this->Customer->getPoint() + 1);
        $OriginalOrder = clone $this->Order;
        $processor = new UsePointProcessor($this->entityManager, $this->BaseInfo);
        $ProcessResult = $processor->process($this->Order, new PurchaseContext($OriginalOrder, $this->Customer));

        self::assertTrue($ProcessResult->isError());
        $OrderItem = $this->Order->getOrderItems()->filter(
            function ($OrderItem) {
                return $OrderItem->getProductName() == 'ポイント値引';
            }
        )->first();
        self::assertFalse($OrderItem);
    }

    public function testProcessFailureWithPriceOver()
    {
        $this->BaseInfo->setPointConversionRate(1);
        $this->Customer->setPoint(PHP_INT_MAX);
        $this->Order->setUsePoint($this->Order->getTotal() + 1);
        $OriginalOrder = clone $this->Order;
        $processor = new UsePointProcessor($this->entityManager, $this->BaseInfo);
        $ProcessResult = $processor->process($this->Order, new PurchaseContext($OriginalOrder, $this->Customer));

        self::assertTrue($ProcessResult->isError());
        $OrderItem = $this->Order->getOrderItems()->filter(
            function ($OrderItem) {
                return $OrderItem->getProductName() == 'ポイント値引';
            }
        )->first();

        self::assertFalse($OrderItem);
    }
}
