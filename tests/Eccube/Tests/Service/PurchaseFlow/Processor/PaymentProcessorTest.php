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

namespace Eccube\Tests\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Delivery;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\PaymentValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PaymentProcessorTest extends EccubeTestCase
{
    /**
     * @var PaymentValidator
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

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $SaleType = new SaleType();
        $SaleType->setId(1000);
        $SaleType->setName('テスト種別');
        $SaleType->setSortNo(1000);
        $this->entityManager->persist($SaleType);
        $this->entityManager->flush($SaleType);

        $Delivery = new Delivery();
        $Delivery->setName('テスト配送');
        $Delivery->setSaleType($SaleType);
        $Delivery->setVisible(true);
        $this->entityManager->persist($Delivery);
        $this->entityManager->flush($Delivery);

        $Payment = new Payment();
        $Payment->setMethod('テスト支払');
        $Payment->setVisible(true);
        $this->entityManager->persist($Payment);
        $this->entityManager->flush($Payment);

        $PaymentOption = new PaymentOption();
        $PaymentOption->setDeliveryId($Delivery->getId());
        $PaymentOption->setDelivery($Delivery);
        $PaymentOption->setPaymentId($Payment->getId());
        $PaymentOption->setPayment($Payment);
        $Delivery->addPaymentOption($PaymentOption);
        $Payment->addPaymentOption($PaymentOption);
        $this->entityManager->persist($PaymentOption);
        $this->entityManager->flush();

        $this->Product = $this->createProduct('テスト商品', 3);
        $this->ProductClass1 = $this->Product->getProductClasses()[0];
        $this->ProductClass2 = $this->Product->getProductClasses()[1];
        $this->ProductClass3 = $this->Product->getProductClasses()[2];
        $this->ProductClass3->setSaleType($SaleType);

        $this->validator = new PaymentValidator($this->entityManager->getRepository(\Eccube\Entity\Delivery::class));
    }

    public function testInstance()
    {
        self::assertInstanceOf(PaymentValidator::class, $this->validator);
    }

    public function testCartNoItems()
    {
        $cart = new Cart();
        $result = $this->validator->execute($cart, new PurchaseContext());

        self::assertFalse($result->isError());
    }

    public function testCartOneItem()
    {
        $cart = new Cart();
        $item = new CartItem();
        $item->setProductClass($this->ProductClass1);
        $cart->addItem($item);

        $result = $this->validator->execute($cart, new PurchaseContext());

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

        $result = $this->validator->execute($cart, new PurchaseContext());

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

        $result = $this->validator->execute($cart, new PurchaseContext());

        self::assertTrue($result->isError());
        self::assertCount(3, $cart->getItems());
    }
}
