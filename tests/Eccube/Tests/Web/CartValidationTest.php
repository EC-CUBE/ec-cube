<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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


namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Entity\Master\Disp;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Symfony\Component\HttpKernel\Client;

class CartValidationTest extends AbstractWebTestCase
{
    /**
     * setup mail
     */
    public function setUp()
    {
        parent::setUp();
        $this->initializeMailCatcher();
    }

    /**
     * tear down
     */
    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    // 商品詳細画面からカート画面のvalidation

    /**
     * 在庫制限チェック
     */
    public function testValidationStock()
    {
        /** @var Product $Product */
        $Product = $this->createProduct('test1');

        $ProductClass = $Product->getProductClasses()->get(1);

        // 在庫数を設定
        $ProductClass->setStock(1);
        $this->app['orm.em']->persist($ProductClass);

        $arr = array(
            'product_id' => $Product->getId(),
            'mode' => 'add_cart',
            'product_class_id' => $ProductClass->getId(),
            'quantity' => 9999,
            '_token' => 'dummy',
        );
        if ($ProductClass->hasClassCategory1()) {
            $arr['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arr['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        /** @var Client $client */
        $client = $this->client;

        $crawler = $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $Product->getId())),
            $arr
        );

        $crawler = $client->followRedirect();

        // エラーメッセージは改行されているため2回に分けてチェック

