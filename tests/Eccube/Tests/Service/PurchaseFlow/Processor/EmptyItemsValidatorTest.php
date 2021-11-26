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
use Eccube\Service\PurchaseFlow\Processor\EmptyItemsValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class EmptyItemsValidatorTest extends EccubeTestCase
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
    protected $OrderItem;

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

        $this->validator = self::$container->get(EmptyItemsValidator::class);
        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
        $ItemType = new OrderItemType();
        $ItemType->setId(OrderItemType::PRODUCT);
        $this->OrderItem = new OrderItem();
        $this->OrderItem->setQuantity(1);
        $this->OrderItem->setProductClass($this->ProductClass);
        $this->OrderItem->setOrderItemType($ItemType);
        $this->Order = new Order();
        $this->Order->addOrderItem($this->OrderItem);
    }

    public function testInstance()
    {
        self::assertInstanceOf(EmptyItemsValidator::class, $this->validator);
    }

    public function testNotEmptyItem()
    {
        $result = $this->validator->execute($this->Order, new PurchaseContext());

        self::assertCount(1, $this->Order->getOrderItems());
    }

    public function testEmptyItem()
    {
        $this->OrderItem->setQuantity(0);

        $result = $this->validator->execute($this->Order, new PurchaseContext());

        self::assertCount(0, $this->Order->getOrderItems());
    }

    public function testMinusItem()
    {
        $this->OrderItem->setQuantity(-1);

        $result = $this->validator->execute($this->Order, new PurchaseContext());

        self::assertCount(0, $this->Order->getOrderItems());
    }
}
