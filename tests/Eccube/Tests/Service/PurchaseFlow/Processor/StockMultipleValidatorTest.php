<?php

declare(strict_types=1);

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

use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\Processor\StockMultipleValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

final class StockMultipleValidatorTest extends EccubeTestCase
{
    protected ?StockMultipleValidator $validator = null;

    protected ?Order $Order = null;

    protected ?OrderItem $OrderItem1 = null;

    protected ?OrderItem $OrderItem2 = null;

    protected ?Product $Product = null;

    protected ?ProductClass $ProductClass = null;

    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = static::getContainer()->get(StockMultipleValidator::class);
        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()->first();
        $this->Order = $this->createOrderWithProductClasses($this->createCustomer(),
            [$this->ProductClass, $this->ProductClass]);
        $this->OrderItem1 = $this->Order->getProductOrderItems()[0];
        $this->OrderItem2 = $this->Order->getProductOrderItems()[1];
    }

    public function testInstance()
    {
        $this->assertInstanceOf(StockMultipleValidator::class, $this->validator);
        $this->assertSame($this->ProductClass, $this->OrderItem1->getProductClass());
        $this->assertSame($this->ProductClass, $this->OrderItem2->getProductClass());
    }

    public function testValidStock()
    {
        $this->ProductClass->setStockUnlimited(false);
        $this->ProductClass->setStock('2');
        $this->OrderItem1->setQuantity(1);
        $this->OrderItem2->setQuantity(1);
        try {
            $this->validator->validate($this->Order, new PurchaseContext());
            $this->assertTrue(true);
        } catch (InvalidItemException) {
            self::fail();
        }
    }

    public function testStockUnlimited()
    {
        $this->ProductClass->setStockUnlimited(true);
        $this->ProductClass->setStock(null);
        $this->OrderItem1->setQuantity(1000);
        $this->OrderItem2->setQuantity(50);

        try {
            $this->validator->validate($this->Order, new PurchaseContext());
            $this->assertTrue(true);
        } catch (InvalidItemException) {
            self::fail();
        }
    }

    public function testStockZero()
    {
        $this->ProductClass->setStockUnlimited(false);
        $this->ProductClass->setStock('0');
        $this->OrderItem1->setQuantity(1000);
        $this->OrderItem2->setQuantity(50);

        $this->expectException(InvalidItemException::class);
        $this->validator->validate($this->Order, new PurchaseContext());
    }

    public function testStockOver()
    {
        $this->ProductClass->setStockUnlimited(false);
        $this->ProductClass->setStock('100');
        $this->OrderItem1->setQuantity(50);
        $this->OrderItem2->setQuantity(51);

        $this->expectException(InvalidItemException::class);
        $this->validator->validate($this->Order, new PurchaseContext());
    }
}
