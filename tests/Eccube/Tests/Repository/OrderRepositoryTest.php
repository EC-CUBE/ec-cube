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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\OrderRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * OrderRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderRepositoryTest extends EccubeTestCase
{
    /** @var Customer */
    protected $Customer;
    /** @var Order */
    protected $Order;

    /** @var OrderRepository */
    protected $orderRepository;

    public function setUp()
    {
        parent::setUp();
        $this->orderRepository = $this->container->get(OrderRepository::class);

        $this->createProduct();
        $this->Customer = $this->createCustomer();
        $this->entityManager->persist($this->Customer);
        $this->entityManager->flush();
        $this->Order = $this->createOrder($this->Customer);
    }

    public function testChangeStatusWithCommitted()
    {
        $orderId = $this->Order->getId();
        $Status = $this->entityManager->find(OrderStatus::class, OrderStatus::DELIVERED);

        $this->orderRepository->changeStatus($orderId, $Status);

        $this->assertNotNull($this->Order->getShippingDate());
        $this->expected = 5;
        $this->actual = $this->Order->getOrderStatus()->getId();
        $this->verify();
    }

    public function testChangeStatusWithPayment()
    {
        $orderId = $this->Order->getId();
        $Status = $this->entityManager->find(OrderStatus::class, OrderStatus::PAID);

        $this->orderRepository->changeStatus($orderId, $Status);

        $this->assertNotNull($this->Order->getPaymentDate());
        $this->expected = 6;
        $this->actual = $this->Order->getOrderStatus()->getId();
        $this->verify();
    }

    public function testChangeStatusWithOther()
    {
        $orderId = $this->Order->getId();
        $Status = $this->entityManager->find(OrderStatus::class, OrderStatus::NEW);

        $this->orderRepository->changeStatus($orderId, $Status);

        $this->assertNull($this->Order->getShippingDate());
        $this->assertNull($this->Order->getPaymentDate());
    }

    public function testGetQueryBuilderByCustomer()
    {
        $Customer2 = $this->createCustomer();
        $this->createOrder($this->Customer);
        $this->createOrder($Customer2);

        $qb = $this->orderRepository->getQueryBuilderByCustomer($this->Customer);
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

    public function testGetExistsOrdersByCustomer()
    {
        $Order2 = $this->createOrder($this->Customer);
        $Status = $this->entityManager->find(OrderStatus::class, OrderStatus::PROCESSING);

        $this->orderRepository->changeStatus($Order2->getId(), $Status);

        $this->actual = $Order2;
        $this->expected = $this->orderRepository->getExistsOrdersByCustomer($this->Customer);
        $this->verify();
    }

    public function testGetExistsOrdersByCustomerWithNull()
    {
        $Order2 = $this->createOrder($this->Customer);
        $Status = $this->entityManager->find(OrderStatus::class, OrderStatus::NEW);

        $this->orderRepository->changeStatus($Order2->getId(), $Status);

        $this->assertNull($this->orderRepository->getExistsOrdersByCustomer($this->Customer));
    }
}