        $message = $crawler->filter('.errormsg')->text();

        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);

        $this->assertContains( '一度に在庫数を超える購入はできません。', $message);

    }

    /**
     * Test product in cart when product is deleting.
     */
    public function testProductInCartDeleted()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        /** @var Product $Product */
        $Product = $this->createProduct('test', 1, 1);

        $productClassId = $Product->getProductClasses()->first()->getId();
        $productId = $Product->getId();

        $arrForm = array(
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => 1,
            '_token' => 'dummy',
        );

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $productId))
        );

        // delete
        $this->deleteAllProduct();

        // submit
        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $productId)),
            $arrForm
        );

        $this->fail('404! Page not found!');
    }

    /**
     * Test product in cart when product is private.
     */
    public function testProductInCartIsPrivate()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        /** @var Product $Product */
        $Product = $this->createProduct('test', 1, 1);

        $productClassId = $Product->getProductClasses()->first()->getId();
        $productId = $Product->getId();

        $arrForm = array(
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => 1,
            '_token' => 'dummy',
        );

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $productId))
        );

        // private
        $this->changeStatus($Product, Disp::DISPLAY_HIDE);

        // submit
        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $productId)),
            $arrForm
        );

        $this->fail('404! Page not found!');
    }

    /**
     * Test product in cart when product is stock out.
     * @NOTE:
     * No stock hidden flg -> false
     */
    public function testProductInCartIsStockOut()
    {
        /** @var Product $Product */
        $Product = $this->createProduct('test', 0, 1);
        $ProductClass = $Product->getProductClasses()->first();

        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        /** @var Client $client */
        $client = $this->client;

        // Stock out
        $ProductClass->setStock(0);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->flush();

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $productId))
        );

        // submit
        $arrForm = array(
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => 1,
            '_token' => 'dummy',
        );

        $crawler = $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $productId)),
            $arrForm
        );

        $html = $crawler->filter('#detail_cart_box__button_area')->html();

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertContains('ただいま品切れ中です', $html);
    }

    /**
     * Test product in cart when product is stock out.
     * @NOTE:
     * No stock hidden flg -> false
     */
    public function testProductInCartIsStockOutWithProductClass()
    {
        /** @var Product $Product */
        $Product = $this->createProduct('test', 2, 1);
        $ProductClass = $Product->getProductClasses()->first();

        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        /** @var Client $client */
        $client = $this->client;

        // Stock out
        $ProductClass->setStock(0);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->flush();

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $productId))
        );

        // submit
        $arrForm = array(
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => 1,
            '_token' => 'dummy',
        );
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $crawler = $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $productId)),
            $arrForm
        );
        $crawler = $client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);

        $this->assertEmpty($crawler->filter('#cart_box__message--2'));

        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

    }

    /**
     * Test product in cart when product is not enough
     */
    public function testProductInCartIsNotEnough()
    {
        $stock = 1;
        $productName = $this->getFaker()->word;
        /** @var Product $Product */
        $Product = $this->createProduct($productName, 1, $stock);
        $ProductClass = $Product->getProductClasses()->first();

        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $productId))
        );

        // submit
        $arrForm = array(
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => $stock + 1,
            '_token' => 'dummy',
        );
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $productId)),
            $arrForm
        );

        // check error message
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $message = $crawler->filter('.errormsg')->text();

        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);

        $this->assertContains('一度に在庫数を超える購入はできません。', $message);

        // check quantity on cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();

        $this->actual = $CartItem->getQuantity();
        $this->expected = $stock;
        $this->verify('Cart quantity not equal!');
    }

    /**
     * 金額の上限と販売制限確認
     */
    public function testProductInCartIsNotEnoughAndLimit()
    {
        $productName = $this->getFaker()->word;
        /** @var Product $Product */
        $Product = parent::createProduct($productName, 1);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setPrice02(999999911);
        $this->changeStock($ProductClass, 10);
        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $Product->getId()))
        );
        // submit
        $arrForm = array(
            'product_id' => $Product->getId(),
            'mode' => 'add_cart',
            'product_class_id' => $ProductClass->getId(),
            'quantity' => 9,
            '_token' => 'dummy',
        );
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $Product->getId())),
            $arrForm
        );

        $stock = 2000000;
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, 1, 100);
        $ProductClass = $Product->getProductClasses()->first();

        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $productId))
        );

        // submit
        $arrForm = array(
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => $stock ,
            '_token' => 'dummy',
        );
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $productId)),
            $arrForm
        );

        // check error message
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $message = $crawler->filter('div#cart_box__message--1')->text();
        $this->assertContains('商品を購入できる金額の上限を超えております。数量を調整してください。', $message);

        $message = $crawler->filter('div#cart_box__message--2')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
    }

    /**
     * Test product in cart when product has other type
     */
    public function testProductInCartProductType()
    {
        // disable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::DISABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();

        // Stock
        $stock = 10;
        $productName = $this->getFaker()->word;
        /** @var Product $Product */
        $Product = $this->createProduct($productName, 1, $stock);
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setProductType($ProductType);
        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $productId))
        );

        // submit product type 2
        $arrForm = array(
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => 1,
            '_token' => 'dummy',
        );
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $productId)),
            $arrForm
        );

        // submit product type 1
        $arrForm = array(
            'product_id' => 1,
            'mode' => 'add_cart',
            'product_class_id' => 1,
            'classcategory_id1' => 3,
            'classcategory_id2' => 6,
            'quantity' => 1,
            '_token' => 'dummy',
        );

        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => 1)),
            $arrForm
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $message = $crawler->filter('.errormsg')->text();
        $this->assertContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * Test product in cart when product has other type
     * with MultiShipping
     * enable add cart
     */
    public function testProductInCartProductTypeWithMultiShipping()
    {
        // enable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();

        // Stock
        $stock = 10;
        $productName = $this->getFaker()->word;
        /** @var Product $Product */
        $Product = $this->createProduct($productName, 1, $stock);
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setProductType($ProductType);
        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $productId))
        );

        // submit product type 2
        $arrForm = array(
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => 1,
            '_token' => 'dummy',
        );
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $productId)),
            $arrForm
        );

        // submit product type 1
        $arrForm = array(
            'product_id' => 1,
            'mode' => 'add_cart',
            'product_class_id' => 1,
            'classcategory_id1' => 3,
            'classcategory_id2' => 6,
            'quantity' => 1,
            '_token' => 'dummy',
        );

        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => 1)),
            $arrForm
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();
        
        // expect not contain the error message
        $this->assertEmpty($crawler->filter('.errormsg'));
    }

    /**
     * Test product in cart when product stock sale limit
     */
    public function testProductInCartStockLimit()
    {
        // Stock
        $stock = 10;
        // Sale limit
        $limit = 5;

        $productName = $this->getFaker()->word;
        /** @var Product $Product */
        $Product = $this->createProduct($productName, 1, $stock);
        $ProductClass = $Product->getProductClasses()->first();

        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        // Sale limit
        $ProductClass->setSaleLimit($limit);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->app->url('product_detail', array('id' => $productId))
        );

        // submit
        $arrForm = array(
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => $limit + 1,
            '_token' => 'dummy',
        );
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }
        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $productId)),
            $arrForm
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $message = $crawler->filter('.errormsg')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);

        // check quantity on cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();

        $this->actual = $CartItem->getQuantity();
        $this->expected = $limit;
        $this->verify('Cart item quantity not equal!');
    }

    /**
     * Test product in cart when product is deleting by shopping step
     */
    public function testProductInCartDeletedFromShopping()
    {
        $this->logIn();

        /** @var Product $Product */
        $Product = $this->createProduct('test', 1, 1);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $this->scenarioCartIn($client, $productClassId);

        // Remove product (delete flg)
        $Product->setDelFlg(Constant::ENABLED);
        $ProductClass->setDelFlg(Constant::ENABLED);
        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();
        $crawler = $client->followRedirect();

        $message = $crawler->filter('body')->text();

        $this->assertContains('現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。', $message);
    }

    /**
     * Test product in cart when product is private from shopping step
     */
    public function testProductInCartIsPrivateFromShopping()
    {
        $this->logIn();

        /** @var Product $Product */
        $Product = $this->createProduct('test', 1, 1);
        $productClassId = $Product->getProductClasses()->first()->getId();

        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $this->scenarioCartIn($client, $productClassId);

        // change status
        $this->changeStatus($Product, Disp::DISPLAY_HIDE);

        $this->scenarioConfirm($client);

        // two redirect???
        $client->followRedirect();
        $crawler = $client->followRedirect();

        $message = $crawler->filter('#cart_box__body .errormsg')->text();

        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();

        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item not empty!');
    }

    /**
     * Test product in cart when product out of stock from shopping step
     */
    public function testProductInCartOutOfStockFromShopping()
    {
        $this->logIn();

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, 1, 10);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $this->scenarioCartIn($client, $productClassId);

        // change stock
        $this->changeStock($ProductClass, 0);

        $this->scenarioConfirm($client);

        // two redirect???
        $client->followRedirect();
        $crawler = $client->followRedirect();

        // check message error
        $message = $crawler->filter('#cart_box__body .errormsg')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();

        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item not empty!');
    }

    /**
     * Test product in cart when product stock not enough from shopping step
     */
    public function testProductInCartStockNotEnoughFromShopping()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change stock
        $currentStock = $stockInCart - 1;
        $this->changeStock($ProductClass, $currentStock);

        $this->scenarioConfirm($client);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        // cart or shopping???
        $message = $crawler->filter('#confirm_flow_box__body .errormsg')->text();
