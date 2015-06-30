<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Service;

use Eccube\Application;

class OrderServiceTest extends AbstractServiceTestCase
{
    protected $app;

    public function setUp()
    {
        parent::setUp();
    }

    public function testNewOrder()
    {
        self::markTestSkipped();
        $Order = $this->app['eccube.service.order']->newOrder();
        $this->assertNotEmpty($Order);
    }

    public function testNewOrderDetail()
    {
        self::markTestSkipped();
        $ProductClass = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find(2);
        $Product = $ProductClass->getProduct();
        $OrderDetail = $this->app['eccube.service.order']->newOrderDetail($Product, $ProductClass, 3);
        $this->assertNotEmpty($OrderDetail);
    }

    public function testCopyToOrderFromCustomer()
    {
        self::markTestSkipped();
        $orderService = $this->app['eccube.service.order'];

        $Order = new \Eccube\Entity\Order();
        $Order = $orderService->copyToOrderFromCustomer($Order, null);
        $this->assertNull($Order->getCustomer());

        $Customer = new \Eccube\Entity\Customer();
        $Customer->setName01('last name');
        $Customer->setName02('first name');
        $Order = $orderService->copyToOrderFromCustomer($Order, $Customer);

        $this->assertEquals($Customer->getName01(), $Order->getName01());
        $this->assertEquals($Customer->getName02(), $Order->getName02());
    }
    public function testCopyToShippingFromCustomer()
    {
        self::markTestSkipped();
        $orderService = $this->app['eccube.service.order'];

        $Shipping = new \Eccube\Entity\Shipping();
        $Shipping = $orderService->copyToShippingFromCustomer($Shipping, null);
        $this->assertTrue(null === $Shipping->getName01());

        $Customer = new \Eccube\Entity\Customer();
        $Customer->setName01('last name');
        $Customer->setName02('first name');
        $Order = $orderService->copyToShippingFromCustomer($Shipping, $Customer);

        $this->assertEquals($Customer->getName01(), $Order->getName01());
        $this->assertEquals($Customer->getName02(), $Order->getName02());
    }

    public function testRegisterPreOrderFromCart()
    {
        self::markTestSkipped();
        $this->app['orm.em']->getConnection()->beginTransaction();

        // set up customer;
        $Customer = $this->newCustomer();
        $this->app['orm.em']->persist($Customer);
        $this->app['orm.em']->flush();

        // set up cart items
        $ProductClass = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find(1);
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();
        $cartService->addProduct($ProductClass->getId());
        $cartService->addProduct($ProductClass->getId());
        $cartService->lock();

        $CarItems = $cartService->getCart()->getCartItems();

        // 受注データ登録
        $Order = $this->app['eccube.service.order']->registerPreOrderFromCartItems($CarItems, $Customer);

        // 登録内容確認
        $this->assertNotEmpty($Order);
        $OrderDetails = $Order->getOrderDetails();
        foreach ($OrderDetails as $detail) {
            $this->assertNotEmpty($detail);
        }

        $Shippings = $Order->getShippings();
        $this->assertNotEmpty($Shippings);
        foreach ($Shippings as $Shipping) {
            $this->assertNotEmpty($Shipping);
            $ShipmentItems = $Shipping->getShipmentItems();
            foreach ($ShipmentItems as $item) {
                $this->assertNotEmpty($item);
            }
        }

        // 購入確定
        $this->app['eccube.service.order']->commit($Order);
        $this->assertEquals(0, $Order->getDelFlg());

        $this->app['orm.em']->getConnection()->rollback();
    }

    public function newCustomer()
    {
        $CustomerStatus = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Master\CustomerStatus')
            ->find(1);
        $Customer = new \Eccube\Entity\Customer();
        $Customer->setName01('last name')
            ->setName02('first name')
            ->setEmail('example@lockon.co.jp')
            ->setSecretKey('dummy' + uniqid())
            ->setStatus($CustomerStatus)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime())
            ->setDelFlg(1);

        return $Customer;
    }

    public function tearDown()
    {
        $this->app['orm.em']->getConnection()->close();
        parent::tearDown();
    }
}
