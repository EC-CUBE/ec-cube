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

use Eccube\Entity\CartItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\PriceChangeValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PriceChangeValidatorTest extends EccubeTestCase
{
    protected ?PriceChangeValidator $validator = null;

    protected ?CartItem $cartItem = null;

    protected ?Product $Product = null;

    protected ?ProductClass $ProductClass = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
        $this->validator = static::getContainer()->get(PriceChangeValidator::class);
        $this->cartItem = new CartItem();
        $this->cartItem->setProductClass($this->ProductClass);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(PriceChangeValidator::class, $this->validator);
        $this->assertSame($this->ProductClass, $this->cartItem->getProductClass());
    }

    public function testNoChange()
    {
        $this->ProductClass->setPrice02(100);
        $this->cartItem->setPrice($this->ProductClass->getPrice02IncTax());
        $result = $this->validator->execute($this->cartItem, new PurchaseContext());
        $this->assertTrue($result->isSuccess());
    }

    public function testChange()
    {
        $this->ProductClass->setPrice02(100);
        $this->cartItem->setPrice(50);
        $result = $this->validator->execute($this->cartItem, new PurchaseContext());
        $this->assertTrue($result->isWarning());
        $this->assertSame($this->ProductClass->getPrice02IncTax(), $this->cartItem->getPrice());
    }
}
