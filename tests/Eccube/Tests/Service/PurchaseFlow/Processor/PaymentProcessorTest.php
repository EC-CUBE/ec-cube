<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Delivery;
use Eccube\Entity\Master\ProductType;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\PaymentProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PaymentProcessorTest extends EccubeTestCase
{
    /**
     * @var PaymentProcessor
     */
    protected $validator;

    /**
     * @var Cart
     */
    protected $Cart;

    /**
     * @var CartItem
     */
    protected $cartItem;

    /**
     * @var Product
     */
    protected $Product;

    /**
     * @var ProductClass
     */
    protected $ProductClass1;

    /**
     * @var ProductClass
     */
    protected $ProductClass2;

    /**
     * @var ProductClass
     */
    protected $ProductClass3;

    public function setUp()
    {
        parent::setUp();

        $ProductType = new ProductType();
        $ProductType->setId(1000);
        $ProductType->setName('テスト種別');
        $ProductType->setRank(1000);
        $this->app['orm.em']->persist($ProductType);
        $this->app['orm.em']->flush($ProductType);

        $Delivery = new Delivery();
        $Delivery->setName('テスト配送');
        $Delivery->setProductType($ProductType);
        $Delivery->setDelFlg(0);
        $this->app['orm.em']->persist($Delivery);
        $this->app['orm.em']->flush($Delivery);

        $Payment = new Payment();
        $Payment->setMethod('テスト支払');
        $Payment->setDelFlg(0);
        $this->app['orm.em']->persist($Payment);
        $this->app['orm.em']->flush($Payment);

        $PaymentOption = new PaymentOption();
        $PaymentOption->setDeliveryId($Delivery->getId());
        $PaymentOption->setDelivery($Delivery);
        $PaymentOption->setPaymentId($Payment->getId());
        $PaymentOption->setPayment($Payment);
        $Delivery->addPaymentOption($PaymentOption);
        $Payment->addPaymentOption($PaymentOption);
        $this->app['orm.em']->persist($PaymentOption);
        $this->app['orm.em']->flush();

        $this->Product = $this->createProduct('テスト商品', 3);
        $this->ProductClass1 = $this->Product->getProductClasses()[0];
        $this->ProductClass2 = $this->Product->getProductClasses()[1];
        $this->ProductClass3 = $this->Product->getProductClasses()[2];
        $this->ProductClass3->setProductType($ProductType);

        $this->validator = new PaymentProcessor($this->app);
    }

    public function testInstance()
    {
        self::assertInstanceOf(PaymentProcessor::class, $this->validator);
    }

    public function testCartNoItems()
    {
        $cart = new Cart();
        $result = $this->validator->process($cart, PurchaseContext::create());

        self::assertFalse($result->isError());
    }

    public function testCartOneItem()
    {
        $cart = new Cart();
        $item = new CartItem();
        $item->setObject($this->ProductClass1);
        $cart->addItem($item);

        $result = $this->validator->process($cart, PurchaseContext::create());

        self::assertFalse($result->isError());
    }

    public function testCartValidItems()
    {
        $cart = new Cart();
        $item1 = new CartItem();
        $item1->setObject($this->ProductClass1);
        $cart->addItem($item1);

        $item2 = new CartItem();
        $item2->setObject($this->ProductClass2);
        $cart->addItem($item2);

        $result = $this->validator->process($cart, PurchaseContext::create());

        self::assertFalse($result->isError());
    }

    public function testCartInValidItems()
    {
        $cart = new Cart();
        $item1 = new CartItem();
        $item1->setClassName(ProductClass::class);
        $item1->setClassId($this->ProductClass1->getId());
        $item1->setObject($this->ProductClass1);
        $cart->addItem($item1);

        $item2 = new CartItem();
        $item1->setClassName(ProductClass::class);
        $item1->setClassId($this->ProductClass2->getId());
        $item2->setObject($this->ProductClass2);
        $cart->addItem($item2);

        $item3 = new CartItem();
        $item1->setClassName(ProductClass::class);
        $item1->setClassId($this->ProductClass3->getId());
        $item3->setObject($this->ProductClass3);
        $cart->addItem($item3);

        $result = $this->validator->process($cart, PurchaseContext::create());

        self::assertTrue($result->isError());
        self::assertCount(3, $cart->getItems());
    }
}