//        $message = $crawler->filter('#cart_box__body .errormsg')->text();

        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();

        $this->actual = $CartItem->getQuantity();
        $this->expected = $currentStock;
        $this->verify('Cart item quantity not equal!');
    }

    /**
     * Test product in cart when product stock is limit from shopping step
     */
    public function testProductInCartStockLimitFromShopping()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;
        $limit = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = $limit + 1;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // Sale limit
        $ProductClass->setSaleLimit($limit);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        $this->scenarioConfirm($client);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        // cart or shopping???
        $message = $crawler->filter('#confirm_flow_box__body .errormsg')->text();

        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();

        $this->actual = $CartItem->getQuantity();
        $this->expected = $limit;
        $this->verify('Cart item quantity not equal!');
    }

    /**
     * Test product in cart when product type change from shopping step
     */
    public function testProductInCartProductTypeFromShopping()
    {
        // GIVE
        // disable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::DISABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();

        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        // product type A
        $productName = $this->getFaker()->word;
        /** @var Product $Product */
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $this->scenarioCartIn($client, $productClassId);

        // Delete related delivery type
        $Delivery = $this->app['eccube.repository.delivery']->find(1);
        $Delivery
            ->setDelFlg(Constant::ENABLED)
            ->setRank(0);
        $this->app['orm.em']->persist($Delivery);
        $this->app['orm.em']->flush($Delivery);

        // shopping
        $crawler = $this->scenarioConfirm($client);
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();

        // THEN
        // check page title
        $message = $crawler->filter('h1.page-heading')->text();
        $this->assertContains('ショッピングカート', $message);
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('配送の準備ができていない商品が含まれております。', $message);
        $this->assertContains('恐れ入りますがお問い合わせページよりお問い合わせください。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
    }

    /**
     * Test product in cart when product is deleting before plus one
     */
    public function testProductInCartIsDeletedBeforePlus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // Remove product (delete flg)
        $Product->setDelFlg(Constant::ENABLED);
        $ProductClass->setDelFlg(Constant::ENABLED);
        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // cart up
        $this->scenarioCartUp($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product is private before plus one
     */
    public function testProductInCartIsPrivateBeforePlus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change status
        $this->changeStatus($Product, Disp::DISPLAY_HIDE);

        // cart up
        $this->scenarioCartUp($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product out of stock before plus one
     */
    public function testProductInCartProductOutOfStockBeforePlus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // cart up
        $this->scenarioCartUp($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product is not enough before plus one
     */
    public function testProductInCartProductStockIsNotEnoughBeforePlus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // cart up
        $this->scenarioCartUp($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $stock;
        $this->verify('Cart item quantity is not enough!!');
    }

    /**
     * Test product in cart when product sale limit is not enough before plus one
     */
    public function testProductInCartSaleLimitIsNotEnoughBeforePlus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // sale limit
        $saleLimit = 1;
        $ProductClass->setSaleLimit($saleLimit);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // cart up
        $this->scenarioCartUp($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $saleLimit;
        $this->verify('Cart item sale quantity has been limited!!');
    }

    /**
     * Test product in cart when product type is changing before plus one
     */
    public function testProductInCartChangeProductTypeBeforePlus()
    {
        // GIVE
        // disable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::DISABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();

        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // product 2
        $productName2 = $this->getFaker()->word;
        $Product2 = $this->createProduct($productName2, $productClassNum, $productStock);
        $ProductClass2 = $Product2->getProductClasses()->first();
        $productClassId2 = $ProductClass2->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);
        $this->app['eccube.service.cart']->unlock();
        $this->scenarioCartIn($client, $productClassId2, $stockInCart);

        // Change product type
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass->setProductType($ProductType);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // cart up
        $this->scenarioCartUp($client, $productClassId);
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * Test product in cart when product type is changing before plus one
     * with MultiShipping
     * enable add cart
     */
    public function testProductInCartChangeProductTypeBeforePlusWithMultiShipping()
    {
        // GIVE
        // enable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();

        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // product 2
        $productName2 = $this->getFaker()->word;
        $Product2 = $this->createProduct($productName2, $productClassNum, $productStock);
        $ProductClass2 = $Product2->getProductClasses()->first();
        $productClassId2 = $ProductClass2->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);
        $this->app['eccube.service.cart']->unlock();
        $this->scenarioCartIn($client, $productClassId2, $stockInCart);

        // Change product type
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass->setProductType($ProductType);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // cart up
        $this->scenarioCartUp($client, $productClassId);
        $crawler = $client->followRedirect();

        // THEN
        // check message error (expect not contain)
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertNotContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * Test product in cart when product is deleting before plus one
     */
    public function testProductInCartIsDeletedBeforeMinus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // Remove product (delete flg)
        $Product->setDelFlg(Constant::ENABLED);
        $ProductClass->setDelFlg(Constant::ENABLED);
        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // cart down
        $this->scenarioCartDown($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product is private before Minus one
     */
    public function testProductInCartIsPrivateBeforeMinus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change status
        $this->changeStatus($Product, Disp::DISPLAY_HIDE);

        // cart down
        $this->scenarioCartDown($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product out of stock before Minus one
     */
    public function testProductInCartProductOutOfStockBeforeMinus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // cart down
        $this->scenarioCartDown($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product is not enough before Minus one
     */
    public function testProductInCartProductStockIsNotEnoughBeforeMinus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // cart down
        $this->scenarioCartDown($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $stock;
        $this->verify('Cart item quantity is not enough!!');
    }

    /**
     * Test product in cart when product sale limit is not enough before Minus one
     */
    public function testProductInCartSaleLimitIsNotEnoughBeforeMinus()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // sale limit
        $saleLimit = 1;
        $ProductClass->setSaleLimit($saleLimit);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // cart down
        $this->scenarioCartDown($client, $productClassId);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $saleLimit;
        $this->verify('Cart item sale quantity has been limited!!');
    }

    /**
     * Test product in cart when product type is changing before Minus one
     */
    public function testProductInCartChangeProductTypeBeforeMinus()
    {
        // GIVE
        // disable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::DISABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();

        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // product 2
        $productName2 = $this->getFaker()->word;
        $Product2 = $this->createProduct($productName2, $productClassNum, $productStock);
        $ProductClass2 = $Product2->getProductClasses()->first();
        $productClassId2 = $ProductClass2->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);
        $this->app['eccube.service.cart']->unlock();
        $this->scenarioCartIn($client, $productClassId2, $stockInCart);

        // Change product type
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass->setProductType($ProductType);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // cart down
        $this->scenarioCartDown($client, $productClassId);
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * Test product in cart when product type is changing before Minus one
     * with MultiShipping
     * enable add cart
     */
    public function testProductInCartChangeProductTypeBeforeMinusWithMultiShipping()
    {
        // GIVE
        // enable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();

        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // product 2
        $productName2 = $this->getFaker()->word;
        $Product2 = $this->createProduct($productName2, $productClassNum, $productStock);
        $ProductClass2 = $Product2->getProductClasses()->first();
        $productClassId2 = $ProductClass2->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);
        $this->app['eccube.service.cart']->unlock();
        $this->scenarioCartIn($client, $productClassId2, $stockInCart);

        // Change product type
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass->setProductType($ProductType);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // cart down
        $this->scenarioCartDown($client, $productClassId);
        $crawler = $client->followRedirect();

        // THEN
        // check message error (expect not contain)
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertNotContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * Test product in cart when product is deleting on the top page
     */
    public function testProductInCartIsDeletedWhileReturnTopPage()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // Move to top
        $crawler = $client->request('GET', $this->app->url('homepage'));

        // Remove product (delete flg)
        $Product->setDelFlg(Constant::ENABLED);
        $ProductClass->setDelFlg(Constant::ENABLED);
        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // move to cart
        $crawler = $client->request('GET', $this->app->url('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product is private on the top page
     */
    public function testProductInCartIsPrivateWhileReturnTopPage()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change status
        $this->changeStatus($Product, Disp::DISPLAY_HIDE);

        // move to cart
        $crawler = $client->request('GET', $this->app->url('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product out of stock on the top page
     */
    public function testProductInCartProductOutOfStockWhileReturnTopPage()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // move to cart
        $crawler = $client->request('GET', $this->app->url('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product is not enough before Minus one
     */
    public function testProductInCartProductStockIsNotEnoughWhileReturnTopPage()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // move to cart
        $crawler = $client->request('GET', $this->app->url('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $stock;
        $this->verify('Cart item quantity is not enough!!');
    }

    /**
     * Test product in cart when product sale limit is not enough before Minus one
     */
    public function testProductInCartSaleLimitIsNotEnoughWhileReturnTopPage()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // Move to top
        $crawler = $client->request('GET', $this->app->url('homepage'));

        // sale limit
        $saleLimit = 1;
        $ProductClass->setSaleLimit($saleLimit);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // move to cart
        $crawler = $client->request('GET', $this->app->url('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $saleLimit;
        $this->verify('Cart item sale quantity has been limited!!');
    }

    /**
     * Test product in cart when product is deleting by shopping step back to cart
     */
    public function testProductInCartDeletedFromShoppingBackToCart()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $this->scenarioCartIn($client, $productClassId);

        // shopping step
        $this->scenarioConfirm($client);

        $crawler = $client->followRedirect();

        // Remove product (delete flg)
        $Product->setDelFlg(Constant::ENABLED);
        $ProductClass->setDelFlg(Constant::ENABLED);
        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // back to cart
        $urlBackToCart = $crawler->filter('#confirm_box__quantity_edit_button')->selectLink('数量を変更または削除する')->link()->getUri();
        $crawler = $client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product is private from shopping step back to cart
     */
    public function testProductInCartIsPrivateFromShoppingBackToCart()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $crawler = $client->followRedirect();

        // change status
        $this->changeStatus($Product, Disp::DISPLAY_HIDE);

        // back to cart
        $urlBackToCart = $crawler->filter('#confirm_box__quantity_edit_button')->selectLink('数量を変更または削除する')->link()->getUri();
        $crawler = $client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product out of stock from shopping step back to cart
     */
    public function testProductInCartOutOfStockFromShoppingBackToCart()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $crawler = $client->followRedirect();

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // back to cart
        $urlBackToCart = $crawler->filter('#confirm_box__quantity_edit_button')->selectLink('数量を変更または削除する')->link()->getUri();
        $crawler = $client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product stock not enough from shopping step back to cart
     */
    public function testProductInCartStockNotEnoughFromShoppingBackToCart()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $crawler = $client->followRedirect();

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // back to cart
        $urlBackToCart = $crawler->filter('#confirm_box__quantity_edit_button')->selectLink('数量を変更または削除する')->link()->getUri();
        $crawler = $client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $stock;
        $this->verify('Cart item quantity is not enough!!');
    }

    /**
     * Test product in cart when product stock is limit from shopping step back to cart
     */
    public function testProductInCartStockLimitFromShoppingBackToCart()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $crawler = $client->followRedirect();

        // sale limit
        $saleLimit = 1;
        $ProductClass->setSaleLimit($saleLimit);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // back to cart
        $urlBackToCart = $crawler->filter('#confirm_box__quantity_edit_button')->selectLink('数量を変更または削除する')->link()->getUri();
        $crawler = $client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $saleLimit;
        $this->verify('Cart item sale quantity has been limited!!');
    }

    /**
     * Test product in cart when product is deleting by shopping step change payment
     */
    public function testProductInCartDeletedFromShoppingChangePayment()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $this->scenarioCartIn($client, $productClassId);

        // shopping step
        $this->scenarioConfirm($client);

        $client->followRedirect();

        // Remove product (delete flg)
        $Product->setDelFlg(Constant::ENABLED);
        $ProductClass->setDelFlg(Constant::ENABLED);
        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // change payment
        $paymentForm = array(
            '_token' => 'dummy',
            'payment' => 4,
            'message' => $this->getFaker()->paragraph,
            'shippings' => array(
                array('delivery' => 1,),
            ),
        );
        $client->request('POST', $this->app->url('shopping_payment'), array('shopping' => $paymentForm));
        $client->followRedirect();
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product is private from shopping step change payment
     */
    public function testProductInCartIsPrivateFromShoppingChangePayment()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // change status
        $this->changeStatus($Product, Disp::DISPLAY_HIDE);

        // change payment
        $paymentForm = array(
            '_token' => 'dummy',
            'payment' => 4, // change payment
            'message' => $this->getFaker()->paragraph,
            'shippings' => array(
                array('delivery' => 1,),
            ),
        );
        $client->request('POST', $this->app->url('shopping_payment'), array('shopping' => $paymentForm));
        $client->followRedirect();
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product out of stock from shopping step change payment
     */
    public function testProductInCartOutOfStockFromShoppingChangePayment()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // change payment
        $paymentForm = array(
            '_token' => 'dummy',
            'payment' => 4, // change payment
            'message' => $this->getFaker()->paragraph,
            'shippings' => array(
                array('delivery' => 1,),
            ),
        );
        $client->request('POST', $this->app->url('shopping_payment'), array('shopping' => $paymentForm));
        $client->followRedirect();
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in cart when product stock not enough from shopping step change payment
     */
    public function testProductInCartStockNotEnoughFromShoppingChangePayment()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // change payment
        $paymentForm = array(
            '_token' => 'dummy',
            'payment' => 4, // change payment
            'message' => $this->getFaker()->paragraph,
            'shippings' => array(
                array('delivery' => 1,),
            ),
        );
        $client->request('POST', $this->app->url('shopping_payment'), array('shopping' => $paymentForm));

        // only one redirect (shopping 1)
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#confirm_flow_box__body .errormsg')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $stock;
        $this->verify('Cart item quantity is not enough!!');
    }

    /**
     * Test product in cart when product stock is limit from shopping step change payment
     */
    public function testProductInCartStockLimitFromShoppingChangePayment()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $crawler = $client->followRedirect();

        // sale limit
        $saleLimit = 1;
        $ProductClass->setSaleLimit($saleLimit);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // change payment
        $paymentForm = array(
            '_token' => 'dummy',
            'payment' => 4, // change payment
            'message' => $this->getFaker()->paragraph,
            'shippings' => array(
                array('delivery' => 1,),
            ),
        );
        $client->request('POST', $this->app->url('shopping_payment'), array('shopping' => $paymentForm));

        // only one redirect (shopping 1)
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);
      
        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $saleLimit;
        $this->verify('Cart item sale quantity has been limited!!');
    }

    /**
     * Test product in history order when product is deleting by order again function
     */
    public function testProductInHistoryOrderDeletedFromOrderAgain()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $this->scenarioCartIn($client, $productClassId);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->app->url('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);

        // Remove product (delete flg)
        $Product->setDelFlg(Constant::ENABLED);
        $ProductClass->setDelFlg(Constant::ENABLED);
        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, array('_token' => 'dummy'));
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in history order when product is private from order again function
     */
    public function testProductInHistoryOrderIsPrivateFromOrderAgain()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->app->url('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);

        // change status
        $this->changeStatus($Product, Disp::DISPLAY_HIDE);

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, array('_token' => 'dummy'));
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertContains('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in history order when product out of stock from order again funtion
     */
    public function testProductInHistoryOrderOutOfStockFromOrderAgain()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->app->url('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, array('_token' => 'dummy'));
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);

        // check cart
        $arrCartItem = $this->app['eccube.service.cart']->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in history order when product stock not enough from order again function
     */
    public function testProductInHistoryOrderStockNotEnoughFromOrderAgain()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->app->url('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, array('_token' => 'dummy'));
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $stock;
        $this->verify('Cart item quantity is not enough!!');
    }

    /**
     * Test product in history order when product stock is limit from order again function
     */
    public function testProductInHistoryOrderStockLimitFromOrderAgain()
    {
        // GIVE
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->app->url('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);

        // sale limit
        $saleLimit = 1;
        $ProductClass->setSaleLimit($saleLimit);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, array('_token' => 'dummy'));
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertContains('選択された商品('.$this->getProductName($ProductClass).')は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);

        // check cart
        $CartItem = $this->app['eccube.service.cart']->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $saleLimit;
        $this->verify('Cart item sale quantity has been limited!!');
    }

    /**
     * Test product in history order when product type is changed from order again function
     */
    public function testProductInHistoryOrderWhenProductTypeIsChangedFromOrderAgain()
    {
        // GIVE
        // disable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::DISABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        /* product 2 */
        $productName2 = $this->getFaker()->word;
        $Product2 = $this->createProduct($productName2, $productClassNum, $productStock);
        $ProductClass2 = $Product2->getProductClasses()->first();
        $productClassId2 = $ProductClass2->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);
        $this->app['eccube.service.cart']->unlock();
        $this->scenarioCartIn($client, $productClassId2, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->app->url('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);
        $this->assertContains($productName2, $product);

        // change type
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass2->setProductType($ProductType);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, array('_token' => 'dummy'));
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * Test product in history order when product type is changed from order again function
     * with MultiShipping
     * enable add cart
     */
    public function testProductInHistoryOrderWhenProductTypeIsChangedFromOrderAgainWithMultiShipping()
    {
        // GIVE
        // enable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        /* product 2 */
        $productName2 = $this->getFaker()->word;
        $Product2 = $this->createProduct($productName2, $productClassNum, $productStock);
        $ProductClass2 = $Product2->getProductClasses()->first();
        $productClassId2 = $ProductClass2->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);
        $this->app['eccube.service.cart']->unlock();
        $this->scenarioCartIn($client, $productClassId2, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->app->url('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);
        $this->assertContains($productName2, $product);

        // change type
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass2->setProductType($ProductType);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, array('_token' => 'dummy'));
        $crawler = $client->followRedirect();

        // THEN
        // check message error (expect not contain)
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertNotContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * Test product in history order when invalid payment method before confirm
     * with MultiShipping
     * enable add cart
     */
    public function testProductInHistoryOrderWhenInvalidPaymentMethodBeforeConfirm()
    {
        // GIVE
        // enable multi shipping
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();
        $this->logIn();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct(
          $productName,
          $productClassNum,
          $productStock
        );
        $ProductClass = $Product->getProductClasses()->first();
        $productClassId = $ProductClass->getId();

        /* product 2 */
        $productName2 = $this->getFaker()->word;
        $Product2 = $this->createProduct(
          $productName2,
          $productClassNum,
          $productStock
        );
        $ProductClass2 = $Product2->getProductClasses()->first();
        $productClassId2 = $ProductClass2->getId();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($client, $productClassId, $stockInCart);
        $this->app['eccube.service.cart']->unlock();
        $this->scenarioCartIn($client, $productClassId2, $stockInCart);

        // shopping step
        $this->scenarioConfirm($client);
        $client->followRedirect();

        // order complete, with invalid method payment
        $this->scenarioComplete($client, '', array(), 3);

        // error page
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_error')));
    }

    /**
     * @param $client
     * @param int $productClass
     * @param int $num
     * @return mixed
     */
    protected function scenarioCartIn($client, $productClass = 1, $num = 1)
    {
        $crawler = $client->request('POST', $this->app->url('cart_add'), array('product_class_id' => $productClass, 'quantity' => $num));
        $crawler = $client->followRedirect();
        $this->app['eccube.service.cart']->lock();

        return $crawler;
    }

    /**
     * @param $client
     * @return mixed
     */
    protected function scenarioConfirm($client)
    {
        $crawler = $client->request('GET', $this->app->url('cart_buystep'));

        return $crawler;
    }

    /**
     * @param $client
     * @param string $confirmUrl
     * @param array $arrShopping
     * @param int $payment
     * @return mixed
     */
    protected function scenarioComplete($client, $confirmUrl = '', $arrShopping = array(), $payment = 1)
    {
        $faker = $this->getFaker();
        if (strlen($confirmUrl) == 0) {
            $confirmUrl = $this->app->url('shopping_confirm');
        }

        if (count($arrShopping) == 0) {
            $arrShopping = array(
                'shippings' =>
                    array(
                        array(
                            'delivery' => 1,
                            'deliveryTime' => 1
                        ),
                    ),
                'payment' => $payment,
                'message' => $faker->text(),
                '_token' => 'dummy',
            );
        }
        $crawler = $client->request(
            'POST',
            $confirmUrl,
            array('shopping' => $arrShopping)
        );

        return $crawler;
    }

    /**
     * @param $client
     * @param $productClassId
     * @return mixed
     */
    protected function scenarioCartUp($client, $productClassId = 1)
    {
        $crawler = $client->request('PUT', $this->app->url('cart_up', array('productClassId' => $productClassId)));

        return $crawler;
    }

    /**
     * @param $client
     * @param $productClassId
     * @return mixed
     */
    protected function scenarioCartDown($client, $productClassId = 1)
    {
        $crawler = $client->request('PUT', $this->app->url('cart_down', array('productClassId' => $productClassId)));

        return $crawler;
    }

    /**
     * @param Product $Product
     * @param int     $display
     * @return Product
     */
    protected function changeStatus(Product $Product, $display = Disp::DISPLAY_SHOW)
    {
        $Disp = $this->app['eccube.repository.master.disp']->find($display);
        $Product->setStatus($Disp);

        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->flush();

        return $Product;
    }

    /**
     * @param ProductClass $ProductClass
     * @param int          $stock
     * @return ProductClass
     */
    protected function changeStock(ProductClass $ProductClass, $stock = 0)
    {
        $ProductClass->setStock($stock);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        return $ProductClass;
    }

    /**
     * Delete all product
     */
    protected function deleteAllProduct()
    {
        // remove product exist
        $pdo = $this->app['orm.em']->getConnection()->getWrappedConnection();
        $sql = 'DELETE FROM dtb_tax_rule WHERE dtb_tax_rule.tax_rule_id <> 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $this->deleteAllRows(array(
            'dtb_order_detail',
            'dtb_shipment_item',
            'dtb_product_stock',
            'dtb_product_class',
            'dtb_product_image',
            'dtb_product_category',
            'dtb_customer_favorite_product',
            'dtb_product',
        ));
    }

    /**
     * @param null $productName
     * @param int  $productClassNum
     * @param int  $stock
     * @return \Eccube\Entity\Product
     */
    public function createProduct($productName = null, $productClassNum = 3, $stock = 0)
    {
        $Product = parent::createProduct($productName, $productClassNum);
        $ProductClass = $Product->getProductClasses()->first();

        $this->changeStock($ProductClass, $stock);

        return $Product;
    }

    /**
     * エラーに表示する商品名を取得
     *
     * @param ProductClass $ProductClass
     * @return string
     */
    private function getProductName(ProductClass $ProductClass)
    {

        $productName = $ProductClass->getProduct()->getName();

        if ($ProductClass->hasClassCategory1()) {
            $productName .= ' - '.$ProductClass->getClassCategory1()->getName();
        }

        if ($ProductClass->hasClassCategory2()) {
            $productName .= ' - '.$ProductClass->getClassCategory2()->getName();
        }

        return $productName;
    }
}
