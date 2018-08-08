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

use Eccube\Entity\Cart;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\OrderHelper;
use Eccube\Tests\EccubeTestCase;

class OrderHelperTest extends EccubeTestCase
{
    /** @var OrderHelper */
    private $helper;

    public function setUp()
    {
        parent::setUp();
        $this->helper = $this->container->get(OrderHelper::class);
    }

    public function testConvertToCart_new_cart()
    {
        $Order = new Order();
        self::assertInstanceOf(Cart::class, $this->helper->convertToCart($Order));
    }

    public function testConvertToCart()
    {
        $Product = $this->createProduct('test', 1);
        $ProductClasses = $Product->getProductClasses()->toArray();
        $Customer = $this->createCustomer();
        $Order = $this->createOrderWithProductClasses($Customer, $ProductClasses);

        /** @var OrderItem $OrderItem */
        $OrderItem = $Order->getOrderItems()->get(0);

        $Cart = $this->helper->convertToCart($Order);

        $CartItems = $Cart->getCartItems();
        self::assertCount(1, $CartItems);

        $CartItem = $CartItems[0];
        self::assertEquals($OrderItem->getProductClass(), $CartItem->getProductClass());
        self::assertEquals($OrderItem->getPriceIncTax(), $CartItem->getPrice());
        self::assertEquals($OrderItem->getQuantity(), $CartItem->getQuantity());

        self::assertEquals($Order->getPreOrderId(), $Cart->getPreOrderId());
    }
}
