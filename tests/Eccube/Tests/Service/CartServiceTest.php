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

    /**
     * 必ず別カート商品と判定するストラテジーを追加
     */
    protected function addFalseStrategy()
    {
        $strategy = new \Eccube\Tests\Service\CartComparator\Strategy\FalseStrategy();
        $this->app['eccube.cart.comparator.context']->addStrategy($strategy);
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

    public function testDownProductQuantity_NotRemove()
    {
        $cartService = $this->app['eccube.service.cart'];

        $cartService->setProductQuantity(1, 1);
        $cartService->downProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);
        $this->assertEquals(1, $quantity);
        $this->assertCount(1, $cartService->getCart()->getCartItems());
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
        $cartService->setCanAddProductType($this->ProductType1);

        $this->expected = $this->ProductType1;
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

        try {
            $this->app['eccube.service.cart']->setProductQuantity($ProductClass, 2)->save();
        } catch (CartException $e) {
            $this->actual = $this->app['eccube.service.cart']->getError();
            $this->expected = 'cart.over.price_limit';
        }

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

    public function testSetProductQuantityWithOverSaleLimit()
    {
        $ProductClasses = $this->Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $ProductClass->setStockUnlimited(0);
        $ProductClass->setStock(10);
        $ProductClass->setSaleLimit(5);
        $this->app['orm.em']->flush();

        $this->app['eccube.service.cart']->setProductQuantity($ProductClass, 7)->save();

        $this->actual = $this->app['eccube.service.cart']->getErrors();
        $this->expected = array('cart.over.sale_limit');
        $this->verify();
    }

    public function testCanAddProductPaymentWithCartEmpty()
    {

        $this->actual = $this->app['eccube.service.cart']->canAddProductPayment($this->ProductType1);
        $this->assertTrue($this->actual, 'カートが空の場合は true');

        $this->expected = 4;
        $this->actual = count($this->app['eccube.service.cart']->getCart()->getPayments());
        $this->verify('設定されている支払い方法は'.$this->expected.'種類');
    }

    public function testSetProductQuantityWithMultipleProductType()
    {
        // カート投入
        $ProductClasses1 = $this->Product->getProductClasses();
        $ProductClasses2 = $this->Product2->getProductClasses();

        try {
            $this->app['eccube.service.cart']
                ->addProduct($ProductClasses1[0]->getId(), 1)
                ->save();
            $this->app['eccube.service.cart']
                ->addProduct($ProductClasses2[0]->getId(), 1)
                ->save();
            $this->fail();
        } catch (CartException $e) {
            $this->actual = $e->getMessage();
        }
        $this->expected = 'cart.product.type.kind';
        $this->verify('複数配送OFFの場合は複数商品種別のカート投入はエラー');
    }

    public function testSetProductQuantityWithMultipleShipping()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        // product_class_id = 2 の ProductType を 2 に変更
        $ProductClass = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find(2);
        $ProductClass->setProductType($this->ProductType2);

        // ProductType 1 と 2 で, 共通する支払い方法を削除しておく
        $PaymentOption = $this
            ->app['orm.em']
            ->getRepository('\Eccube\Entity\PaymentOption')
            ->findOneBy(
                array(
                    'delivery_id' => 1,
                    'payment_id' => 3
                )
            );
        $this->assertNotNull($PaymentOption);
        $this->app['orm.em']->remove($PaymentOption);
        $this->app['orm.em']->flush();

        // カート投入
        try {
            // XXX createProduct() で生成した商品を使いたいが,
            // createProduct() で生成すると CartService::getCart()->getCartItem() で
            // 商品が取得できないため, 初期設定商品を使用する
            $this->app['eccube.service.cart']->setProductQuantity(1, 1);
            $this->app['eccube.service.cart']->setProductQuantity(2, 1);
            $this->fail();
        } catch (CartException $e) {
            $this->actual = $e->getMessage();
        }
        $this->expected = 'cart.product.payment.kind';
        $this->verify('複数配送ONの場合は支払い方法の異なるカート投入はエラー');
    }


    public function testCanAddProductPaymentWithMultiple()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        // カート投入
        // XXX createProduct() で生成した商品を使いたいが,
        // createProduct() で生成すると CartService::getCart()->getCartItem() で
        // 商品が取得できないため, 初期設定商品を使用する
        $this->app['eccube.service.cart']->setProductQuantity(1, 1);
        $this->app['eccube.service.cart']->setProductQuantity(2, 1);

        $ProductType1 = $this->app['eccube.repository.master.product_type']->find(1);
        $this->actual = $this->app['eccube.service.cart']->canAddProductPayment($ProductType1);
        $this->assertTrue($this->actual, '共通の支払い方法が存在するため true');
    }

    public function testRemoveProductWithMultiple()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        // product_class_id = 2 の ProductType を 2 に変更
        $ProductClass = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find(2);
        $ProductClass->setProductType($this->ProductType2);

        // カート投入
        // XXX createProduct() で生成した商品を使いたいが,
        // createProduct() で生成すると CartService::getCart()->getCartItem() で
        // 商品が取得できないため, 初期設定商品を使用する
        $this->app['eccube.service.cart']->setProductQuantity(1, 1);
        $this->app['eccube.service.cart']->setProductQuantity(2, 1);

        $this->expected = 1;
        $this->actual = count($this->app['eccube.service.cart']->getCart()->getPayments());
        $this->verify('設定されている支払い方法は'.$this->expected.'種類');

        // ProductType2 の商品を削除すると支払い方法が再設定される
        $this->app['eccube.service.cart']->removeProduct(2);

        $this->expected = 4;
        $this->actual = count($this->app['eccube.service.cart']->getCart()->getPayments());
        $this->verify('設定されている支払い方法は'.$this->expected.'種類');
    }

    public function testGetProductTypetWithMultiple()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        // product_class_id = 2 の ProductType を 2 に変更
        $ProductClass = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find(2);
        $ProductClass->setProductType($this->ProductType2);

        // カート投入
        // XXX createProduct() で生成した商品を使いたいが,
        // createProduct() で生成すると CartService::getCart()->getCartItem() で
        // 商品が取得できないため, 初期設定商品を使用する
        $this->app['eccube.service.cart']->setProductQuantity(1, 1);
        $this->app['eccube.service.cart']->setProductQuantity(2, 1);
        $this->app['eccube.service.cart']->setProductQuantity(3, 1);

        $ProductTypes = $this->app['eccube.service.cart']->getProductTypes();

        $this->expected = 2;
        $this->actual = count($ProductTypes);
        $this->verify();
    }

    public function testGenerateCartItem()
    {
        $ProductClasses = $this->Product->getProductClasses();
        $ProductClass = $ProductClasses[0];

        $CartItem = $this->app['eccube.service.cart']->generateCartItem($ProductClass);
        $this->assertInstanceOf('Eccube\Entity\CartItem', $CartItem);

        $this->assertEquals($ProductClass, $CartItem->getObject());
        $this->assertEquals($ProductClass->getId(), $CartItem->getClassId());
        $this->assertEquals($ProductClass->getPrice02IncTax(), $CartItem->getPrice());
        $this->assertEquals(1, $CartItem->getQuantity());

        $CartItem = $this->app['eccube.service.cart']->generateCartItem($ProductClass->getId());
        $this->assertInstanceOf('Eccube\Entity\CartItem', $CartItem);

        $this->assertEquals($ProductClass, $CartItem->getObject());
        $this->assertEquals($ProductClass->getId(), $CartItem->getClassId());
        $this->assertEquals($ProductClass->getPrice02IncTax(), $CartItem->getPrice());
        $this->assertEquals(1, $CartItem->getQuantity());
    }

    public function testGenerateCartItem_invalidArgument()
    {
        try {
            $this->app['eccube.service.cart']->generateCartItem(null);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testAddCartItem_ProductClassEntity()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $CartItem = $cartService->generateCartItem(1);
        $cartService->addCartItem($CartItem);
        $CartItems = $cartService->getCart()->getCartItems();

        $this->assertEquals('Eccube\Entity\ProductClass', $CartItems[0]->getClassName());
        $this->assertEquals(1, $CartItems[0]->getClassId());
    }

    public function testAddCartItem_Quantity()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];
        $this->assertCount(0, $cartService->getCart()->getCartItems());

        $CartItem = $cartService->generateCartItem(1);
        $cartService->addCartItem($CartItem);
        $this->assertEquals(1, $cartService->getCartItemQuantity($CartItem));

        $cartService->clear();

        $CartItem = $cartService->generateCartItem(10);
        $CartItem->setQuantity(6);
        $cartService->addCartItem($CartItem);
        $this->assertEquals(5, $cartService->getCartItemQuantity($CartItem));

        $cartService->clear();

        $CartItem = $cartService->generateCartItem(10);
        $CloneCartItem = clone $CartItem;
        $CartItem->setQuantity(101);
        $cartService->addCartItem($CartItem);
        $this->assertEquals(5, $cartService->getCartItemQuantity($CloneCartItem));
    }

    public function testAddCartItem_duplicateProductClass()
    {
        $this->addFalseStrategy();
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        /** @var \Doctrine\Common\Collections\ArrayCollection $CartItems */
        $CartItems = $cartService->getCart()->getCartItems();
        $this->assertCount(0, $CartItems);

        $CartItem = $cartService->generateCartItem(10);
        $cartService->addCartItem($CartItem);
        $this->assertCount(1, $CartItems);
        $this->assertEquals(1, $CartItems->last()->getQuantity());

        $CartItem = $cartService->generateCartItem(10);
        $CartItem->setQuantity(2);
        $cartService->addCartItem($CartItem);
        $this->assertCount(2, $CartItems);
        $this->assertEquals(2, $CartItems->last()->getQuantity());

        $CartItem = $cartService->generateCartItem(10);
        $CartItem->setQuantity(10);
        $cartService->addCartItem($CartItem);
        $this->assertCount(3, $CartItems);
        // セットした数量は10だが、購入制限で2個に減らされる
        $this->assertEquals(2, $CartItems->last()->getQuantity());
    }

    public function testSetCartItemQuantityWithObject()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $ProductClasses = $this->Product->getProductClasses();
        $CartItem = $this->app['eccube.service.cart']->generateCartItem($ProductClasses[0]);
        $cartService
            ->setCartItemQuantity($CartItem, 1)
            ->save();

        $Cart = $this->app['session']->get('cart');
        $CartItems = $Cart->getCartItems();

        $this->assertCount(1, $CartItems);
    }

    public function testSetCartItemQuantityWithProductHide()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
        $this->Product->setStatus($Disp);
        $this->app['orm.em']->flush();

        try {
            $ProductClasses = $this->Product->getProductClasses();
            $ProductClass = $ProductClasses[0];
            $CartItem = $cartService->generateCartItem($ProductClass);
            $cartService
                ->setCartItemQuantity($CartItem, 1)
                ->save();
            $this->fail();
        } catch (CartException $e) {
            $this->expected = 'cart.product.not.status';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    public function testSetCartItemQuantityWithOverPrice()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $ProductClasses = $this->Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $ProductClass->setPrice02($this->app['config']['max_total_fee']);
        $this->app['orm.em']->flush();

        try {
            $CartItem = $cartService->generateCartItem($ProductClass);
            $cartService->setCartItemQuantity($CartItem, 2)->save();
        } catch (CartException $e) {
            $this->actual = $cartService->getError();
            $this->expected = 'cart.over.price_limit';
        }

        $this->verify();
    }

    public function testSetCartItemQuantityWithOverStock()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $ProductClasses = $this->Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $ProductClass->setStockUnlimited(0);
        $ProductClass->setStock(10);
        $this->app['orm.em']->flush();

        $CartItem = $cartService->generateCartItem($ProductClass);
        $cartService->setCartItemQuantity($CartItem, 20)->save();

        $this->actual = $cartService->getErrors();
        $this->expected = array('cart.over.stock');
        $this->verify();
    }

    public function testSetCartItemQuantityWithOverStock_duplicateProductClass()
    {
        $this->addFalseStrategy();
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $ProductClasses = $this->Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $ProductClass->setStockUnlimited(0);
        $ProductClass->setStock(10);
        $this->app['orm.em']->flush();

        $CartItem = $cartService->generateCartItem($ProductClass);
        $cartService->setCartItemQuantity($CartItem, 4)->save();

        $this->actual = $cartService->getErrors();
        $this->expected = array();
        $this->verify();

        $CartItem = $cartService->generateCartItem($ProductClass);
        $cartService->setCartItemQuantity($CartItem, 4)->save();

        $this->actual = $cartService->getErrors();
        $this->expected = array();
        $this->verify();

        $CartItem = $cartService->generateCartItem($ProductClass);
        $cartService->setCartItemQuantity($CartItem, 4)->save();

        $this->actual = $cartService->getErrors();
        $this->expected = array('cart.over.stock');
        $this->verify();
    }

    public function testSetCartItemQuantityWithOverSaleLimit()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $ProductClasses = $this->Product->getProductClasses();
        /** @var \Eccube\Entity\ProductClass $ProductClass */
        $ProductClass = $ProductClasses[0];
        $ProductClass
            ->setStockUnlimited(0)
            ->setStock(10)
            ->setSaleLimit(5);
        $this->app['orm.em']->flush();

        $CartItem = $cartService->generateCartItem($ProductClass);
        $cartService->setCartItemQuantity($CartItem, 7)->save();

        $this->actual = $cartService->getErrors();
        $this->expected = array('cart.over.sale_limit');
        $this->verify();
    }

    public function testSetCartItemQuantityWithOverSaleLimit_duplicateProductClass()
    {
        $this->addFalseStrategy();
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $ProductClasses = $this->Product->getProductClasses();
        /** @var \Eccube\Entity\ProductClass $ProductClass */
        $ProductClass = $ProductClasses[0];
        $ProductClass
            ->setStockUnlimited(0)
            ->setStock(10)
            ->setSaleLimit(5);
        $this->app['orm.em']->flush();

        $CartItem = $cartService->generateCartItem($ProductClass);
        $cartService->setCartItemQuantity($CartItem, 2)->save();

        $this->actual = $cartService->getErrors();
        $this->expected = array();
        $this->verify();

        $CartItem = $cartService->generateCartItem($ProductClass);
        $cartService->setCartItemQuantity($CartItem, 2)->save();

        $this->actual = $cartService->getErrors();
        $this->expected = array();
        $this->verify();

        $CartItem = $cartService->generateCartItem($ProductClass);
        $cartService->setCartItemQuantity($CartItem, 2)->save();

        $this->actual = $cartService->getErrors();
        $this->expected = array('cart.over.sale_limit');
        $this->verify();
    }

    public function testSetCartItemQuantityWithMultipleProductType()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        // カート投入
        $ProductClasses1 = $this->Product->getProductClasses();
        $ProductClasses2 = $this->Product2->getProductClasses();

        try {
            $cartService
                ->addCartItem($cartService->generateCartItem($ProductClasses1[0]))
                ->save();
            $cartService
                ->addCartItem($cartService->generateCartItem($ProductClasses2[0]))
                ->save();
            $this->fail();
        } catch (CartException $e) {
            $this->actual = $e->getMessage();
        }
        $this->expected = 'cart.product.type.kind';
        $this->verify('複数配送OFFの場合は複数商品種別のカート投入はエラー');
    }

    public function testSetCartItemQuantityWithMultipleShipping()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        // product_class_id = 2 の ProductType を 2 に変更
        $ProductClass = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find(2);
        $ProductClass->setProductType($this->ProductType2);

        // ProductType 1 と 2 で, 共通する支払い方法を削除しておく
        $PaymentOption = $this
            ->app['orm.em']
            ->getRepository('\Eccube\Entity\PaymentOption')
            ->findOneBy(
                array(
                    'delivery_id' => 1,
                    'payment_id' => 3
                )
            );
        $this->assertNotNull($PaymentOption);
        $this->app['orm.em']->remove($PaymentOption);
        $this->app['orm.em']->flush();

        // カート投入
        try {
            // XXX createProduct() で生成した商品を使いたいが,
            // createProduct() で生成すると CartService::getCart()->getCartItem() で
            // 商品が取得できないため, 初期設定商品を使用する
            $cartService->setCartItemQuantity($cartService->generateCartItem(1), 1);
            $cartService->setCartItemQuantity($cartService->generateCartItem(2), 1);
            $this->fail();
        } catch (CartException $e) {
            $this->actual = $e->getMessage();
        }
        $this->expected = 'cart.product.payment.kind';
        $this->verify('複数配送ONの場合は支払い方法の異なるカート投入はエラー');
    }

    public function testRemoveCartNo()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];
        $ProductClasses = $this->Product->getProductClasses();

        $cartService->setCartItemQuantity($cartService->generateCartItem($ProductClasses[0]), 2);
        $cartService->setCartItemQuantity($cartService->generateCartItem($ProductClasses[1]), 2);
        $cartService->setCartItemQuantity($cartService->generateCartItem($ProductClasses[2]), 2);

        $this->assertCount(3, $cartService->getCart()->getCartItems());
        $cartService->removeCartNo(0);
        $this->assertCount(2, $cartService->getCart()->getCartItems());
        $cartService->removeCartNo(0);
        $this->assertCount(2, $cartService->getCart()->getCartItems());
        $cartService->removeCartNo(3);
        $this->assertCount(2, $cartService->getCart()->getCartItems());
        $cartService->removeCartNo(2);
        $this->assertCount(1, $cartService->getCart()->getCartItems());
    }

    public function testRemoveCartNo_duplicateProductClass()
    {
        $this->addFalseStrategy();
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];
        $CartItems = $cartService->getCart()->getCartItems();

        $ci = function () use ($cartService) {
            $CartItem = $cartService->generateCartItem(1);
            $cartService->addCartItem($CartItem);
            return $CartItem;
        };

        $CartItem1 = $ci();
        $CartItem2 = $ci();
        $CartItem3 = $ci();

        $this->assertContains($CartItem1, $CartItems);
        $this->assertContains($CartItem2, $CartItems);
        $this->assertContains($CartItem3, $CartItems);

        $cartService->removeCartNo($CartItem2->getCartNo());
        $this->assertContains($CartItem1, $CartItems);
        $this->assertNotContains($CartItem2, $CartItems);
        $this->assertContains($CartItem3, $CartItems);

        $cartService->removeCartNo(9999);
        $this->assertContains($CartItem1, $CartItems);
        $this->assertNotContains($CartItem2, $CartItems);
        $this->assertContains($CartItem3, $CartItems);

        $cartService->removeCartNo($CartItem1->getCartNo());
        $this->assertNotContains($CartItem1, $CartItems);
        $this->assertNotContains($CartItem2, $CartItems);
        $this->assertContains($CartItem3, $CartItems);
    }

    public function testRemoveCartItemWithMultiple()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        // product_class_id = 2 の ProductType を 2 に変更
        $ProductClass = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find(2);
        $ProductClass->setProductType($this->ProductType2);

        // カート投入
        // XXX createProduct() で生成した商品を使いたいが,
        // createProduct() で生成すると CartService::getCart()->getCartItem() で
        // 商品が取得できないため, 初期設定商品を使用する
        $CartItem1 = $cartService->generateCartItem(1);
        $CartItem2 = $cartService->generateCartItem(2);
        $cartService->setCartItemQuantity($CartItem1, 1);
        $cartService->setCartItemQuantity($CartItem2, 1);

        $this->expected = 1;
        $this->actual = count($cartService->getCart()->getPayments());
        $this->verify('設定されている支払い方法は' . $this->expected . '種類');

        // ProductType2 の商品を削除すると支払い方法が再設定される
        $cartService->removeCartNo(1);

        $this->expected = 4;
        $this->actual = count($cartService->getCart()->getPayments());
        $this->verify('設定されている支払い方法は' . $this->expected . '種類');
    }

    public function testUpCartNoQuantity()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $CartItem = $cartService->generateCartItem(1);
        $cartService->setCartItemQuantity($CartItem, 1);
        $cart_no = $CartItem->getCartNo();

        $cartService->upCartNoQuantity($cart_no);
        $quantity = $cartService->getCartItemQuantity($CartItem);
        $this->assertEquals(2, $quantity);
    }

    public function testDownCartNoQuantity()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $CartItem = $cartService->generateCartItem(1);
        $cartService->setCartItemQuantity($CartItem, 2);
        $cart_no = $CartItem->getCartNo();

        $cartService->downCartNoQuantity($cart_no);
        $quantity = $cartService->getCartItemQuantity($CartItem);
        $this->assertEquals(1, $quantity);
    }

    public function testDownCartNoQuantity_NotRemove()
    {
        /** @var \Eccube\Service\CartService $cartService */
        $cartService = $this->app['eccube.service.cart'];

        $CartItem = $cartService->generateCartItem(1);
        $cartService->setCartItemQuantity($CartItem, 1);
        $cart_no = $CartItem->getCartNo();

        $cartService->downCartNoQuantity($cart_no);
        $quantity = $cartService->getCartItemQuantity($CartItem);
        $this->assertEquals(1, $quantity);
        $this->assertCount(1, $cartService->getCart()->getCartItems());
    }
}
