<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Delivery;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\DeliveryRepository;
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

        $SaleType = new SaleType();
        $SaleType->setId(1000);
        $SaleType->setName('テスト種別');
        $SaleType->setSortNo(1000);
        $this->app['orm.em']->persist($SaleType);
        $this->app['orm.em']->flush($SaleType);

        $Delivery = new Delivery();
        $Delivery->setName('テスト配送');
        $Delivery->setSaleType($SaleType);
        $Delivery->setVisible(true);
        $this->app['orm.em']->persist($Delivery);
        $this->app['orm.em']->flush($Delivery);

        $Payment = new Payment();
        $Payment->setMethod('テスト支払');
        $Payment->setVisible(true);
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
        $this->ProductClass3->setSaleType($SaleType);

        $this->validator = new PaymentProcessor($this->app[DeliveryRepository::class]);
    }

    public function testInstance()
    {
        self::assertInstanceOf(PaymentProcessor::class, $this->validator);
    }

    public function testCartNoItems()
    {
        $cart = new Cart();
        $result = $this->validator->process($cart, new PurchaseContext());

        self::assertFalse($result->isError());
    }

    public function testCartOneItem()
    {
        $cart = new Cart();
        $item = new CartItem();
        $item->setProductClass($this->ProductClass1);
        $cart->addItem($item);

        $result = $this->validator->process($cart, new PurchaseContext());

        self::assertFalse($result->isError());
    }

    public function testCartValidItems()
    {
        $cart = new Cart();
        $item1 = new CartItem();
        $item1->setProductClass($this->ProductClass1);
        $cart->addItem($item1);

        $item2 = new CartItem();
        $item2->setProductClass($this->ProductClass2);
        $cart->addItem($item2);

        $result = $this->validator->process($cart, new PurchaseContext());

        self::assertFalse($result->isError());
    }

    public function testCartInValidItems()
    {
        $cart = new Cart();
        $item1 = new CartItem();
        $item1->setProductClass($this->ProductClass1);
        $cart->addItem($item1);

        $item2 = new CartItem();
        $item2->setProductClass($this->ProductClass2);
        $cart->addItem($item2);

        $item3 = new CartItem();
        $item3->setProductClass($this->ProductClass3);
        $cart->addItem($item3);

        $result = $this->validator->process($cart, new PurchaseContext());

        self::assertTrue($result->isError());
        self::assertCount(3, $cart->getItems());
    }
}
