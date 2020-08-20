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

    public function testUpdateOrderSummary()
    {
        $Customer = $this->createCustomer();
        $this->orderRepository->updateOrderSummary($Customer);

        self::assertNull($Customer->getFirstBuyDate());
        self::assertNull($Customer->getLastBuyDate());
        self::assertSame(0, $Customer->getBuyTimes());
        self::assertSame(0, $Customer->getBuyTotal());

        $Order1 = $this->createOrder($Customer);
        $Order1->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();

        $this->orderRepository->updateOrderSummary($Customer);
        self::assertSame($Order1->getOrderDate(), $Customer->getFirstBuyDate());
        self::assertSame($Order1->getOrderDate(), $Customer->getLastBuyDate());
        self::assertEquals(1, $Customer->getBuyTimes());
        self::assertEquals($Order1->getTotal(), $Customer->getBuyTotal());

        $Order2 = $this->createOrder($Customer);
        $Order2->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();

        $this->orderRepository->updateOrderSummary($Customer);
        self::assertSame($Order1->getOrderDate(), $Customer->getFirstBuyDate());
        self::assertSame($Order2->getOrderDate(), $Customer->getLastBuyDate());
        self::assertEquals(2, $Customer->getBuyTimes());
        self::assertEquals($Order1->getTotal() + $Order2->getTotal(), $Customer->getBuyTotal());
    }

    public function testGetQueryBuilderBySearchDataForAdmin_multi_2147483648()
    {
        $Order = $this->createOrder($this->createCustomer('2147483648@example.com'));
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->orderRepository->save($Order);
        $this->entityManager->flush();;

        $actual = $this->orderRepository->getQueryBuilderBySearchDataForAdmin(['multi' => '2147483648'])
            ->getQuery()
            ->getResult();

        self::assertEquals($Order, $actual[0]);
    }
}
