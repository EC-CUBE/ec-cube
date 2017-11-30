<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;

/**
 * OrderRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderRepositoryTest extends EccubeTestCase
{
    protected $Customer;
    protected $Order;

    public function setUp() {
        parent::setUp();
        $this->createProduct();
        $this->Customer = $this->createCustomer();
        $this->app['orm.em']->persist($this->Customer);
        $this->app['orm.em']->flush();

        $this->Order = $this->createOrder($this->Customer);
    }

    public function testChangeStatusWithCommitted()
    {
        $orderId = $this->Order->getId();
        $Status = $this->app['eccube.repository.order_status']->find(5);

        $this->app['eccube.repository.order']->changeStatus($orderId, $Status);

        $this->assertNotNull($this->Order->getShippingDate());
        $this->expected = 5;
        $this->actual = $this->Order->getOrderStatus()->getId();
        $this->verify();
    }

    public function testChangeStatusWithPayment()
    {
        $orderId = $this->Order->getId();
        $Status = $this->app['eccube.repository.order_status']->find(6);

        $this->app['eccube.repository.order']->changeStatus($orderId, $Status);

        $this->assertNotNull($this->Order->getPaymentDate());
        $this->expected = 6;
        $this->actual = $this->Order->getOrderStatus()->getId();
        $this->verify();
    }

    public function testChangeStatusWithOther()
    {
        $orderId = $this->Order->getId();
        $Status = $this->app['eccube.repository.order_status']->find(1);

        $this->app['eccube.repository.order']->changeStatus($orderId, $Status);

        $this->assertNull($this->Order->getShippingDate());
        $this->assertNull($this->Order->getPaymentDate());
    }

    public function testGetQueryBuilderByCustomer()
    {
        $Customer2 = $this->createCustomer();
        $this->createOrder($this->Customer);
        $this->createOrder($Customer2);

        $qb = $this->app['eccube.repository.order']->getQueryBuilderByCustomer($this->Customer);
        $Orders = $qb->getQuery()->getResult();

        $this->expected = 2;
        $this->actual = count($Orders);
        $this->verify();
    }

    public function testGetShippings()
    {
        $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $this->Order->getShippings());
        $this->assertEquals(1, $this->Order->getShippings()->count());
    }
}
