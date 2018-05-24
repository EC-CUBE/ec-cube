<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
        $OrderItem->setQuantity(3);

        $actual = $this->helper->convertToCart($Order);

        $CartItems = $actual->getCartItems();
        self::assertCount(1, $CartItems);
    }
}
