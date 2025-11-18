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

use Eccube\Entity\CartItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\StockValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Fixture\Generator;

final class StockValidatorTest extends EccubeTestCase
{
    protected ?StockValidator $validator = null;

    protected ?CartItem $cartItem = null;

    protected ?Product $Product = null;

    protected ?ProductClass $ProductClass = null;

    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
        $this->validator = static::getContainer()->get(StockValidator::class);
        $this->cartItem = new CartItem();
        $this->cartItem->setProductClass($this->ProductClass);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(StockValidator::class, $this->validator);
        $this->assertSame($this->ProductClass, $this->cartItem->getProductClass());
    }

    public function testValidStock()
    {
        $this->cartItem->setQuantity(1);
        $this->validator->execute($this->cartItem, new PurchaseContext());
        $this->assertSame('1', $this->cartItem->getQuantity());
    }

    public function testValidStockFail()
    {
        $this->cartItem->setQuantity(PHP_INT_MAX);
        $result = $this->validator->execute($this->cartItem, new PurchaseContext());

        $this->assertEquals($this->ProductClass->getStock(), $this->cartItem->getQuantity());
        $this->assertTrue($result->isWarning());
    }

    public function testValidStockOrder()
    {
        $Customer = $this->createCustomer();
        $Order = static::getContainer()->get(Generator::class)->createOrder($Customer, [$this->ProductClass]);

        $this->assertEquals($Order->getOrderItems()[0]->getProductClass(), $this->ProductClass);

        $Order->getOrderItems()[0]->setQuantity(1);
        $this->ProductClass->setStock('100');

        $this->validator->execute($Order->getOrderItems()[0], new PurchaseContext());
        $this->assertSame('1', $Order->getOrderItems()[0]->getQuantity());
    }
}
