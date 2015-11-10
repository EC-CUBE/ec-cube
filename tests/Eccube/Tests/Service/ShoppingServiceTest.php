<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Shipping;
use Eccube\Service\ShoppingService;
use Eccube\Util\Str;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ShoppingServiceTest extends AbstractServiceTestCase
{

    protected $Customer;
    protected $CartService;
    protected $ProductType1;
    protected $ProductType2;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->app['security']->setToken(
            new UsernamePasswordToken(
                $this->Customer, null, 'Customer', $this->Customer->getRoles()
            )
        );
        $this->CartService = $this->app['eccube.service.cart'];
        $this->CartService->setProductQuantity(1, 1)
            ->save();

        $this->ProductType1 = $this->app['eccube.repository.master.product_type']->find(1);
        $this->ProductType2 = $this->app['eccube.repository.master.product_type']->find(2);
    }

    public function testCreateOrder()
    {
        $Order = $this->app['eccube.service.shopping']->createOrder($this->Customer);

        $this->expected = $this->Customer->getName01();
        $this->actual = $Order->getName01();
        $this->verify();
    }

    public function testGetOrder()
    {
        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $Order = $this->app['eccube.service.shopping']->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithMultiple()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $Order = $this->app['eccube.service.shopping']->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithNonMember()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        $NonMember = $this->createNonMember();
        $this->app['security']->setToken(
            new UsernamePasswordToken(
                $NonMember, null, 'Customer', array('IS_AUTHENTICATED_ANONYMOUSLY')
            )
        );

        $NewOrder = $this->app['eccube.service.shopping']->createOrder($NonMember);
        $Order = $this->app['eccube.service.shopping']->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithStatusAndNull()
    {
        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $this->app['orm.em']->flush();

        $OrderNew = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $Order = $this->app['eccube.service.shopping']->getOrder($OrderNew);
        $this->assertNull($Order);
    }

    public function testGetOrderWithStatus()
    {
        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $OrderProcessing = $this->app['eccube.repository.order_status']->find($this->app['config']['order_processing']);
        $Order = $this->app['eccube.service.shopping']->getOrder($OrderProcessing);

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();

    }

    public function testGetNonMemberIsNull()
    {
        $Customer = $this->app['eccube.service.shopping']->getNonMember('eccube.front.shopping.nonmember');

        $this->assertNull($Customer);
    }

    public function testGetNonMember()
    {
        $email = 'test@example.net';
        $NonMember = $this->createNonMember($email);
        $Customer = $this->app['eccube.service.shopping']->getNonMember('eccube.front.shopping.nonmember');

        $this->expected = $email;
        $this->actual = $Customer->getEmail();
        $this->verify('セッションのメールアドレスが一致するか');

        $this->expected = 1;
        $this->actual = $Customer->getPref()->getId();
        $this->verify('都道府県IDが一致するか');
    }

    public function testGetDeliveries()
    {
        $Deliveries = $this->app['eccube.service.shopping']->getDeliveries($this->ProductType1);

        $this->expected = 1;
        $this->actual = count($Deliveries);
        $this->verify();

        $this->expected = 1;
        $this->actual = $Deliveries[0]->getId();
        $this->verify();
    }

    public function testGetDeliveriesMultiple()
    {
        $Deliveries = $this->app['eccube.service.shopping']->getDeliveries(
            array($this->ProductType1, $this->ProductType2));

        $this->expected = 2;
        $this->actual = count($Deliveries);
        $this->verify();
    }

    public function testCopyToShippingFromCustomerWithNull()
    {
        $Shipping = new Shipping();
        $Shipping->copyProperties($this->Customer);

        $this->expected = $Shipping;
        $this->actual = $this->app['eccube.service.shopping']->copyToShippingFromCustomer($Shipping, null);
        $this->verify();
    }

    public function testGetAmount()
    {
        $NewOrder = $this->createOrder($this->Customer);
        $Order = $this->app['eccube.service.shopping']->getAmount($NewOrder);

        $this->expected = $NewOrder->getTotal();
        $this->actual = $Order->getTotal();
        $this->verify();
    }

    public function testSetDeliveryFreeAmount()
    {
        // 送料無料条件を 0 円に設定
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setDeliveryFreeAmount(0);

        $Order = $this->createOrder($this->Customer);
        $Order->setDeliveryFeeTotal(100); // 送料 100 円に設定しておく
        $this->assertNotEquals(0, $Order->getDeliveryFeeTotal());

        // 送料 0 円に設定される
        $this->app['eccube.service.shopping']->setDeliveryFreeAmount($Order);

        $this->expected = 0;
        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();
    }

    public function testSetDeliveryFreeQuantity()
    {
        // 送料無料条件を 0 個に設定
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setDeliveryFreeQuantity(0);

        $Order = $this->createOrder($this->Customer);
        $Order->setDeliveryFeeTotal(100); // 送料 100 円に設定しておく
        $this->assertNotEquals(0, $Order->getDeliveryFeeTotal());

        // 送料 0 円に設定される
        $this->app['eccube.service.shopping']->setDeliveryFreeQuantity($Order);

        $this->expected = 0;
        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();
    }

    /**
     * @link https://github.com/EC-CUBE/ec-cube/issues/1162
     * #1162 の修正後, コメントアウトをはずす.
     *
    public function testGetDeliveriesMultipleShipping()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        // ProductType 1 と 2 で, 共通する支払い方法を削除しておく
        $PaymentOption = $this
            ->app['orm.em']
            ->getRepository('\Eccube\Entity\PaymentOption')
            ->findOneBy(
                array(
                    'delivery_id' => 1,
                    'payment_id' => 3
                )
            );
        $this->assertNotNull($PaymentOption);
        $this->app['orm.em']->remove($PaymentOption);
        $this->app['orm.em']->flush();

        $Deliveries = $this->app['eccube.service.shopping']->getDeliveries(
            array($this->ProductType1, $this->ProductType2));

        $this->expected = 0;
        $this->actual = count($Deliveries);
        $this->verify();
    }
    */
}
