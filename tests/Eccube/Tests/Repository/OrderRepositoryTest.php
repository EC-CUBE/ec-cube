<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Entity\Shipping;
use Eccube\Entity\ShipmentItem;

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

        $this->assertNotNull($this->Order->getCommitDate());
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

        $this->assertNull($this->Order->getCommitDate());
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

    public function testGetNew()
    {
        $NewStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $CancelStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_cancel']);
        $Customer2 = $this->createCustomer();
        $Order1 = $this->createOrder($this->Customer);
        $Order1->setOrderStatus($NewStatus);
        $Order2 = $this->createOrder($Customer2);
        $Order2->setOrderStatus($CancelStatus);
        $this->app['orm.em']->flush();

        $Orders = $this->app['eccube.repository.order']->getNew();
        $this->expected = 2;
        $this->actual = count($Orders);
        $this->verify();
    }
}
