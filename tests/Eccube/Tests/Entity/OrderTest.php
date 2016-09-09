<?php

namespace Eccube\Tests\Entity;

use Eccube\Common\Constant;
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
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule();
        $this->rate = $TaxRule->getTaxRate();
    }

    public function testConstructor()
    {
        $OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_processing']);
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
        $this->assertSame(Constant::DISABLED, $Order->getDelFlg());
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
        $this->assertSame(Constant::DISABLED, $Order->getDelFlg());
    }

    public function testGetSubTotal()
    {
        $quantity = 3;
        $price = 100;
        $rows = count($this->Order->getOrderDetails());

        $subTotal = 0;
        foreach ($this->Order->getOrderDetails() as $Detail) {
            $Detail->setPrice($price);
            $Detail->setQuantity($quantity);
            $subTotal = $Detail->getPriceIncTax() * $Detail->getQuantity();
        }
        $this->Order->setSubTotal($subTotal);
        $this->app['orm.em']->flush();

        $Result = $this->app['eccube.repository.order']->find($this->Order->getId());

        $this->expected = ($price + ($price * ($this->rate / 100))) * $quantity * $rows;
        $this->actual = $Result->calculateSubTotal();
        $this->verify();
    }

    public function testGetTotalQuantity()
    {
        $quantity = 3;
        $rows = count($this->Order->getOrderDetails());

        $total = 0;
        foreach ($this->Order->getOrderDetails() as $Detail) {
            $Detail->setQuantity($quantity);
            $total += $Detail->getQuantity();
        }
        $this->app['orm.em']->flush();

        $Result = $this->app['eccube.repository.order']->find($this->Order->getId());

        $this->expected = $total;
        $this->actual = $Result->calculateTotalQuantity();
        $this->verify();
    }

    public function testGetTotalTax()
    {
        $quantity = 3;
        $price = 100;
        $rows = count($this->Order->getOrderDetails());

        $totalTax = 0;
        foreach ($this->Order->getOrderDetails() as $Detail) {
            $Detail->setPrice($price);
            $Detail->setQuantity($quantity);
            $totalTax += ($Detail->getPriceIncTax() - $Detail->getPrice()) * $Detail->getQuantity();
        }
        $this->app['orm.em']->flush();

        $Result = $this->app['eccube.repository.order']->find($this->Order->getId());

        $this->expected = ($price * ($this->rate / 100)) * $quantity * $rows;
        $this->actual = $Result->calculateTotalTax();
        $this->verify();
    }

    public function testGetProductTypes()
    {
        $this->expected = array($this->app['eccube.repository.master.product_type']->find(1));
        $this->actual = $this->Order->getProductTypes();
        $this->verify();
    }

    public function testGetTotalPrice()
    {
        $faker = $this->getFaker();
        $Order = $this->app['eccube.fixture.generator']->createOrder(
            $this->Customer,
            array(),
            null,
            $faker->randomNumber(5),
            $faker->randomNumber(5)
        );
        $this->expected = $Order->getSubTotal() + $Order->getCharge() + $Order->getDeliveryFeeTotal() - $Order->getDiscount();
        $this->actual = $Order->getTotalPrice();
        $this->verify();
    }
}
