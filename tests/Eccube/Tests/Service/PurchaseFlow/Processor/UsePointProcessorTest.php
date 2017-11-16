<?php

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Service\PurchaseFlow\Processor\UsePointProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class AddPointProcessorTest extends EccubeTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->BaseInfo = $this->app[BaseInfo::class];
        $this->BaseInfo->setPointConversionRate(10);
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
    }

    public function testProcess()
    {
        $this->Order->setUsePoint(10);
        $OriginalOrder = clone $this->Order;
        $processor = new UsePointProcessor($this->app['orm.em'], $this->BaseInfo);
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
        $processor = new UsePointProcessor($this->app['orm.em'], $this->BaseInfo);
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
        $processor = new UsePointProcessor($this->app['orm.em'], $this->BaseInfo);
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
