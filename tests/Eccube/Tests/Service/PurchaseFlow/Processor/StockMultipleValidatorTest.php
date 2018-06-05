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

namespace Eccube\Tests\Service;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\Processor\StockMultipleValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;

class StockMultipleValidatorTest extends EccubeTestCase
{
    /**
     * @var StockMultipleValidator
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

        $this->validator = $this->container->get(StockMultipleValidator::class);
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
        self::assertInstanceOf(StockMultipleValidator::class, $this->validator);
        self::assertSame($this->ProductClass, $this->OrderItem1->getProductClass());
        self::assertSame($this->ProductClass, $this->OrderItem2->getProductClass());
    }

    public function testValidStock()
    {
        $this->ProductClass->setStockUnlimited(false);
        $this->ProductClass->setStock(2);
        $this->OrderItem1->setQuantity(1);
        $this->OrderItem2->setQuantity(1);
        $processResult = $this->validator->process($this->Order, new PurchaseContext());
        self::assertTrue($processResult->isSuccess());
    }

    public function testStockUnlimited()
    {
        $this->ProductClass->setStockUnlimited(true);
        $this->ProductClass->setStock(null);
        $this->OrderItem1->setQuantity(1000);
        $this->OrderItem2->setQuantity(50);

        $processResult = $this->validator->process($this->Order, new PurchaseContext());
        self::assertTrue($processResult->isSuccess());
    }

    public function testStockZero()
    {
        $this->ProductClass->setStockUnlimited(false);
        $this->ProductClass->setStock(0);
        $this->OrderItem1->setQuantity(1000);
        $this->OrderItem2->setQuantity(50);

        $processResult = $this->validator->process($this->Order, new PurchaseContext());
        self::assertTrue($processResult->isError());
    }

    public function testStockOver()
    {
        $this->ProductClass->setStockUnlimited(false);
        $this->ProductClass->setStock(100);
        $this->OrderItem1->setQuantity(50);
        $this->OrderItem2->setQuantity(51);

        $processResult = $this->validator->process($this->Order, new PurchaseContext());
        self::assertTrue($processResult->isWarning());
    }
}
