<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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

use Eccube\Application;
use Eccube\Service\CartService;

class CartServiceTest extends AbstractServiceTestCase
{
    public function testUnlock()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->unlock();

        $this->assertFalse($cartService->isLocked());
    }

    public function testLock()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->lock();

        $this->assertTrue($cartService->isLocked());
    }

    public function testClear_PreOrderId()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();

        $this->assertNull($cartService->getPreOrderId());
    }

    public function testClear_Lock()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();

        $this->assertFalse($cartService->isLocked());
        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testClear_Products()
    {
        self::markTestSkipped();

        $cartService = $this->app['eccube.service.cart'];
        $cartService->addProduct(1);
        $cartService->clear();

        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testAddProducts_ProductClassEntity()
    {
        self::markTestSkipped();

        $cartService = $this->app['eccube.service.cart'];
        $cartService->addProduct(1);

        $CartItems = $cartService->getCart()->getCartItems();

        $this->assertEquals('Eccube\Entity\ProductClass', $CartItems[0]->getClassName());
        $this->assertEquals(1, $CartItems[0]->getClassId());
    }

    public function testAddProducts_Quantity()
    {
        self::markTestSkipped();

        $cartService = $this->app['eccube.service.cart'];
        $this->assertCount(0, $cartService->getCart()->getCartItems());

        $cartService->addProduct(1);
        $this->assertEquals(1, $cartService->getProductQuantity(1));
    }

    public function testUpProductQuantity()
    {
        self::markTestSkipped();

        $cartService = $this->app['eccube.service.cart'];
        $cartService->setProductQuantity(1, 1);
        $cartService->upProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);

        $this->assertEquals(2, $quantity);
    }

    public function testDownProductQuantity()
    {
        self::markTestSkipped();

        $cartService = $this->app['eccube.service.cart'];

        $cartService->setProductQuantity(1, 2);
        $cartService->downProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);

        $this->assertEquals(1, $quantity);
    }

    public function testDownProductQuantity_Remove()
    {
        self::markTestSkipped();

        $cartService = $this->app['eccube.service.cart'];

        $cartService->setProductQuantity(1, 1);
        $cartService->downProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);
        $this->assertEquals(0, $quantity);
        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testRemoveProduct()
    {
        self::markTestSkipped();

        $cartService = $this->app['eccube.service.cart'];

        $cartService->setProductQuantity(1, 2);
        $cartService->removeProduct(1);

        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testGetErrors()
    {
        $cartService = $this->app['eccube.service.cart'];

        $this->assertCount(0, $cartService->getErrors());

        $cartService->addError('foo');
        $cartService->addError('bar');

        $this->assertCount(2, $cartService->getErrors());
    }

    public function testGetMessages()
    {
        $cartService = $this->app['eccube.service.cart'];
        $this->assertCount(0, $cartService->getMessages());

        $cartService->setMessage('foo');
        $cartService->setMessage('bar');

        $this->assertCount(2, $cartService->getMessages());
    }

}
