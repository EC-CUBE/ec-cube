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
use Eccube\Entity\Product;
use Eccube\Entity\Master\SaleType;
use Eccube\Repository\Master\SaleTypeRepository;

class CartServiceTest extends AbstractServiceTestCase
{
    /**
     * @var Product
     */
    protected $Product;

    /**
     * @var Product
     */
    protected $Product2;

    /**
     * @var SaleType
     */
    protected $SaleType1;

    /**
     * @var SaleType
     */
    protected $SaleType2;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var SaleTypeRepository
     */
    protected $saleTypeRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->cartService = $this->container->get(CartService::class);
        $this->saleTypeRepository = $this->container->get(SaleTypeRepository::class);

        $this->SaleType1 = $this->saleTypeRepository->find(1);
        $this->SaleType2 = $this->saleTypeRepository->find(2);
        $this->Product = $this->createProduct();

        // SaleType 2 の商品を作成
        $this->Product2 = $this->createProduct();
        foreach ($this->Product2->getProductClasses() as $ProductClass) {
            $ProductClass->setSaleType($this->SaleType2);
        }
        $this->entityManager->flush();
    }

    public function testUnlock()
    {
        $this->cartService->unlock();

        $this->assertFalse($this->cartService->isLocked());
    }

    public function testLock()
    {
        $this->cartService->lock();

        $this->assertTrue($this->cartService->isLocked());
    }

    public function testClear_PreOrderId()
    {
        $this->cartService->clear();

        $this->assertNull($this->cartService->getPreOrderId());
    }

    public function testClear_Lock()
    {
        $this->cartService->clear();

        $this->assertFalse($this->cartService->isLocked());
        $this->assertCount(0, $this->cartService->getCart()->getCartItems());
    }

    public function testClear_Products()
    {
        $this->cartService->addProduct(1);
        $this->cartService->clear();

        $this->assertCount(0, $this->cartService->getCart()->getCartItems());
    }

    public function testAddProducts_ProductClassEntity()
    {
        $this->cartService->addProduct(1);

        /* @var \Eccube\Entity\CartItem[] $CartItems */
        $CartItems = $this->cartService->getCart()->getCartItems();

        $this->assertEquals(1, $CartItems[0]->getProductClassId());
    }

    public function testAddProducts_Quantity()
    {
        $this->assertCount(0, $this->cartService->getCart()->getCartItems());

        $this->cartService->addProduct(1);
        $quantity = $this->cartService->getCart()->getItems()->reduce(function ($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        $this->assertEquals(1, $quantity);

        $this->cartService->clear();

        $this->cartService->addProduct(10, 6);
        $quantity = $this->cartService->getCart()->getItems()->reduce(function ($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        // 明細の丸め処理はpurchaseFlowで実行されるため、販売制限数を超えてもカートには入る
        $this->assertEquals(6, $quantity);

        $this->cartService->clear();

        $this->cartService->addProduct(10, 101);
        $this->cartService->addProduct(10, 6);
        $quantity = $this->cartService->getCart()->getItems()->reduce(function ($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        // 明細の丸め処理はpurchaseFlowで実行されるため、販売制限数を超えてもカートには入る
        $this->assertEquals(107, $quantity);
    }

    public function testAddProducts_WithCartItemComparator()
    {
        // 同じ商品規格で同じ数量なら同じ明細とみなすようにする
        $this->cartService->setCartItemComparator(new CartServiceTest_CartItemComparator());

        {
            $this->cartService->addProduct(1, 1);
            $this->cartService->addProduct(1, 1);

            /* @var \Eccube\Entity\CartItem[] $CartItems */
            $CartItems = $this->cartService->getCart()->getCartItems();
            self::assertEquals(1, count($CartItems));
            self::assertEquals(1, $CartItems[0]->getProductClassId());
            self::assertEquals(2, $CartItems[0]->getQuantity());
        }

        {
            $this->cartService->addProduct(1, 1);

            /* @var \Eccube\Entity\CartItem[] $CartItems */
            $CartItems = $this->cartService->getCart()->getCartItems();
            self::assertEquals(2, count($CartItems));
            self::assertEquals(1, $CartItems[0]->getProductClassId());
            self::assertEquals(2, $CartItems[0]->getQuantity());
            self::assertEquals(1, $CartItems[1]->getProductClassId());
            self::assertEquals(1, $CartItems[1]->getQuantity());
        }
    }

    public function testUpProductQuantity()
    {
        $this->cartService->clear();
        $this->cartService->addProduct(10, 1);
        $this->cartService->addProduct(10, 1);

        $quantity = $this->cartService->getCart()->getItems()->reduce(function($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        $this->assertEquals(2, $quantity);
    }

    public function testDownProductQuantity()
    {
        $this->cartService->clear();
        $this->cartService->addProduct(10, 2);
        $this->cartService->addProduct(10, -1);

        $quantity = $this->cartService->getCart()->getItems()->reduce(function($q, $item) {
            $q += $item->getQuantity();
            return $q;
        });
        $this->assertEquals(1, $quantity);
    }

    public function testRemoveProduct()
    {
        $this->cartService->addProduct(1, 2);
        $this->cartService->removeProduct(1);

        $this->assertCount(0, $this->cartService->getCart()->getCartItems());
    }

    public function testSave()
    {
        $preOrderId = sha1(StringUtil::random(32));

        $this->cartService->setPreOrderId($preOrderId);
        $this->cartService->save();

        $this->expected = $preOrderId;
        $this->actual = $this->cartService->getCart()->getPreOrderId();
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
