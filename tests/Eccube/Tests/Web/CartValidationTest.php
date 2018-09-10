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

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Service\CartService;
use Symfony\Component\HttpKernel\Client;

class CartValidationTest extends AbstractWebTestCase
{
    /** @var ProductStatusRepository */
    private $productStatusRepository;

    /** @var CartService */
    private $cartService;

    /** @var BaseInfo */
    private $BaseInfo;

    /**
     * setup mail
     */
    public function setUp()
    {
        parent::setUp();
        $this->productStatusRepository = $this->container->get(ProductStatusRepository::class);
        $this->cartService = $this->container->get(CartService::class);
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
    }

    /**
     * tear down
     */
    public function tearDown()
    {
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

        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->get(1);

        // 在庫数を設定
        $ProductClass->setStock(1);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        /** @var Client $client */
        $client = $this->client;

        $client->request(
            'GET',
            $this->generateUrl('product_detail', ['id' => $Product->getId()])
        );

        $form = [
            'ProductClass' => $ProductClass->getId(),
            'quantity' => 9999,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];
        if ($ProductClass->hasClassCategory1()) {
            $form['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $form['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $this->client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $Product->getId()]),
            $form
        );

        $crawler = $this->client->followRedirect();

        // エラーメッセージは改行されているため2回に分けてチェック

