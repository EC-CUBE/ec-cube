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
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\Cart\CartItemComparator;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Util\StringUtil;

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
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->cartService = self::$container->get(CartService::class);
        $this->saleTypeRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\SaleType::class);
        $this->orderRepository = $this->entityManager->getRepository(\Eccube\Entity\Order::class);
        $this->productClassRepository = $this->entityManager->getRepository(\Eccube\Entity\ProductClass::class);
        $this->purchaseFlow = self::$container->get('eccube.purchase.flow.cart');

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

    public function testClear()
    {
        $this->cartService->addProduct(2);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();

        $this->assertCount(1, $this->cartService->getCart()->getCartItems());

        $this->cartService->clear();

        $this->assertNull($this->cartService->getCart());
    }

    public function testAddProductsProductClassEntity()
    {
        $this->cartService->addProduct(1);

        /* @var \Eccube\Entity\CartItem[] $CartItems */
        $CartItems = $this->cartService->getCart()->getCartItems();

        $this->assertEquals(1, $CartItems[0]->getProductClassId());
    }

    public function testAddProductsQuantity()
    {
        $this->cartService->addProduct(1);

        $quantity = $this->cartService->getCart()->getItems()->reduce(function ($q, $item) {
            $q += $item->getQuantity();

            return $q;
        });
        $this->assertEquals(1, $quantity);
    }

    public function testAddProductsQuantityOverSaleLimit()
    {
        $this->cartService->addProduct(10, 6);

        $quantity = $this->cartService->getCart()->getItems()->reduce(function ($q, $item) {
            $q += $item->getQuantity();

            return $q;
        });
        // 明細の丸め処理はpurchaseFlowで実行されるため、販売制限数を超えてもカートには入る
        $this->assertEquals(6, $quantity);
    }

    public function testAddProductsQuantityMultiItems()
    {
        /** @var ProductClass $ProductClass */
        $ProductClass = $this->productClassRepository->find(11);

        $this->cartService->addProduct($ProductClass, 101);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();

        $this->cartService->addProduct($ProductClass, 6);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();

        $quantity = $this->cartService->getCart()->getItems()->reduce(function ($q, $item) {
            $q += $item->getQuantity();

            return $q;
        });
        $this->assertEquals(5, $quantity);
    }

    public function testAddProductsWithCartItemComparator()
    {
        // 同じ商品規格で同じ数量なら同じ明細とみなすようにする
        $this->cartService->setCartItemComparator(new CartServiceTest_CartItemComparator());

        $ProductClass = $this->productClassRepository->find(2);

        $this->cartService->addProduct($ProductClass, 1);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();

        $this->cartService->addProduct($ProductClass, 1);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();

        /* @var \Eccube\Entity\CartItem[] $CartItems */
        $CartItems = $this->cartService->getCart()->getCartItems();
        self::assertEquals(1, count($CartItems));
        self::assertEquals(2, $CartItems[0]->getProductClassId());
        self::assertEquals(2, $CartItems[0]->getQuantity());

        $this->cartService->addProduct($ProductClass, 1);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();

        /* @var \Eccube\Entity\CartItem[] $CartItems */
        $CartItems = $this->cartService->getCart()->getCartItems();
        self::assertEquals(2, count($CartItems));
        self::assertEquals(2, $CartItems[0]->getProductClassId());
        self::assertEquals(2, $CartItems[0]->getQuantity());
        self::assertEquals(2, $CartItems[1]->getProductClassId());
        self::assertEquals(1, $CartItems[1]->getQuantity());
    }

    public function testUpProductQuantity()
    {
        $this->cartService->clear();
        /** @var ProductClass $ProductClass */
        $ProductClass = $this->productClassRepository->find(10);
        $this->cartService->addProduct($ProductClass, 1);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();
        $this->cartService->addProduct($ProductClass, 1);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();

        $quantity = $this->cartService->getCart()->getItems()->reduce(function ($q, $item) {
            $q += $item->getQuantity();

            return $q;
        });
        $this->assertEquals(2, $quantity);
    }

    public function testDownProductQuantity()
    {
        $this->cartService->clear();
        /** @var ProductClass $ProductClass */
        $ProductClass = $this->productClassRepository->find(10);
        $this->cartService->addProduct($ProductClass, 2);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();
        $this->cartService->addProduct($ProductClass, -1);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();

        $quantity = $this->cartService->getCart()->getItems()->reduce(function ($q, $item) {
            $q += $item->getQuantity();

            return $q;
        });
        $this->assertEquals(1, $quantity);
    }

    public function testRemoveProduct()
    {
        $this->cartService->addProduct(1, 2);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());
        $this->cartService->save();

        $this->cartService->removeProduct(1);

        $this->assertNull($this->cartService->getCart());
    }

    public function testSave()
    {
        $preOrderId = sha1(StringUtil::random(32));

        $ProductClass = $this->productClassRepository->find(1);
        $this->cartService->addProduct($ProductClass, 1);
        $this->cartService->setPreOrderId($preOrderId);
        $this->purchaseFlow->validate($this->cartService->getCart(), new PurchaseContext());

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
     *
     * @return boolean 同じ明細になる場合はtrue
     */
    public function compare(CartItem $item1, CartItem $item2)
    {
        return $item1->getProductClassId() == $item2->getProductClassId()
            && $item1->getQuantity() == $item2->getQuantity();
    }
}
