<?php

namespace Eccube\Tests\Repository;

use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * OrderRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderRepositoryTest extends EccubeTestCase
{
    /** @var  Customer */
    protected $Customer;
    /** @var  Order */
    protected $Order;

    /** @var  OrderStatusRepository */
    protected $orderStatusRepo;
    /** @var  OrderRepository */
    protected $orderRepo;

    public function setUp() {
        parent::setUp();
        $this->orderStatusRepo = $this->container->get(OrderStatusRepository::class);
        $this->orderRepo = $this->container->get(OrderRepository::class);

        $this->createProduct();
        $this->Customer = $this->createCustomer();
        $this->entityManager->persist($this->Customer);
        $this->entityManager->flush();
        $this->Order = $this->createOrder($this->Customer);
    }

    public function testChangeStatusWithCommitted()
    {
        $orderId = $this->Order->getId();
        $Status = $this->orderStatusRepo->find(5);

        $this->orderRepo->changeStatus($orderId, $Status);

        $this->assertNotNull($this->Order->getShippingDate());
        $this->expected = 5;
        $this->actual = $this->Order->getOrderStatus()->getId();
        $this->verify();
    }

    public function testChangeStatusWithPayment()
    {
        $orderId = $this->Order->getId();
        $Status = $this->orderStatusRepo->find(6);

        $this->orderRepo->changeStatus($orderId, $Status);

        $this->assertNotNull($this->Order->getPaymentDate());
        $this->expected = 6;
        $this->actual = $this->Order->getOrderStatus()->getId();
        $this->verify();
    }

    public function testChangeStatusWithOther()
    {
        $orderId = $this->Order->getId();
        $Status = $this->orderStatusRepo->find(1);

        $this->orderRepo->changeStatus($orderId, $Status);

        $this->assertNull($this->Order->getShippingDate());
        $this->assertNull($this->Order->getPaymentDate());
    }

    public function testGetQueryBuilderByCustomer()
    {
        $Customer2 = $this->createCustomer();
        $this->createOrder($this->Customer);
        $this->createOrder($Customer2);

        $qb = $this->orderRepo->getQueryBuilderByCustomer($this->Customer);
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
