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
use Eccube\Exception\CartException;
use Eccube\Service\CartService;
use Eccube\Util\Str;

class CartServiceTest extends AbstractServiceTestCase
{

    protected $Product;

    public function setUp()
    {
        parent::setUp();
        $this->Product = $this->createProduct();
    }
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
        $cartService = $this->app['eccube.service.cart'];
        $cartService->addProduct(1);
        $cartService->clear();

        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testAddProducts_ProductClassEntity()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->addProduct(1);

        $CartItems = $cartService->getCart()->getCartItems();

        $this->assertEquals('Eccube\Entity\ProductClass', $CartItems[0]->getClassName());
        $this->assertEquals(1, $CartItems[0]->getClassId());
    }

    public function testAddProducts_Quantity()
    {
        $cartService = $this->app['eccube.service.cart'];
        $this->assertCount(0, $cartService->getCart()->getCartItems());

        $cartService->addProduct(1);
        $this->assertEquals(1, $cartService->getProductQuantity(1));

        $cartService->clear();

        $cartService->addProduct(10, 6);
        $this->assertEquals(5, $cartService->getProductQuantity(10));

        $cartService->clear();

        $cartService->addProduct(10, 101);
        $this->assertEquals(5, $cartService->getProductQuantity(10));
    }

    public function testUpProductQuantity()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->setProductQuantity(1, 1);
        $cartService->upProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);

        $this->assertEquals(2, $quantity);
    }

    public function testDownProductQuantity()
    {
        $cartService = $this->app['eccube.service.cart'];

        $cartService->setProductQuantity(1, 2);
        $cartService->downProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);

        $this->assertEquals(1, $quantity);
    }

    public function testDownProductQuantity_Remove()
    {
        $cartService = $this->app['eccube.service.cart'];

        $cartService->setProductQuantity(1, 1);
        $cartService->downProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);
        $this->assertEquals(0, $quantity);
        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testRemoveProduct()
    {
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

    public function testSave()
    {
        $cartService = $this->app['eccube.service.cart'];
        $preOrderId = sha1(Str::random(32));

        $cartService->setPreOrderId($preOrderId);
        $cartService->save();

        $this->expected = $preOrderId;
        $this->actual = $this->app['session']->get('cart')->getPreOrderId();
        $this->verify();
    }

    public function testAddProductType()
    {
        $cartService = $this->app['eccube.service.cart'];
        $ProductType = $this->app['eccube.repository.master.product_type']->find(1);
        $cartService->setCanAddProductType($ProductType);

        $this->expected = $ProductType;
        $this->actual = $cartService->getCanAddProductType();
        $this->verify();
    }

    public function testSetProductQuantityWithId()
    {
        $ProductClasses = $this->Product->getProductClasses();

        $this->app['eccube.service.cart']->setProductQuantity($ProductClasses[0]->getId(), 1)
            ->save();

        $Cart = $this->app['session']->get('cart');
        $CartItems = $Cart->getCartItems();

        $this->expected = 1;
        $this->actual = count($CartItems);
        $this->verify();
    }

    public function testSetProductQuantityWithObject()
    {
        $ProductClasses = $this->Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $this->app['eccube.service.cart']->setProductQuantity($ProductClass, 1)
            ->save();
        $Cart = $this->app['session']->get('cart');
        $CartItems = $Cart->getCartItems();

        $this->expected = 1;
        $this->actual = count($CartItems);
        $this->verify();
    }

    public function testSetProductQuantityWithProductNotFound()
    {
        try {
            $this->app['eccube.service.cart']->setProductQuantity(999999, 1)
                ->save();
            $this->fail();
        } catch (CartException $e) {
            $this->expected = 'cart.product.delete';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    public function testSetProductQuantityWithProductHide()
    {
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
        $this->Product->setStatus($Disp);
        $this->app['orm.em']->flush();

        try {
            $ProductClasses = $this->Product->getProductClasses();
            $ProductClass = $ProductClasses[0];
            $this->app['eccube.service.cart']->setProductQuantity($ProductClass, 1)
                ->save();
            $this->fail();
        } catch (CartException $e) {
            $this->expected = 'cart.product.not.status';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    public function testSetProductQuantityWithOverPrice()
    {
        $ProductClasses = $this->Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $ProductClass->setPrice02($this->app['config']['max_total_fee']);
        $this->app['orm.em']->flush();

        $this->app['eccube.service.cart']->setProductQuantity($ProductClass, 2)->save();

        $this->actual = $this->app['eccube.service.cart']->getError();
        $this->expected = 'cart.over.price_limit';
        $this->verify();
    }

    public function testSetProductQuantityWithOverStock()
    {
        $ProductClasses = $this->Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $ProductClass->setStockUnlimited(0);
        $ProductClass->setStock(10);
        $this->app['orm.em']->flush();

        $this->app['eccube.service.cart']->setProductQuantity($ProductClass, 20)->save();

        $this->actual = $this->app['eccube.service.cart']->getErrors();
        $this->expected = array('cart.over.stock');
        $this->verify();
    }

}
