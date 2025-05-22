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

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Cart;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\PointRateProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PointRateProcessorTest extends EccubeTestCase
{
    /** @var PointRateProcessor */
    private $processor;

    /** @var BaseInfo */
    private $BaseInfo;

    /** @var Order */
    private $Order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = static::getContainer()->get(PointRateProcessor::class);
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $this->Order = $this->createOrder($this->createCustomer());
        foreach($this->Order->getOrderItems() as $OrderItem) {
            $OrderItem->setPointRate(null);
        }
    }

    public function testExecute()
    {
        $this->processor->execute($this->Order, new PurchaseContext());

        foreach ($this->Order->getOrderItems() as $OrderItem) {
            $this->assertEquals($OrderItem->getPointRate(), $this->BaseInfo->getBasicPointRate());
        }
    }

    public function testExecuteProductPointRate()
    {
        $baseRate = $this->BaseInfo->getBasicPointRate();
        $productPointRate = $baseRate + 1;

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $OrderItem->getProductClass()->setPointRate($productPointRate);
        }

        $this->processor->execute($this->Order, new PurchaseContext());

        foreach ($this->Order->getOrderItems() as $OrderItem) {
            if ($OrderItem->isProduct()) {
                $this->assertEquals($OrderItem->getPointRate(), $productPointRate);
            } else {
                $this->assertEquals($OrderItem->getPointRate(), $baseRate);
            }
        }
    }

    public function testExecuteCart()
    {
        $Cart = new Cart();
        $result = $this->processor->execute($Cart, new PurchaseContext());
        $this->assertInstanceOf( ProcessResult::class, $result);
    }
}
