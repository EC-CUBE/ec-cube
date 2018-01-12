<?php

namespace Eccube\Tests\Entity;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Tests\EccubeTestCase;

/**
 * AbstractEntity test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderTest extends EccubeTestCase
{
    protected $Customer;
    protected $Order;
    protected $rate;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule();
        $this->rate = $TaxRule->getTaxRate();
    }

    public function testConstructor()
    {
        $OrderStatus = $this->app['eccube.repository.order_status']->find(OrderStatus::PROCESSING);
        $Order = new Order($OrderStatus);

        $this->expected = 0;

        $this->actual = $Order->getDiscount();
        $this->verify();

        $this->actual = $Order->getSubTotal();
        $this->verify();

        $this->actual = $Order->getTotal();
        $this->verify();

        $this->actual = $Order->getPaymentTotal();
        $this->verify();

        $this->actual = $Order->getCharge();
        $this->verify();

        $this->actual = $Order->getTax();
        $this->verify();

        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();

        $this->assertSame($OrderStatus, $Order->getOrderStatus());
    }

    public function testConstructor2()
    {
        $Order = new Order();

        $this->expected = 0;

        $this->actual = $Order->getDiscount();
        $this->verify();

        $this->actual = $Order->getSubTotal();
        $this->verify();

        $this->actual = $Order->getTotal();
        $this->verify();

        $this->actual = $Order->getPaymentTotal();
        $this->verify();

        $this->actual = $Order->getCharge();
        $this->verify();

        $this->actual = $Order->getTax();
        $this->verify();

        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();

        $this->assertNull($Order->getOrderStatus());
    }

    public function testGetSaleTypes()
    {
        $this->expected = array($this->app['eccube.repository.master.sale_type']->find(1));
        $this->actual = $this->Order->getSaleTypes();
        $this->verify();
    }

    public function testGetTotalPrice()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $faker = $this->getFaker();
        $Order = $this->app['eccube.fixture.generator']->createOrder(
            $this->Customer,
            array(),
            null,
            $faker->randomNumber(5),
            $faker->randomNumber(5)
        );
        $this->expected = $Order->getSubTotal() + $Order->getCharge() - $Order->getDiscount();
        $this->actual = $Order->getTotalPrice();
        $this->verify();
    }
}
