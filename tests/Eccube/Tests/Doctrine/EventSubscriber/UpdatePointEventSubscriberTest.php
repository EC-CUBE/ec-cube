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

namespace Eccube\Tests\Doctrine\EventSubscriber;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Tests\EccubeTestCase;

class UpdatePointEventSubscriberTest extends EccubeTestCase
{
    /** @var Customer */
    protected $Customer;
    /** @var Order */
    protected $Order;

    public function setUp()
    {
        parent::setUp();

        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $OrderNew = $this->entityManager->find(OrderStatus::class, OrderStatus::NEW);
        $this->Order->setOrderStatus($OrderNew);
        $this->entityManager->flush($this->Order);
    }

    public function testAddPoint()
    {
        $prevPoint = $this->Customer->getPoint();
        $this->Order->setAddPoint(100);
        $OrderDeliv = $this->entityManager->find(OrderStatus::class, OrderStatus::DELIVERED);

        $this->Order->setOrderStatus($OrderDeliv);
        $this->entityManager->flush($this->Order);

        $this->actual = $this->Customer->getPoint();
        $this->expected = $prevPoint + 100;
        $this->verify();
    }

    public function testReturnOfAddPoint()
    {
        $OrderDeliv = $this->entityManager->find(OrderStatus::class, OrderStatus::DELIVERED);
        $this->Order->setAddPoint(100);
        $this->Order->setOrderStatus($OrderDeliv);
        $this->entityManager->flush($this->Order);

        $prevPoint = $this->Customer->getPoint();
        // Return of add point
        $OrderNew = $this->entityManager->find(OrderStatus::class, OrderStatus::NEW);
        $this->Order->setOrderStatus($OrderNew);
        $this->entityManager->flush($this->Order);

        $this->actual = $this->Customer->getPoint();
        $this->expected = $prevPoint - 100;
        $this->verify();
    }

    public function testUsePoint()
    {
        $prevPoint = $this->Customer->getPoint();
        $this->Order->setUsePoint(100);
        $OrderCancel = $this->entityManager->find(OrderStatus::class, OrderStatus::CANCEL);
        $this->Order->setOrderStatus($OrderCancel);
        $this->entityManager->flush($this->Order);

        $OrderNew = $this->entityManager->find(OrderStatus::class, OrderStatus::NEW);
        $this->Order->setOrderStatus($OrderNew);
        $this->entityManager->flush($this->Order);

        $this->actual = $this->Customer->getPoint();
        $this->expected = $prevPoint - 100;
        $this->verify();
    }

    public function testReturnOfUsePoint()
    {
        $prevPoint = $this->Customer->getPoint();
        $this->Order->setUsePoint(100);
        $this->entityManager->flush($this->Order);

        $OrderCancel = $this->entityManager->find(OrderStatus::class, OrderStatus::CANCEL);
        $this->Order->setOrderStatus($OrderCancel);
        $this->entityManager->flush($this->Order);

        $this->actual = $this->Customer->getPoint();
        $this->expected = $prevPoint + 100;
        $this->verify();
    }
}
