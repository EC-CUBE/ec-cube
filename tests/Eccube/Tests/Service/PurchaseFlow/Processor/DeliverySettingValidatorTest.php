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
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\DeliverySettingValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DeliverySettingValidatorTest extends EccubeTestCase
{
    /**
     * @var DeliverySettingValidator
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

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
        $this->validator = self::$container->get(DeliverySettingValidator::class);
        $this->cartItem = new CartItem();
        $this->cartItem->setProductClass($this->ProductClass);
    }

    public function testInstance()
    {
        self::assertInstanceOf(DeliverySettingValidator::class, $this->validator);
    }

    /**
     * 配送業者が適切に設定されていれば何もしない.
     */
    public function testDeliverySettingIsValid()
    {
        $result = $this->validator->execute($this->cartItem, new PurchaseContext());

        self::assertFalse($result->isError());
    }

    /**
     * 配送業者が設定できていない商品の場合は明細の個数を0に設定する.
     */
    public function testDisplayStatusWithClosed()
    {
        $SaleType = new SaleType();
        $SaleType->setId(10000);
        $this->ProductClass->setSaleType($SaleType);

        $this->validator->execute($this->cartItem, new PurchaseContext());

        self::assertEquals(0, $this->cartItem->getQuantity());
    }
}
