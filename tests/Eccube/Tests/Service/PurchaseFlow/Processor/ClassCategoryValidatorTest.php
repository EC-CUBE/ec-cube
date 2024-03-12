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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\CartItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\ClassCategoryValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class ClassCategoryValidatorTest extends EccubeTestCase
{
    /**
     * @var ClassCategoryValidator
     */
    protected $validator;

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
    protected $ProductClass;

    public function setUp(): void
    {
        parent::setUp();

        $this->Product = $this->entityManager->find(Product::class, 1);
        $this->ProductClass = $this->Product->getProductClasses()->next();
        $this->validator = static::getContainer()->get(ClassCategoryValidator::class);
        $this->cartItem = new CartItem();
        $this->cartItem->setQuantity(10);
        $this->cartItem->setProductClass($this->ProductClass);
    }

    public function testInstance()
    {
        self::assertInstanceOf(ClassCategoryValidator::class, $this->validator);
    }

    /**
     * 規格1が無効の場合は明細の個数を0に設定する
     */
    public function testClassCategory1VisibleFalse()
    {
        $this->ProductClass->getClassCategory1()->setVisible(false);

        $this->validator->execute($this->cartItem, new PurchaseContext());

        self::assertEquals(0, $this->cartItem->getQuantity());
    }

    /**
     * 規格2が無効の場合は明細の個数を0に設定する
     */
    public function testClassCategory2VisibleFalse()
    {
        $this->ProductClass->getClassCategory2()->setVisible(false);

        $this->validator->execute($this->cartItem, new PurchaseContext());

        self::assertEquals(0, $this->cartItem->getQuantity());
    }
}