        $message = $crawler->filter('.ec-cartRole__error')->text();

        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);

        $this->assertContains('一度に在庫数を超える購入はできません。', $message);
    }

    /**
     * Test product in cart when product is deleting.
     */
    public function testProductInCartDeleted()
    {
        /** @var Product $Product */
        $Product = $this->createProduct('test', 1, 1);

        $productClassId = $Product->getProductClasses()->first()->getId();
        $productId = $Product->getId();

        $arrForm = [
            'ProductClass' => $productClassId,
            'quantity' => 1,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];

        // render
        $this->client->request(
            'GET',
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // delete
        $this->deleteAllProduct();

        // submit
        $this->client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test product in cart when product is private.
     */
    public function testProductInCartIsPrivate()
    {
        /** @var Product $Product */
        $Product = $this->createProduct('test', 1, 1);

        $productClassId = $Product->getProductClasses()->first()->getId();
        $productId = $Product->getId();

        $arrForm = [
            'ProductClass' => $productClassId,
            'quantity' => 1,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];

        // render
        $this->client->request(
            'GET',
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // private
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // submit
        $this->client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test product in cart when product is stock out.
     *
     * @NOTE:
     * No stock hidden flg -> false
     */
    public function testProductInCartIsStockOut()
    {
        $this->markTestIncomplete('在庫がゼロの場合フォームエラーになってしまう');

        /** @var Product $Product */
        $Product = $this->createProduct('test', 0, 1);
        $ProductClass = $Product->getProductClasses()->first();

        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // Stock out
        $ProductClass->setStock(0);

        $this->entityManager->persist($ProductClass);
        $this->entityManager->persist($Product);
        $this->entityManager->flush();

        // submit
        $arrForm = [
            'ProductClass' => $productClassId,
            'quantity' => 1,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];

        $crawler = $client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        $html = $crawler->html();
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertContains('ただいま品切れ中です', $html);
    }

    /**
     * Test product in cart when product is stock out.
     *
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

        $this->entityManager->persist($ProductClass);
        $this->entityManager->persist($Product);
        $this->entityManager->flush();

        // render
        $client->request(
            'GET',
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // submit
        $arrForm = [
            'ProductClass' => $productClassId,
            'quantity' => 1,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $crawler = $client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );
        $crawler = $client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $message = $crawler->filter('.ec-cartRole')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
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
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // submit
        $arrForm = [
            'ProductClass' => $productClassId,
            'quantity' => $stock + 1,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        // check error message
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $message = $crawler->filter('.ec-alert-warning')->text();

        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);

        $this->assertContains('一度に在庫数を超える購入はできません。', $message);

        self::assertEquals($stock, $crawler->filter('.ec-cartRow__amount')->text(), '在庫数分だけカートに入っているはず');
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
            $this->generateUrl('product_detail', ['id' => $Product->getId()])
        );
        // submit
        $arrForm = [
            'ProductClass' => $ProductClass->getId(),
            'quantity' => 9,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $Product->getId()]),
            $arrForm
        );

        $stock = 2000000;
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, 1, 100);
        $ProductClass = $Product->getProductClasses()->first();

        $productId = $Product->getId();

        // render
        $client->request(
            'GET',
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // submit
        $arrForm = [
            'ProductClass' => $ProductClass->getId(),
            'quantity' => $stock,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $crawler = $client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        // check error message
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();
        $message = $crawler->filter('.ec-alert-warning__text')->text();
        // FIXME $this->assertContains('商品を購入できる金額の上限を超えております。数量を調整してください。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません', $message);

        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
    }

    /**
     * Test product in cart when product has other type
     */
    public function testProductInCartSaleType()
    {
        $this->markTestIncomplete('複数配送が実装されるまでスキップ');
        $this->entityManager->persist($this->BaseInfo);
        $this->entityManager->flush();

        // Stock
        $stock = 10;
        $productName = $this->getFaker()->word;
        /** @var Product $Product */
        $Product = $this->createProduct($productName, 1, $stock);
        $SaleType = $this->container->get(SaleTypeRepository::class)->find(2);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setSaleType($SaleType);
        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // submit product type 2
        $arrForm = [
            'ProductClass' => $productClassId,
            'quantity' => 1,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        // submit product type 1
        $arrForm = [
            'ProductClass' => 1,
            'classcategory_id1' => 3,
            'classcategory_id2' => 6,
            'quantity' => 1,
            '_token' => 'dummy',
        ];

        $client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => 1]),
            $arrForm
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $message = $crawler->filter('.ec-alert-warning')->text();
        $this->assertContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * Test product in cart when product has other type
     * with MultiShipping
     * enable add cart
     */
    public function testProductInCartSaleTypeWithMultiShipping()
    {
        $this->markTestIncomplete('複数配送が実装されるまでスキップ');
        $this->entityManager->persist($this->BaseInfo);
        $this->entityManager->flush();

        // Stock
        $stock = 10;
        $productName = $this->getFaker()->word;
        /** @var Product $Product */
        $Product = $this->createProduct($productName, 1, $stock);
        $SaleType = $this->entityManager->find(SaleType::class, 2);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setSaleType($SaleType);
        $productClassId = $ProductClass->getId();
        $productId = $Product->getId();

        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // submit product type 2
        $arrForm = [
            'product_id' => $productId,
            'mode' => 'add_cart',
            'product_class_id' => $productClassId,
            'quantity' => 1,
            '_token' => 'dummy',
        ];
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }

        $client->request(
            'POST',
            $this->generateUrl('product_detail', ['id' => $productId]),
            $arrForm
        );

        // submit product type 1
        $arrForm = [
            'product_id' => 1,
            'mode' => 'add_cart',
            'product_class_id' => 1,
            'classcategory_id1' => 3,
            'classcategory_id2' => 6,
            'quantity' => 1,
            '_token' => 'dummy',
        ];

        $client->request(
            'POST',
            $this->generateUrl('product_detail', ['id' => 1]),
            $arrForm
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        // expect not contain the error message
        $this->assertEmpty($crawler->filter('.ec-alert-warning'));
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
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            'GET',
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // submit
        $arrForm = [
            'ProductClass' => $productClassId,
            'quantity' => $limit + 1,
            'product_id' => $Product->getId(),
            '_token' => 'dummy',
        ];
        if ($ProductClass->hasClassCategory1()) {
            $arrForm['classcategory_id1'] = $ProductClass->getClassCategory1()->getId();
        }
        if ($ProductClass->hasClassCategory2()) {
            $arrForm['classcategory_id2'] = $ProductClass->getClassCategory2()->getId();
        }
        $client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $message = $crawler->filter('.ec-alert-warning')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);

        self::assertEquals($limit, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product is abolished by shopping step
     */
    public function testProductInCartIsAbolishedFromShopping()
    {
        $Customer = $this->createCustomer();
        $this->loginTo($Customer);

        /** @var Product $Product */
        $Product = $this->createProduct('test', 1, 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->get(0);

        // add to cart
        $this->scenarioCartIn($Customer, $ProductClass);

        // Abolish product
        $this->changeStatus($Product, ProductStatus::DISPLAY_ABOLISHED);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();

        $message = $crawler->filter('.ec-layoutRole__main')->text();

        $this->assertContains('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
    }

    /**
     * Test product in cart when product is private from shopping step
     */
    public function testProductInCartIsPrivateFromShopping()
    {
        $Customer = $this->createCustomer();
        /** @var Product $Product */
        $Product = $this->createProduct('test', 1, 1);
        /** @var ProductClass $productClass */
        $ProductClass = $Product->getProductClasses()->first();

        // add to cart
        $this->scenarioCartIn($Customer, $ProductClass);

        // change status
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        $this->scenarioConfirm($Customer, $ProductClass);

        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();

        $message = $crawler->filter('.ec-layoutRole__main')->text();

        $this->assertContains('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
    }

    /**
     * Test product in cart when product out of stock from shopping step
     */
    public function testProductInCartOutOfStockFromShopping()
    {
        $Customer = $this->createCustomer();

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, 1, 10);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        // add to cart
        $this->scenarioCartIn($Customer, $ProductClass);

        // change stock
        $this->changeStock($ProductClass, 0);

        $this->scenarioConfirm($Customer, $ProductClass);

        // two redirect???
        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();

        // check message error
        $message = $crawler->filter('.ec-layoutRole__main')->text();
        $this->assertContains('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
    }

    /**
     * Test product in cart when product stock not enough from shopping step
     */
    public function testProductInCartStockNotEnoughFromShopping()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change stock
        $currentStock = $stockInCart - 1;
        $this->changeStock($ProductClass, $currentStock);

        // レジへすすむボタンを押下
        $this->scenarioConfirm($Customer, $ProductClass);

        // 注文手続き画面へリダイレクト
        $crawler = $this->client->followRedirect();
        self::assertTrue($this->client->getResponse()->isSuccessful());

        // THEN
        // check message error
        // cart or shopping???
        $message = $crawler->filter('.ec-layoutRole__main')->text();

        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);
    }

    /**
     * Test product in cart when product stock is limit from shopping step
     */
    public function atestProductInCartStockLimitFromShopping()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;
        $limit = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = $limit + 1;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // Sale limit
        $ProductClass->setSaleLimit($limit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        $this->scenarioConfirm($Customer, $ProductClass);

        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        // cart or shopping???
        $message = $crawler->filter('.ec-layoutRole__main')->text();

        $this->assertContains('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);

        // check cart
        $this->assertContains((string) $limit, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product type change from shopping step
     */
    public function atestProductInCartSaleTypeFromShopping()
    {
        $this->markTestIncomplete('複数配送が実装されるまでスキップ');
        // GIVE
        $this->entityManager->persist($this->BaseInfo);
        $this->entityManager->flush();

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
        $Delivery = $this->entityManager->find(Delivery::class, 1);
        $this->entityManager->remove($Delivery);
        $this->entityManager->flush($Delivery);

        // shopping
        $crawler = $this->scenarioConfirm($client, $ProductClass);
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
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // Remove product (delete flg)
        $this->changeStatus($Product, ProductStatus::DISPLAY_ABOLISHED);

        // cart up
        $this->scenarioCartUp($Customer, $ProductClass);

        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('.ec-layoutRole__main')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product is private before plus one
     */
    public function testProductInCartIsPrivateBeforePlus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change status
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // cart up
        $this->scenarioCartUp($Customer, $ProductClass);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product out of stock before plus one
     */
    public function testProductInCartProductOutOfStockBeforePlus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // cart up
        $this->scenarioCartUp($Customer, $ProductClass);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product is not enough before plus one
     */
    public function testProductInCartProductStockIsNotEnoughBeforePlus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // cart up
        $this->scenarioCartUp($Customer, $ProductClass);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);
        $this->assertContains((string) $stock, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product sale limit is not enough before plus one
     */
    public function testProductInCartSaleLimitIsNotEnoughBeforePlus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // sale limit
        $saleLimit = 1;
        $ProductClass->setSaleLimit($saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // cart up
        $this->scenarioCartUp($Customer, $ProductClass);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);
        $this->assertContains((string) $saleLimit, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product type is changing before plus one
     */
    public function testProductInCartChangeSaleTypeBeforePlus()
    {
        $this->markTestIncomplete('複数配送対応するまでスキップ');
        // GIVE
        $this->entityManager->persist($this->BaseInfo);
        $this->entityManager->flush();

        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        // product 2
        $productName2 = $this->getFaker()->word;
        /** @var Product $Product2 */
        $Product2 = $this->createProduct($productName2, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass2 */
        $ProductClass2 = $Product2->getProductClasses()->first();

        // WHEN
        /** @var Client $client */
        $client = $this->client;

        // add to cart
        $stockInCart = 1;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);
        $this->scenarioCartIn($Customer, $ProductClass2, $stockInCart);

        // Change product type
        $SaleType = $this->entityManager->getRepository(SaleType::class)->find(2);
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        $ProductClass->setSaleType($SaleType);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // cart up
        $this->scenarioCartUp($Customer, $ProductClass);
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * Test product in cart when product type is changing before plus one
     * with MultiShipping
     * enable add cart
     */
    public function testProductInCartChangeSaleTypeBeforePlusWithMultiShipping()
    {
        $this->markTestIncomplete('複数配送対応するまでスキップ');
        // GIVE
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->entityManager->persist($BaseInfo);
        $this->entityManager->flush();

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
        $SaleType = $this->entityManager->find(SaleType::class, 2);
        $ProductClass->setSaleType($SaleType);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

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
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // Remove product (delete flg)
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // cart down
        $this->scenarioCartDown($Customer, $ProductClass);

        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product is private before Minus one
     */
    public function testProductInCartIsPrivateBeforeMinus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change status
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // cart down
        $this->scenarioCartDown($Customer, $ProductClass);

        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product out of stock before Minus one
     */
    public function testProductInCartProductOutOfStockBeforeMinus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // cart down
        $this->scenarioCartDown($Customer, $ProductClass);

        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product is not enough before Minus one
     */
    public function testProductInCartProductStockIsNotEnoughBeforeMinus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // cart down
        $this->scenarioCartDown($Customer, $ProductClass);

        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);
        $this->assertContains((string) $stock, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product sale limit is not enough before Minus one
     */
    public function testProductInCartSaleLimitIsNotEnoughBeforeMinus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // sale limit
        $saleLimit = 1;
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        $ProductClass->setSaleLimit($saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // cart down
        $this->scenarioCartDown($Customer, $ProductClass);

        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);
        $this->assertContains((string) $saleLimit, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product type is changing before Minus one
     */
    public function testProductInCartChangeSaleTypeBeforeMinus()
    {
        $this->markTestIncomplete('複数配送対応するまでスキップ');
        // GIVE
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->entityManager->persist($BaseInfo);
        $this->entityManager->flush();

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
        $SaleType = $this->entityManager->find(SaleType::class, 2);
        $ProductClass->setSaleType($SaleType);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

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
    public function testProductInCartChangeSaleTypeBeforeMinusWithMultiShipping()
    {
        $this->markTestIncomplete('複数配送対応するまでスキップ');
        // GIVE
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->entityManager->persist($BaseInfo);
        $this->entityManager->flush();

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
        $SaleType = $this->entityManager->find(SaleType::class, 2);
        $ProductClass->setSaleType($SaleType);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

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
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // Move to top
        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));

        // Remove product (delete flg)
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // move to cart
        $crawler = $this->client->request('GET', $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product is private on the top page
     */
    public function testProductInCartIsPrivateWhileReturnTopPage()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change status
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // move to cart
        $crawler = $this->client->request('GET', $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product out of stock on the top page
     */
    public function testProductInCartProductOutOfStockWhileReturnTopPage()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // move to cart
        $crawler = $this->client->request('GET', $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);
    }

    /**
     * Test product in cart when product is not enough before Minus one
     */
    public function testProductInCartProductStockIsNotEnoughWhileReturnTopPage()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // move to cart
        $crawler = $this->client->request('GET', $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);
        $this->assertContains((string) $stock, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product sale limit is not enough before Minus one
     */
    public function testProductInCartSaleLimitIsNotEnoughWhileReturnTopPage()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // Move to top
        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));

        // sale limit
        $saleLimit = 1;
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        $ProductClass->setSaleLimit($saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // move to cart
        $crawler = $this->client->request('GET', $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);
        $this->assertContains((string) $saleLimit, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product is deleting by shopping step back to cart
     */
    public function testProductInCartDeletedFromShoppingBackToCart()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // add to cart
        $this->scenarioCartIn($Customer, $ProductClass);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);

        $crawler = $this->client->followRedirect();

        // Remove product (delete flg)
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // back to cart
        $urlBackToCart = $crawler->filter('.ec-orderRole__summary .ec-blockBtn--cancel')->selectLink('カートに戻る')->link()->getUri();
        $crawler = $this->client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product is private from shopping step back to cart
     */
    public function testProductInCartIsPrivateFromShoppingBackToCart()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $crawler = $this->client->followRedirect();

        // change status
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // back to cart
        $urlBackToCart = $crawler->filter('.ec-orderRole__summary .ec-blockBtn--cancel')->selectLink('カートに戻る')->link()->getUri();
        $crawler = $this->client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product out of stock from shopping step back to cart
     */
    public function testProductInCartOutOfStockFromShoppingBackToCart()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $crawler = $this->client->followRedirect();

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // back to cart
        $urlBackToCart = $crawler->filter('.ec-orderRole__summary .ec-blockBtn--cancel')->selectLink('カートに戻る')->link()->getUri();
        $crawler = $this->client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertContains('該当商品をカートから削除しました。', $message);
        $this->assertContains('現在カート内に商品はございません。', $message);
    }

    /**
     * Test product in cart when product stock not enough from shopping step back to cart
     */
    public function testProductInCartStockNotEnoughFromShoppingBackToCart()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $crawler = $this->client->followRedirect();

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // back to cart
        $urlBackToCart = $crawler->filter('.ec-orderRole__summary .ec-blockBtn--cancel')->selectLink('カートに戻る')->link()->getUri();
        $crawler = $this->client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertContains('一度に在庫数を超える購入はできません。', $message);
        $this->assertContains((string) $stock, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product stock is limit from shopping step back to cart
     */
    public function testProductInCartStockLimitFromShoppingBackToCart()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $crawler = $this->client->followRedirect();

        // sale limit
        $saleLimit = 1;
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        $ProductClass->setSaleLimit($saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // back to cart
        $urlBackToCart = $crawler->filter('.ec-orderRole__summary .ec-blockBtn--cancel')->selectLink('カートに戻る')->link()->getUri();
        $crawler = $this->client->request('GET', $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);
        $this->assertContains((string) $saleLimit, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product is deleting by shopping step change payment
     */
    public function testProductInCartDeletedFromShoppingChangePayment()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // add to cart
        $this->scenarioCartIn($Customer, $ProductClass);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // Remove product (delete flg)
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // change payment
        $paymentForm = [
            '_token' => 'dummy',
            'Payment' => 4,
            'use_point' => 0,
            'message' => $this->getFaker()->paragraph,
            'Shippings' => [
                ['Delivery' => 1],
            ],
        ];
        $this->client->request('POST', $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_error')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('body')->text();
        $this->assertContains('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
    }

    /**
     * Test product in cart when product is private from shopping step change payment
     */
    public function testProductInCartIsPrivateFromShoppingChangePayment()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // change status
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // change payment
        $paymentForm = [
            '_token' => 'dummy',
            'Payment' => 4, // change payment
            'use_point' => 0,
            'message' => $this->getFaker()->paragraph,
            'Shippings' => [
                ['Delivery' => 1],
            ],
        ];
        $this->client->request('POST', $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_error')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('body')->text();
        $this->assertContains('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
    }

    /**
     * Test product in cart when product out of stock from shopping step change payment
     */
    public function testProductInCartOutOfStockFromShoppingChangePayment()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // change payment
        $paymentForm = [
            '_token' => 'dummy',
            'Payment' => 4, // change payment
            'use_point' => 0,
            'message' => $this->getFaker()->paragraph,
            'Shippings' => [
                ['Delivery' => 1],
            ],
        ];
        $this->client->request('POST', $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_error')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('body')->text();
        $this->assertContains('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
    }

    /**
     * Test product in cart when product stock not enough from shopping step change payment
     */
    public function testProductInCartStockNotEnoughFromShoppingChangePayment()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // change payment
        $paymentForm = [
            '_token' => 'dummy',
            'Payment' => 4, // change payment
            'use_point' => 0,
            'message' => $this->getFaker()->paragraph,
            'Shippings' => [
                ['Delivery' => 1],
            ],
        ];
        $this->client->request('POST', $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('.ec-layoutRole__main')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
    }

    /**
     * Test product in cart when product stock is limit from shopping step change payment
     */
    public function testProductInCartStockLimitFromShoppingChangePayment()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $crawler = $this->client->followRedirect();

        // sale limit
        $saleLimit = 1;
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        $ProductClass->setSaleLimit($saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // change payment
        $paymentForm = [
            '_token' => 'dummy',
            'Payment' => 4, // change payment
            'use_point' => 0,
            'message' => $this->getFaker()->paragraph,
            'Shippings' => [
                ['Delivery' => 1],
            ],
        ];
        $this->client->request('POST', $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertContains('一度に販売制限数を超える購入はできません。', $message);
    }

    /**
     * Test product in history order when product is deleting by order again function
     */
    public function testProductInHistoryOrderDeletedFromOrderAgain()
    {
        $this->markTestIncomplete('マイページ対応するまでスキップ');
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        /** @var Product $Product */
        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // add to cart
        $this->scenarioCartIn($Customer, $ProductClass);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // order complete
        $this->scenarioComplete($Customer);
        $this->client->followRedirect();

        // my page
        $crawler = $this->client->request('GET', $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('.ec-historyRole .ec-historyListHeader__action .ec-inlineBtn')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $this->client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);

        // Remove product (delete flg)
        $Product->setDelFlg(Constant::ENABLED);
        $ProductClass->setDelFlg(Constant::ENABLED);
        $this->entityManager->persist($Product);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $this->client->request('PUT', $orderLink, ['_token' => 'dummy']);
        $crawler = $this->client->followRedirect();

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
        $this->markTestIncomplete('マイページ対応するまでスキップ');
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
        $this->scenarioConfirm($client, $ProductClass);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);

        // change status
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, ['_token' => 'dummy']);
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
        $this->markTestIncomplete('マイページ対応するまでスキップ');
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
        $this->scenarioConfirm($client, $ProductClass);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->generateUrl('mypage'));
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
        $client->request('PUT', $orderLink, ['_token' => 'dummy']);
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
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
        $this->markTestIncomplete('マイページ対応するまでスキップ');
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
        $this->scenarioConfirm($client, $ProductClass);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->generateUrl('mypage'));
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
        $client->request('PUT', $orderLink, ['_token' => 'dummy']);
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
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
        $this->markTestIncomplete('マイページ対応するまでスキップ');
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
        $this->scenarioConfirm($client, $ProductClass);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->generateUrl('mypage'));
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
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, ['_token' => 'dummy']);
        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertContains('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
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
    public function testProductInHistoryOrderWhenSaleTypeIsChangedFromOrderAgain()
    {
        $this->markTestIncomplete('マイページ対応するまでスキップ');
        // GIVE
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->entityManager->persist($BaseInfo);
        $this->entityManager->flush();
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
        $this->scenarioConfirm($client, $ProductClass);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);
        $this->assertContains($productName2, $product);

        // change type
        $SaleType = $this->entityManager->find(SaleType::class, 2);
        $ProductClass2->setSaleType($SaleType);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, ['_token' => 'dummy']);
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
    public function testProductInHistoryOrderWhenSaleTypeIsChangedFromOrderAgainWithMultiShipping()
    {
        $this->markTestIncomplete('マイページ対応するまでスキップ');
        // GIVE
        $this->entityManager->persist($this->BaseInfo);
        $this->entityManager->flush();
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
        $this->scenarioConfirm($client, $ProductClass);
        $client->followRedirect();

        // order complete
        $this->scenarioComplete($client);
        $client->followRedirect();

        // my page
        $crawler = $client->request('GET', $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $client->request('GET', $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertContains($productName, $product);
        $this->assertContains($productName2, $product);

        // change type
        $SaleType = $this->entityManager->find(SaleType::class, 2);
        $ProductClass2->setSaleType($SaleType);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $client->request('PUT', $orderLink, ['_token' => 'dummy']);
        $crawler = $client->followRedirect();

        // THEN
        // check message error (expect not contain)
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertNotContains('この商品は同時に購入することはできません。', $message);
    }

    /**
     * @param Customer $Customer
     * @param ProductClass $ProductClass
     * @param int $num
     *
     * @return mixed
     */
    protected function scenarioCartIn(Customer $Customer, ProductClass $ProductClass, $num = 1)
    {
        $this->loginTo($Customer);

        return $this->client->request(
            'POST',
            $this->generateUrl('product_add_cart', ['id' => $ProductClass->getProduct()->getId()]),
            [
                'ProductClass' => $ProductClass->getId(),
                'quantity' => $num,
                'product_id' => $ProductClass->getProduct()->getId(),
                '_token' => 'dummy',
            ]
        );
    }

    /**
     * @param $client
     *
     * @return mixed
     */
    protected function scenarioConfirm(Customer $Customer, ProductClass $ProductClass)
    {
        $this->loginTo($Customer);
        $cart_key = $Customer->getId().'_'.$ProductClass->getSaleType()->getId();
        $crawler = $this->client->request('GET', $this->generateUrl('cart_buystep', ['cart_key' => $cart_key]));

        return $crawler;
    }

    /**
     * @param $Customer
     * @param string $confirmUrl
     * @param array $arrShopping
     *
     * @return mixed
     */
    protected function scenarioComplete(Customer $Customer, $confirmUrl = '', $arrShopping = [])
    {
        $faker = $this->getFaker();
        if (strlen($confirmUrl) == 0) {
            $confirmUrl = $this->generateUrl('shopping_order');
        }

        if (count($arrShopping) == 0) {
            $arrShopping = [
                'Shippings' => [
                        [
                            'Delivery' => 1,
                            'DeliveryTime' => 1,
                        ],
                    ],
                'Payment' => 3,
                'message' => $faker->realText(),
                '_token' => 'dummy',
            ];
        }
        $this->loginTo($Customer);
        $crawler = $this->client->request(
            'POST',
            $confirmUrl,
            ['_shopping_order' => $arrShopping]
        );

        return $crawler;
    }

    /**
     * @param $client
     * @param $productClassId
     *
     * @return mixed
     */
    protected function scenarioCartUp(Customer $Customer, ProductClass $ProductClass)
    {
        $this->loginTo($Customer);

        return $this->client->request('PUT', $this->generateUrl('cart_handle_item', [
            'operation' => 'up',
            'productClassId' => $ProductClass->getId(),
        ]));
    }

    /**
     * @param Customer $Customer
     * @param ProductClass $ProductClass
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function scenarioCartDown(Customer $Customer, ProductClass $ProductClass)
    {
        $this->loginTo($Customer);

        return $this->client->request('PUT', $this->generateUrl('cart_handle_item', [
            'operation' => 'down',
            'productClassId' => $ProductClass->getId(),
        ]));
    }

    /**
     * @param Product $Product
     * @param int     $display
     *
     * @return Product
     */
    protected function changeStatus(Product $Product, $display = ProductStatus::DISPLAY_SHOW)
    {
        $Product = $this->entityManager->find(Product::class, $Product->getId());
        $ProductStatus = $this->productStatusRepository->find($display);
        $Product->setStatus($ProductStatus);

        $this->entityManager->persist($Product);
        $this->entityManager->flush();

        return $Product;
    }

    /**
     * @param ProductClass $ProductClass
     * @param int          $stock
     *
     * @return ProductClass
     */
    protected function changeStock(ProductClass $ProductClass, $stock = 0)
    {
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        $ProductClass->setStock($stock);

        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        return $ProductClass;
    }

    /**
     * Delete all product
     */
    protected function deleteAllProduct()
    {
        // remove product exist
        $pdo = $this->entityManager->getConnection()->getWrappedConnection();
        $sql = 'DELETE FROM dtb_tax_rule WHERE dtb_tax_rule.id <> 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $this->deleteAllRows([
            'dtb_order_item',
            'dtb_product_stock',
            'dtb_product_class',
            'dtb_product_image',
            'dtb_product_category',
            'dtb_customer_favorite_product',
            'dtb_product',
        ]);
    }

    /**
     * @param null $productName
     * @param int  $productClassNum
     * @param int  $stock
     *
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
     *
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
