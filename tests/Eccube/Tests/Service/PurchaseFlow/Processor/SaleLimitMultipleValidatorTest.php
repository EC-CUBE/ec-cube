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

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\Processor\SaleLimitMultipleValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class SaleLimitMultipleValidatorTest extends EccubeTestCase
{
    /**
     * @var SaleLimitMultipleValidator
     */
    protected $validator;

    /**
     * @var Order
     */
    protected $Order;

    /**
     * @var OrderItem
     */
    protected $OrderItem1;

    /**
     * @var OrderItem
     */
    protected $OrderItem2;

    /**
     * @var Product
     */
    protected $Product;

    /**
     * @var ProductClass
     */
    protected $ProductClass;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->validator = self::$container->get(SaleLimitMultipleValidator::class);
        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
        $ItemType = new OrderItemType();
        $ItemType->setId(OrderItemType::PRODUCT);
        $this->OrderItem1 = new OrderItem();
        $this->OrderItem1->setQuantity(1);
        $this->OrderItem1->setProductClass($this->ProductClass);
        $this->OrderItem1->setOrderItemType($ItemType);
        $this->OrderItem2 = new OrderItem();
        $this->OrderItem2->setQuantity(1);
        $this->OrderItem2->setProductClass($this->ProductClass);
        $this->OrderItem2->setOrderItemType($ItemType);
        $this->Order = new Order();
        $this->Order->addOrderItem($this->OrderItem1);
        $this->Order->addOrderItem($this->OrderItem2);
    }

    public function testInstance()
    {
        self::assertInstanceOf(SaleLimitMultipleValidator::class, $this->validator);
        self::assertSame($this->ProductClass, $this->OrderItem1->getProductClass());
        self::assertSame($this->ProductClass, $this->OrderItem2->getProductClass());
    }

    public function testNonLimit()
    {
        $this->ProductClass->setSaleLimit(null);
        $this->OrderItem1->setQuantity(1000);
        $this->OrderItem2->setQuantity(500);

        try {
            $this->validator->validate($this->Order, new PurchaseContext());
            self::assertTrue(true);
        } catch (InvalidItemException $e) {
            self::fail();
        }
    }

    public function testValidLimit()
    {
        $this->ProductClass->setSaleLimit(10);
        $this->OrderItem1->setQuantity(4);
        $this->OrderItem2->setQuantity(6);

        try {
            $this->validator->validate($this->Order, new PurchaseContext());
            self::assertTrue(true);
        } catch (InvalidItemException $e) {
            self::fail();
        }
    }

    public function testOverLimit()
    {
        $this->ProductClass->setSaleLimit(10);
        $this->OrderItem1->setQuantity(5);
        $this->OrderItem2->setQuantity(6);

        $this->expectException(InvalidItemException::class);
        $this->validator->validate($this->Order, new PurchaseContext());
    }
}
