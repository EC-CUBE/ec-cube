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
use Eccube\Common\Constant;
use Eccube\Exception\CartException;
use Eccube\Util\Str;

class CartServiceTest extends AbstractServiceTestCase
{

    protected $Product;

    public function setUp()
    {
        parent::setUp();
        $this->ProductType1 = $this->app['eccube.repository.master.product_type']->find(1);
        $this->ProductType2 = $this->app['eccube.repository.master.product_type']->find(2);
        $this->Product = $this->createProduct();

        // ProductType 2 の商品を作成
        $this->Product2 = $this->createProduct();
        foreach ($this->Product2->getProductClasses() as $ProductClass) {
            $ProductClass->setProductType($this->ProductType2);
        }
        $this->app['orm.em']->flush();
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
        $quantity = $cartService->getCart()->getItems()->reduce(function($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        $this->assertEquals(1, $quantity);

        $cartService->clear();

        $cartService->addProduct(10, 6);
        $quantity = $cartService->getCart()->getItems()->reduce(function($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        // 明細の丸め処理はpurchaseFlowで実行されるため、販売制限数を超えてもカートには入る
        $this->assertEquals(6, $quantity);

        $cartService->clear();

        $cartService->addProduct(10, 101);
        $cartService->addProduct(10, 6);
        $quantity = $cartService->getCart()->getItems()->reduce(function($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        // 明細の丸め処理はpurchaseFlowで実行されるため、販売制限数を超えてもカートには入る
        $this->assertEquals(107, $quantity);
    }

    public function testUpProductQuantity()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();
        $cartService->addProduct(10, 1);
        $cartService->addProduct(10, 1);

        $quantity = $cartService->getCart()->getItems()->reduce(function($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        $this->assertEquals(2, $quantity);
    }

    public function testDownProductQuantity()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();
        $cartService->addProduct(10, 2);
        $cartService->addProduct(10, -1);

        $quantity = $cartService->getCart()->getItems()->reduce(function($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        $this->assertEquals(1, $quantity);
    }

    public function testRemoveProduct()
    {
        $cartService = $this->app['eccube.service.cart'];

        $cartService->addProduct(1, 2);
        $cartService->removeProduct(1);

        $this->assertCount(0, $cartService->getCart()->getCartItems());
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
}
