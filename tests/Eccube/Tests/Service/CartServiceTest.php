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

use Eccube\Entity\CartItem;
use Eccube\Service\Cart\CartItemComparator;
use Eccube\Service\CartService;
use Eccube\Util\StringUtil;

class CartServiceTest extends AbstractServiceTestCase
{

    protected $Product;

    protected $Product2;

    protected $SaleType1;

    protected $SaleType2;

    public function setUp()
    {
        parent::setUp();
        $this->SaleType1 = $this->app['eccube.repository.master.sale_type']->find(1);
        $this->SaleType2 = $this->app['eccube.repository.master.sale_type']->find(2);
        $this->Product = $this->createProduct();

        // SaleType 2 の商品を作成
        $this->Product2 = $this->createProduct();
        foreach ($this->Product2->getProductClasses() as $ProductClass) {
            $ProductClass->setSaleType($this->SaleType2);
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

        /* @var \Eccube\Entity\CartItem[] $CartItems */
        $CartItems = $cartService->getCart()->getCartItems();

        $this->assertEquals(1, $CartItems[0]->getProductClassId());
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

    public function testAddProducts_WithCartItemComparator()
    {
        /** @var CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        // 同じ商品規格で同じ数量なら同じ明細とみなすようにする
        $cartService->setCartItemComparator(new CartServiceTest_CartItemComparator());

        {
            $cartService->addProduct(1, 1);
            $cartService->addProduct(1, 1);

            /* @var \Eccube\Entity\CartItem[] $CartItems */
            $CartItems = $cartService->getCart()->getCartItems();
            self::assertEquals(1, count($CartItems));
            self::assertEquals(1, $CartItems[0]->getProductClassId());
            self::assertEquals(2, $CartItems[0]->getQuantity());
        }

        {
            $cartService->addProduct(1, 1);

            /* @var \Eccube\Entity\CartItem[] $CartItems */
            $CartItems = $cartService->getCart()->getCartItems();
            self::assertEquals(2, count($CartItems));
            self::assertEquals(1, $CartItems[0]->getProductClassId());
            self::assertEquals(1, $CartItems[0]->getQuantity());
            self::assertEquals(1, $CartItems[1]->getProductClassId());
            self::assertEquals(2, $CartItems[1]->getQuantity());
        }
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
        /* @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $cartService->addProduct(1, 2);
        $cartService->removeProduct(1);

        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testSave()
    {
        $cartService = $this->app['eccube.service.cart'];
        $preOrderId = sha1(StringUtil::random(32));

        $cartService->setPreOrderId($preOrderId);
        $cartService->save();

        $this->expected = $preOrderId;
        $this->actual = $cartService->getCart()->getPreOrderId();
        $this->verify();
    }
}

/**
 * 同じ商品同じ数量なら同じ明細とみなす.
 */
class CartServiceTest_CartItemComparator implements CartItemComparator
{
    /**
     * @param CartItem $item1 明細1
     * @param CartItem $item2 明細2
     * @return boolean 同じ明細になる場合はtrue
     */
    public function compare(CartItem $item1, CartItem $item2)
    {
        return $item1->getProductClassId() == $item2->getProductClassId()
            && $item1->getQuantity() == $item2->getQuantity();
    }
}