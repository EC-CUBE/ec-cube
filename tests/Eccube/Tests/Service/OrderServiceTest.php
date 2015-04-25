<?php

namespace Eccube\Tests\Service;

use Doctrine\Common\Util\Debug;
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

    public function testNewOrder()
    {
        $Order = $this->app['eccube.service.order']->newOrder();
        $this->assertNotEmpty($Order);
    }

    public function testNewOrderDetail()
    {
        $ProductClass = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find(1);
        $Product = $ProductClass->getProduct();
        $OrderDetail = $this->app['eccube.service.order']->newOrderDetail($Product, $ProductClass, 3);
        $this->assertNotEmpty($OrderDetail);
    }

    public function testCopyToOrderFromCustomer()
    {
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
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();
        $cartService->addProduct($ProductClass->getId());
        $cartService->addProduct($ProductClass->getId());
        $cartService->lock();

        $CarItems = $cartService->getCart()->getCartItems();

        // 受注データ登録
        $Order = $this->app['eccube.service.order']->convertToOrderFromCartItems($CarItems, $Customer);
        $Order = $this->app['eccube.service.order']->registerPreOrderFromCart($Order);

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
            ->setPoint(0)
            ->setStatus($CustomerStatus)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime())
            ->setDelFlg(1);
        return $Customer;
    }
}