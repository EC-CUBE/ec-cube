<?php

namespace Eccube\Tests\Service;

use Eccube\Application;

class OrderServiceTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $this->app = new Application(array(
            'env' => 'test'
        ));
        $this->app->boot();
    }

    public function testCopyToOrderFromCustomer()
    {
        $orderService = $this->app['eccube.service.order'];

        $Order = new \Eccube\Entity\Order();
        $Order = $orderService->copyToOrderFromCustomer($Order, null);
        $this->assertTrue(null === $Order->getCustomer());

        $Customer = new \Eccube\Entity\Customer();
        $Customer->setName01('last name');
        $Customer->setName02('first name');
        $Order = $orderService->copyToOrderFromCustomer($Order, $Customer);

        $this->assertEquals($Customer->getName01(), $Order->getName01());
        $this->assertEquals($Customer->getName02(), $Order->getName02());
    }
    public function testCopyToShippingFromCustomer()
    {
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
        $this->app['orm.em']->getConnection()->beginTransaction();

        // set up customer;
        $Customer = $this->newCustomer();
        $this->app['orm.em']->persist($Customer);
        $this->app['orm.em']->flush();

        // set up cart items
        $ProductClass = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find(1);
        $cartItems = array();
        $cartItems[] = array(
            'Product' => $ProductClass->getProduct(),
            'ProductClass' => $ProductClass,
            'quantity' => 1
        );

        // 受注データ登録
        $Order = $this->app['eccube.service.order']->registerPreOrderFromCart($cartItems, $Customer);

        // 登録内容確認
        $this->assertNotNull($Order);
        $OrderDetails = $Order->getOrderDetails();
        foreach ($OrderDetails as $detail) {
            $this->assertNotNull($detail);
        }

        $Shippings = $Order->getShippings();
        $this->assertNotNull($Shippings);
        foreach ($Shippings as $Shipping) {
            $this->assertNotNull($Shipping);
            $ShipmentItems = $Shipping->getShipmentItems();
            foreach ($ShipmentItems as $item) {
                $this->assertNotNull($item);
            }
        }

        // 購入確定
        $this->app['eccube.service.order']->commit($Order);
        $this->assertEquals(0, $Order->getDelFlg());

        $this->app['orm.em']->getConnection()->rollback();
    }

    public function newCustomer()
    {
        $Customer = new \Eccube\Entity\Customer();
        $Customer->setName01('last name')
            ->setName02('first name')
            ->setEmail('example@lockon.co.jp')
            ->setSecretKey('dummy')
            ->setPoint(0)
            ->setStatus(1)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime())
            ->setDelFlg(1);
        return $Customer;
    }
}