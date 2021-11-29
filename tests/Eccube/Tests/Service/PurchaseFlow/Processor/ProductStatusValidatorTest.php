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
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\ProductStatusValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class ProductStatusValidatorTest extends EccubeTestCase
{
    /**
     * @var ProductStatusValidator
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

    public function setUp()
    {
        parent::setUp();

        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
        $this->validator = self::$container->get(ProductStatusValidator::class);
        $this->cartItem = new CartItem();
        $this->cartItem->setQuantity(10);
        $this->cartItem->setProductClass($this->ProductClass);
    }

    public function testInstance()
    {
        self::assertInstanceOf(ProductStatusValidator::class, $this->validator);
    }

    /**
     * 公開商品の場合はなにもしない.
     */
    public function testDisplayStatusWithShow()
    {
        $ProductStatus = $this->entityManager->find(ProductStatus::class, ProductStatus::DISPLAY_SHOW);
        $this->Product->setStatus($ProductStatus);

        $this->validator->execute($this->cartItem, new PurchaseContext());

        self::assertEquals(10, $this->cartItem->getQuantity());
    }

    /**
     * 非公開商品の場合は明細の個数を0に設定する.
     */
    public function testDisplayStatusWithClosed()
    {
        $ProductStatus = $this->entityManager->find(ProductStatus::class, ProductStatus::DISPLAY_HIDE);
        $this->Product->setStatus($ProductStatus);

        $this->validator->execute($this->cartItem, new PurchaseContext());

        self::assertEquals(0, $this->cartItem->getQuantity());
    }


    /**
     * 無効になっている商品規格の場合は明細の個数を0に設定する.
     */
    public function testProductClassVisibleFalse()
    {
        $this->ProductClass->setVisible(false);

        $this->validator->execute($this->cartItem, new PurchaseContext());

        self::assertEquals(0, $this->cartItem->getQuantity());
    }
}
