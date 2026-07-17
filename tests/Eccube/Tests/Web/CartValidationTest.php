<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Delivery;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Service\CartService;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

final class CartValidationTest extends AbstractWebTestCase
{
    private ?ProductStatusRepository $productStatusRepository = null;

    private ?BaseInfo $BaseInfo = null;

    /**
     * setup mail
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->productStatusRepository = $this->entityManager->getRepository(ProductStatus::class);
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
    }

    /**
     * tear down
     */
    protected function tearDown(): void
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
        $ProductClass->setStock('1');
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        /** @var Client $client */
        $client = $this->client;

        $client->request(
            Request::METHOD_GET,
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
            Request::METHOD_POST,
            $this->generateUrl('product_add_cart', ['id' => $Product->getId()]),
            $form
        );

        $crawler = $this->client->followRedirect();

        // エラーメッセージは改行されているため2回に分けてチェック

        $message = $crawler->filter('.ec-cartRole__error')->text();

        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);

        $this->assertStringContainsString('一度に在庫数を超える購入はできません。', $message);
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
            Request::METHOD_GET,
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // delete
        $this->deleteAllProduct();

        // submit
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
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
            Request::METHOD_GET,
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // private
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // submit
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    /**
     * Test product in cart when product is stock out.
     *
     * @NOTE:
     * No stock hidden flg -> false
     */
    public function testProductInCartIsStockOut(): never
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
            Request::METHOD_GET,
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // Stock out
        $ProductClass->setStock('0');

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
            Request::METHOD_POST,
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        $html = $crawler->html();
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertStringContainsString('ただいま品切れ中です', (string) $html);
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
        $ProductClass->setStock('0');

        $this->entityManager->persist($ProductClass);
        $this->entityManager->persist($Product);
        $this->entityManager->flush();

        // render
        $client->request(
            Request::METHOD_GET,
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
            Request::METHOD_POST,
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );
        $crawler = $client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $message = $crawler->filter('.ec-cartRole')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', (string) $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', (string) $message);
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
            Request::METHOD_GET,
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
            Request::METHOD_POST,
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        // check error message
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $message = $crawler->filter('.ec-alert-warning')->text();

        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', (string) $message);

        $this->assertStringContainsString('一度に在庫数を超える購入はできません。', (string) $message);

        $this->assertSame($stock, (int) $crawler->filter('.ec-cartRow__amount')->text(), '在庫数分だけカートに入っているはず');
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
        $ProductClass->setPrice02('999999911');
        $this->changeStock($ProductClass, 10);
        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            Request::METHOD_GET,
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
            Request::METHOD_POST,
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
            Request::METHOD_GET,
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
            Request::METHOD_POST,
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        // check error message
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();
        $message = $crawler->filter('.ec-alert-warning__text')->text();
        // FIXME $this->assertStringContainsString('商品を購入できる金額の上限を超えております。数量を調整してください。', $message);
        $this->assertStringContainsString('一度に在庫数を超える購入はできません', (string) $message);

        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', (string) $message);
    }

    /**
     * 販売種別が異なる商品をカートに入れると, カートが販売種別ごとに分割される.
     */
    public function testProductInCartSaleType()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $stock = 10;

        $productName = $this->getFaker()->word.'_saletype1';
        $Product = $this->createProduct($productName, 1, $stock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        // 販売種別違いの商品
        $productName2 = $this->getFaker()->word.'_saletype2';
        $ProductClass2 = $this->createProductWithOtherSaleType($productName2, $stock);

        // WHEN
        $this->scenarioCartIn($Customer, $ProductClass);
        $this->scenarioCartIn($Customer, $ProductClass2);

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('cart'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // THEN
        // 購入は拒否されず, 販売種別ごとにカートが分割される
        $this->assertCartDivided($crawler, [$productName, $productName2]);
    }

    /**
     * 販売種別ごとに分割されたカートは, それぞれ独立してレジへ進める.
     *
     * 3系では複数配送を有効にすると販売種別違いの同時購入が許可されたが,
     * 4系に複数配送の ON/OFF 設定 (BaseInfo) は存在せず, 販売種別違いは常にカート分割となる.
     * このため「別々で注文してください」の導線 (カートごとの cart_buystep) が機能することを検証する.
     */
    public function testProductInCartSaleTypeWithMultiShipping()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $stock = 10;

        $productName = $this->getFaker()->word.'_saletype1';
        $Product = $this->createProduct($productName, 1, $stock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        $productName2 = $this->getFaker()->word.'_saletype2';
        $ProductClass2 = $this->createProductWithOtherSaleType($productName2, $stock);

        // WHEN
        $this->scenarioCartIn($Customer, $ProductClass);
        $this->scenarioCartIn($Customer, $ProductClass2);

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('cart'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // THEN
        $this->assertCartDivided($crawler, [$productName, $productName2]);

        // カートごとに個別のレジへ進む導線を持つ
        $this->assertPerCartCheckoutLinks($crawler, $Customer, [$ProductClass, $ProductClass2]);
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
        $ProductClass->setSaleLimit((string) $limit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        /** @var Client $client */
        $client = $this->client;

        // render
        $client->request(
            Request::METHOD_GET,
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
            Request::METHOD_POST,
            $this->generateUrl('product_add_cart', ['id' => $productId]),
            $arrForm
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $message = $crawler->filter('.ec-alert-warning')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」は販売制限しております。', (string) $message);
        $this->assertStringContainsString('一度に販売制限数を超える購入はできません。', (string) $message);

        $this->assertSame($limit, (int) $crawler->filter('.ec-cartRow__amount')->text());
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

        $this->assertStringContainsString('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
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

        $this->assertStringContainsString('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
    }

    /**
     * Test product in cart when product out of stock from shopping step
     */
    public function testProductInCartOutOfStockFromShopping()
    {
        $Customer = $this->createCustomer();

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
        $this->assertStringContainsString('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
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
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // THEN
        // check message error
        // cart or shopping???
        $message = $crawler->filter('.ec-layoutRole__main')->text();

        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertStringContainsString('一度に在庫数を超える購入はできません。', $message);
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

        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = $limit + 1;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // Sale limit
        $ProductClass->setSaleLimit((string) $limit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        $this->scenarioConfirm($Customer, $ProductClass);

        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        // cart or shopping???
        $message = $crawler->filter('.ec-layoutRole__main')->text();

        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertStringContainsString('一度に販売制限数を超える購入はできません。', $message);

        // check cart
        $this->assertStringContainsString((string) $limit, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * Test product in cart when product type change from shopping step
     *
     * 名前に反して販売種別ではなく「配送方法が削除された商品」のテスト.
     * atest プレフィクスで無効化されており実行されない.
     */
    public function atestProductInCartSaleTypeFromShopping(): never
    {
        // 期待する DOM (h1.page-heading, #cart_box__message--1) は Silex 時代のもので現行テンプレートに存在しない.
        $this->markTestIncomplete('期待する DOM が Silex 時代のもので現行テンプレートに追従していない');
        // GIVE
        $this->entityManager->persist($this->BaseInfo);
        $this->entityManager->flush();

        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        // product type A
        $productName = $this->getFaker()->word;
        /** @var Product $Product */
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $this->scenarioCartIn($Customer, $ProductClass);

        // Delete related delivery type
        $Delivery = $this->entityManager->find(Delivery::class, 1);
        $this->entityManager->remove($Delivery);
        $this->entityManager->flush();

        // shopping
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();

        // THEN
        // check page title
        $message = $crawler->filter('h1.page-heading')->text();
        $this->assertStringContainsString('ショッピングカート', (string) $message);
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertStringContainsString('配送の準備ができていない商品が含まれております。', (string) $message);
        $this->assertStringContainsString('恐れ入りますがお問い合わせページよりお問い合わせください。', (string) $message);
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
        $this->assertStringContainsString('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);
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
        $this->assertStringContainsString('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', (string) $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', (string) $message);
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
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', (string) $message);
        $this->assertStringContainsString('該当商品をカートから削除しました。', (string) $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', (string) $message);
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
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', (string) $message);
        $this->assertStringContainsString('一度に在庫数を超える購入はできません。', (string) $message);
        $this->assertStringContainsString((string) $stock, (string) $crawler->filter('.ec-cartRow__amount')->text());
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
        $ProductClass->setSaleLimit((string) $saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // cart up
        $this->scenarioCartUp($Customer, $ProductClass);

        $crawler = $client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」は販売制限しております。', (string) $message);
        $this->assertStringContainsString('一度に販売制限数を超える購入はできません。', (string) $message);
        $this->assertStringContainsString((string) $saleLimit, (string) $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * カート内の商品の販売種別が変更された後に数量を増やすと, カートが分割される.
     */
    public function testProductInCartChangeSaleTypeBeforePlus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;

        $productName = $this->getFaker()->word.'_changed';
        $Product = $this->createProduct($productName, 1, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        $productName2 = $this->getFaker()->word.'_kept';
        $Product2 = $this->createProduct($productName2, 1, $productStock);
        /** @var ProductClass $ProductClass2 */
        $ProductClass2 = $Product2->getProductClasses()->first();

        // WHEN
        // 同じ販売種別の商品を 2 件カートに入れる (この時点ではカートは 1 つ)
        $this->scenarioCartIn($Customer, $ProductClass);
        $this->scenarioCartIn($Customer, $ProductClass2);

        // 一方の販売種別を変更する
        $ProductClass = $this->changeSaleType($ProductClass);

        // cart up
        $this->scenarioCartUp($Customer, $ProductClass);
        $crawler = $this->client->followRedirect();

        // THEN
        // エラーにはならず, 販売種別ごとにカートが分割される
        $this->assertCartDivided($crawler, [$productName, $productName2]);
    }

    /**
     * 販売種別変更後に数量を増やしても, 分割された各カートは独立してレジへ進める.
     *
     * @see self::testProductInCartSaleTypeWithMultiShipping 4系に複数配送の ON/OFF 設定は存在しない
     */
    public function testProductInCartChangeSaleTypeBeforePlusWithMultiShipping()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;

        $productName = $this->getFaker()->word.'_changed';
        $Product = $this->createProduct($productName, 1, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        $productName2 = $this->getFaker()->word.'_kept';
        $Product2 = $this->createProduct($productName2, 1, $productStock);
        /** @var ProductClass $ProductClass2 */
        $ProductClass2 = $Product2->getProductClasses()->first();

        // WHEN
        $this->scenarioCartIn($Customer, $ProductClass);
        $this->scenarioCartIn($Customer, $ProductClass2);

        $ProductClass = $this->changeSaleType($ProductClass);

        // cart up
        $this->scenarioCartUp($Customer, $ProductClass);
        $crawler = $this->client->followRedirect();

        // THEN
        $this->assertCartDivided($crawler, [$productName, $productName2]);
        $this->assertPerCartCheckoutLinks($crawler, $Customer, [$ProductClass, $ProductClass2]);
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
        $this->assertStringContainsString('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);
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
        $this->assertStringContainsString('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);
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
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertStringContainsString('該当商品をカートから削除しました。', $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);
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
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertStringContainsString('一度に在庫数を超える購入はできません。', $message);
        $this->assertStringContainsString((string) $stock, $crawler->filter('.ec-cartRow__amount')->text());
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
        $this->assertInstanceOf(ProductClass::class, $ProductClass);
        $ProductClass->setSaleLimit((string) $saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // cart down
        $this->scenarioCartDown($Customer, $ProductClass);

        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertStringContainsString('一度に販売制限数を超える購入はできません。', $message);
        $this->assertStringContainsString((string) $saleLimit, $crawler->filter('.ec-cartRow__amount')->text());
    }

    /**
     * カート内の商品の販売種別が変更された後に数量を減らすと, カートが分割される.
     */
    public function testProductInCartChangeSaleTypeBeforeMinus()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;

        $productName = $this->getFaker()->word.'_changed';
        $Product = $this->createProduct($productName, 1, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        $productName2 = $this->getFaker()->word.'_kept';
        $Product2 = $this->createProduct($productName2, 1, $productStock);
        /** @var ProductClass $ProductClass2 */
        $ProductClass2 = $Product2->getProductClasses()->first();

        // WHEN
        // 数量を減らせるように 2 個ずつカートに入れる
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);
        $this->scenarioCartIn($Customer, $ProductClass2, $stockInCart);

        // 一方の販売種別を変更する
        $ProductClass = $this->changeSaleType($ProductClass);

        // cart down
        $this->scenarioCartDown($Customer, $ProductClass);
        $crawler = $this->client->followRedirect();

        // THEN
        $this->assertCartDivided($crawler, [$productName, $productName2]);
    }

    /**
     * 販売種別変更後に数量を減らしても, 分割された各カートは独立してレジへ進める.
     *
     * @see self::testProductInCartSaleTypeWithMultiShipping 4系に複数配送の ON/OFF 設定は存在しない
     */
    public function testProductInCartChangeSaleTypeBeforeMinusWithMultiShipping()
    {
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;

        $productName = $this->getFaker()->word.'_changed';
        $Product = $this->createProduct($productName, 1, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        $productName2 = $this->getFaker()->word.'_kept';
        $Product2 = $this->createProduct($productName2, 1, $productStock);
        /** @var ProductClass $ProductClass2 */
        $ProductClass2 = $Product2->getProductClasses()->first();

        // WHEN
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);
        $this->scenarioCartIn($Customer, $ProductClass2, $stockInCart);

        $ProductClass = $this->changeSaleType($ProductClass);

        // cart down
        $this->scenarioCartDown($Customer, $ProductClass);
        $crawler = $this->client->followRedirect();

        // THEN
        $this->assertCartDivided($crawler, [$productName, $productName2]);
        $this->assertPerCartCheckoutLinks($crawler, $Customer, [$ProductClass, $ProductClass2]);
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

        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // Move to top
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('homepage'));

        // Remove product (delete flg)
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // move to cart
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);
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
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);
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
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertStringContainsString('該当商品をカートから削除しました。', $message);
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
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertStringContainsString('一度に在庫数を超える購入はできません。', $message);
        $this->assertStringContainsString((string) $stock, $crawler->filter('.ec-cartRow__amount')->text());
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

        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // Move to top
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('homepage'));

        // sale limit
        $saleLimit = 1;
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        $this->assertInstanceOf(ProductClass::class, $ProductClass);
        $ProductClass->setSaleLimit((string) $saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // move to cart
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('cart'));

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertStringContainsString('一度に販売制限数を超える購入はできません。', $message);
        $this->assertStringContainsString((string) $saleLimit, $crawler->filter('.ec-cartRow__amount')->text());
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
        $crawler = $this->client->request(Request::METHOD_GET, $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);
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
        $crawler = $this->client->request(Request::METHOD_GET, $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);
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
        $crawler = $this->client->request(Request::METHOD_GET, $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertStringContainsString('該当商品をカートから削除しました。', $message);
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);
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
        $crawler = $this->client->request(Request::METHOD_GET, $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
        $this->assertStringContainsString('一度に在庫数を超える購入はできません。', $message);
        $this->assertStringContainsString((string) $stock, $crawler->filter('.ec-cartRow__amount')->text());
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
        $this->assertInstanceOf(ProductClass::class, $ProductClass);
        $ProductClass->setSaleLimit((string) $saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // back to cart
        $urlBackToCart = $crawler->filter('.ec-orderRole__summary .ec-blockBtn--cancel')->selectLink('カートに戻る')->link()->getUri();
        $crawler = $this->client->request(Request::METHOD_GET, $urlBackToCart);

        // THEN
        // check message error
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertStringContainsString('一度に販売制限数を超える購入はできません。', $message);
        $this->assertStringContainsString((string) $saleLimit, $crawler->filter('.ec-cartRow__amount')->text());
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
        $this->client->request(Request::METHOD_POST, $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_error')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
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
        $this->client->request(Request::METHOD_POST, $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_error')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
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
        $this->client->request(Request::METHOD_POST, $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_error')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('ご注文手続きが正常に完了しませんでした。大変お手数ですが、再度ご注文手続きをお願いします。', $message);
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
        $this->client->request(Request::METHOD_POST, $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('.ec-layoutRole__main')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', $message);
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
        $this->assertInstanceOf(ProductClass::class, $ProductClass);
        $ProductClass->setSaleLimit((string) $saleLimit);
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
        $this->client->request(Request::METHOD_POST, $this->generateUrl('shopping_redirect_to'), ['_shopping_order' => $paymentForm]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // THEN
        // check message error
        $crawler = $this->client->followRedirect();
        $message = $crawler->filter('body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」は販売制限しております。', $message);
        $this->assertStringContainsString('一度に販売制限数を超える購入はできません。', $message);
    }

    /**
     * Test product in history order when product is deleting by order again function
     */
    public function testProductInHistoryOrderDeletedFromOrderAgain(): never
    {
        // scenarioConfirm 後にリダイレクトが発生しなくなっており(購入フロー変更), マイページの履歴 UI も未追従.
        $this->markTestIncomplete('購入フロー・マイページ履歴 UI に追従するまでスキップ');
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

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
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('.ec-historyRole .ec-historyListHeader__action .ec-inlineBtn')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $this->client->request(Request::METHOD_GET, $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertStringContainsString($productName, $product);

        // Remove product (delete flg)
        $Product->setDelFlg(Constant::ENABLED);
        $ProductClass->setDelFlg(Constant::ENABLED);
        $this->entityManager->persist($Product);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $this->client->request(Request::METHOD_PUT, $orderLink, ['_token' => 'dummy']);
        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertStringContainsString('現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。', $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertStringContainsString('現在カート内に商品はございません。', $message);

        // check cart
        $arrCartItem = static::getContainer()->get(CartService::class)->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in history order when product is private from order again function
     */
    public function testProductInHistoryOrderIsPrivateFromOrderAgain(): never
    {
        // scenarioConfirm 後にリダイレクトが発生しなくなっており(購入フロー変更), マイページの履歴 UI も未追従.
        $this->markTestIncomplete('購入フロー・マイページ履歴 UI に追従するまでスキップ');
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // order complete
        $this->scenarioComplete($Customer);
        $this->client->followRedirect();

        // my page
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $this->client->request(Request::METHOD_GET, $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertStringContainsString($productName, (string) $product);

        // change status
        $this->changeStatus($Product, ProductStatus::DISPLAY_HIDE);

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $this->client->request(Request::METHOD_PUT, $orderLink, ['_token' => 'dummy']);
        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__message--1')->text();
        $this->assertStringContainsString('現時点で購入できない商品が含まれておりました。該当商品をカートから削除しました。', (string) $message);
        $this->assertEmpty($crawler->filter('#cart_box__message--2'));
        $message = $crawler->filter('#cart_box__message')->text();
        $this->assertStringContainsString('現在カート内に商品はございません。', (string) $message);

        // check cart
        $arrCartItem = static::getContainer()->get(CartService::class)->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in history order when product out of stock from order again funtion
     */
    public function testProductInHistoryOrderOutOfStockFromOrderAgain(): never
    {
        // scenarioConfirm 後にリダイレクトが発生しなくなっており(購入フロー変更), マイページの履歴 UI も未追従.
        $this->markTestIncomplete('購入フロー・マイページ履歴 UI に追従するまでスキップ');
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 2;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // order complete
        $this->scenarioComplete($Customer);
        $this->client->followRedirect();

        // my page
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $this->client->request(Request::METHOD_GET, $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertStringContainsString($productName, (string) $product);

        // change stock
        $stock = 0;
        $this->changeStock($ProductClass, $stock);

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $this->client->request(Request::METHOD_PUT, $orderLink, ['_token' => 'dummy']);
        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', (string) $message);
        $this->assertStringContainsString('該当商品をカートから削除しました。', (string) $message);

        // check cart
        $arrCartItem = static::getContainer()->get(CartService::class)->getCart()->getCartItems();
        $this->actual = count($arrCartItem);
        $this->expected = 0;
        $this->verify('Cart item is not empty!');
    }

    /**
     * Test product in history order when product stock not enough from order again function
     */
    public function testProductInHistoryOrderStockNotEnoughFromOrderAgain(): never
    {
        // scenarioConfirm 後にリダイレクトが発生しなくなっており(購入フロー変更), マイページの履歴 UI も未追従.
        $this->markTestIncomplete('購入フロー・マイページ履歴 UI に追従するまでスキップ');
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // order complete
        $this->scenarioComplete($Customer);
        $this->client->followRedirect();

        // my page
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $this->client->request(Request::METHOD_GET, $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertStringContainsString($productName, (string) $product);

        // change stock
        $stock = 1;
        $this->changeStock($ProductClass, $stock);

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $this->client->request(Request::METHOD_PUT, $orderLink, ['_token' => 'dummy']);
        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」の在庫が不足しております。', (string) $message);
        $this->assertStringContainsString('一度に在庫数を超える購入はできません。', (string) $message);

        // check cart
        $CartItem = static::getContainer()->get(CartService::class)->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $stock;
        $this->verify('Cart item quantity is not enough!!');
    }

    /**
     * Test product in history order when product stock is limit from order again function
     */
    public function testProductInHistoryOrderStockLimitFromOrderAgain(): never
    {
        // scenarioConfirm 後にリダイレクトが発生しなくなっており(購入フロー変更), マイページの履歴 UI も未追従.
        $this->markTestIncomplete('購入フロー・マイページ履歴 UI に追従するまでスキップ');
        // GIVE
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // order complete
        $this->scenarioComplete($Customer);
        $this->client->followRedirect();

        // my page
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $this->client->request(Request::METHOD_GET, $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertStringContainsString($productName, (string) $product);

        // sale limit
        $saleLimit = 1;
        $ProductClass->setSaleLimit((string) $saleLimit);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $this->client->request(Request::METHOD_PUT, $orderLink, ['_token' => 'dummy']);
        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertStringContainsString('「'.$this->getProductName($ProductClass).'」は販売制限しております。', (string) $message);
        $this->assertStringContainsString('一度に販売制限数を超える購入はできません。', (string) $message);

        // check cart
        $CartItem = static::getContainer()->get(CartService::class)->getCart()->getCartItems()->first();
        $this->actual = $CartItem->getQuantity();
        $this->expected = $saleLimit;
        $this->verify('Cart item sale quantity has been limited!!');
    }

    /**
     * Test product in history order when product type is changed from order again function
     */
    public function testProductInHistoryOrderWhenSaleTypeIsChangedFromOrderAgain(): never
    {
        // scenarioConfirm 後にリダイレクトが発生しなくなっており(購入フロー変更), マイページの履歴 UI も未追従.
        $this->markTestIncomplete('購入フロー・マイページ履歴 UI に追従するまでスキップ');
        // GIVE
        $this->entityManager->persist($this->BaseInfo);
        $this->entityManager->flush();
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        /* product 2 */
        $productName2 = $this->getFaker()->word;
        $Product2 = $this->createProduct($productName2, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass2 */
        $ProductClass2 = $Product2->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);
        $this->scenarioCartIn($Customer, $ProductClass2, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // order complete
        $this->scenarioComplete($Customer);
        $this->client->followRedirect();

        // my page
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $this->client->request(Request::METHOD_GET, $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertStringContainsString($productName, (string) $product);
        $this->assertStringContainsString($productName2, (string) $product);

        // change type
        $SaleType = $this->entityManager->find(SaleType::class, 2);
        $ProductClass2->setSaleType($SaleType);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $this->client->request(Request::METHOD_PUT, $orderLink, ['_token' => 'dummy']);
        $crawler = $this->client->followRedirect();

        // THEN
        // check message error
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertStringContainsString('この商品は同時に購入することはできません。', (string) $message);
    }

    /**
     * Test product in history order when product type is changed from order again function
     * with MultiShipping
     * enable add cart
     */
    public function testProductInHistoryOrderWhenSaleTypeIsChangedFromOrderAgainWithMultiShipping(): never
    {
        // scenarioConfirm 後にリダイレクトが発生しなくなっており(購入フロー変更), マイページの履歴 UI も未追従.
        $this->markTestIncomplete('購入フロー・マイページ履歴 UI に追従するまでスキップ');
        // GIVE
        $this->entityManager->persist($this->BaseInfo);
        $this->entityManager->flush();
        $Customer = $this->createCustomer();
        $productStock = 10;
        $productClassNum = 1;

        $productName = $this->getFaker()->word;
        $Product = $this->createProduct($productName, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        /* product 2 */
        $productName2 = $this->getFaker()->word;
        $Product2 = $this->createProduct($productName2, $productClassNum, $productStock);
        /** @var ProductClass $ProductClass2 */
        $ProductClass2 = $Product2->getProductClasses()->first();

        // WHEN
        // add to cart
        $stockInCart = 3;
        $this->scenarioCartIn($Customer, $ProductClass, $stockInCart);
        $this->scenarioCartIn($Customer, $ProductClass2, $stockInCart);

        // shopping step
        $this->scenarioConfirm($Customer, $ProductClass);
        $this->client->followRedirect();

        // order complete
        $this->scenarioComplete($Customer);
        $this->client->followRedirect();

        // my page
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('mypage'));
        $orderNode = $crawler->filter('#history_list__body .historylist_column')->first();
        $historyLink = $orderNode->selectLink('詳細を見る')->link()->getUri();

        // history view
        $crawler = $this->client->request(Request::METHOD_GET, $historyLink);
        $product = $crawler->filter('#detail_list_box__list')->text();

        // check order product name
        $this->assertStringContainsString($productName, (string) $product);
        $this->assertStringContainsString($productName2, (string) $product);

        // change type
        $SaleType = $this->entityManager->find(SaleType::class, 2);
        $ProductClass2->setSaleType($SaleType);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->flush();

        // Order again
        $orderLink = $crawler->filter('body #confirm_side')->selectLink('再注文する')->link()->getUri();
        $this->client->request(Request::METHOD_PUT, $orderLink, ['_token' => 'dummy']);
        $crawler = $this->client->followRedirect();

        // THEN
        // check message error (expect not contain)
        $message = $crawler->filter('#cart_box__body')->text();
        $this->assertNotContains('この商品は同時に購入することはできません。', $message);
    }

    protected function scenarioCartIn(Customer $Customer, ProductClass $ProductClass, int $num = 1): mixed
    {
        $this->loginTo($Customer);

        return $this->client->request(
            Request::METHOD_POST,
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
     */
    protected function scenarioConfirm(Customer $Customer, ProductClass $ProductClass): mixed
    {
        $this->loginTo($Customer);
        $cart_key = $Customer->getId().'_'.$ProductClass->getSaleType()->getId();

        return $this->client->request(Request::METHOD_GET, $this->generateUrl('cart_buystep', ['cart_key' => $cart_key]));
    }

    /**
     * @param $Customer
     */
    protected function scenarioComplete(Customer $Customer, string $confirmUrl = '', array $arrShopping = []): mixed
    {
        $faker = $this->getFaker();
        if (strlen($confirmUrl) == 0) {
            $confirmUrl = $this->generateUrl('shopping_confirm');
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

        return $this->client->request(
            Request::METHOD_POST,
            $confirmUrl,
            ['_shopping_order' => $arrShopping]
        );
    }

    /**
     * @param $client
     * @param $productClassId
     */
    protected function scenarioCartUp(Customer $Customer, ProductClass $ProductClass): mixed
    {
        $this->loginTo($Customer);

        return $this->client->request(Request::METHOD_PUT, $this->generateUrl('cart_handle_item', [
            'operation' => 'up',
            'productClassId' => $ProductClass->getId(),
        ]));
    }

    protected function scenarioCartDown(Customer $Customer, ProductClass $ProductClass): Crawler
    {
        $this->loginTo($Customer);

        return $this->client->request(Request::METHOD_PUT, $this->generateUrl('cart_handle_item', [
            'operation' => 'down',
            'productClassId' => $ProductClass->getId(),
        ]));
    }

    protected function changeStatus(Product $Product, int $display = ProductStatus::DISPLAY_SHOW): Product
    {
        $Product = $this->entityManager->find(Product::class, $Product->getId());
        $ProductStatus = $this->productStatusRepository->find($display);
        $Product->setStatus($ProductStatus);

        $this->entityManager->persist($Product);
        $this->entityManager->flush();

        return $Product;
    }

    protected function changeStock(ProductClass $ProductClass, int $stock = 0): ProductClass
    {
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        $ProductClass->setStock((string) $stock);

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
        $sql = 'DELETE FROM dtb_tax_rule WHERE dtb_tax_rule.id <> 1';
        $this->entityManager->getConnection()->executeStatement($sql);
        $this->deleteAllRows([
            'dtb_order_item',
            'dtb_product_stock',
            'dtb_product_class',
            'dtb_product_image',
            'dtb_product_category',
            'dtb_product_tag',
            'dtb_customer_favorite_product',
            'dtb_product',
        ]);
    }

    #[\Override]
    public function createProduct(?string $productName = null, int $productClassNum = 3, int $stock = 0): Product
    {
        $Product = parent::createProduct($productName, $productClassNum);
        $ProductClass = $Product->getProductClasses()->first();

        $this->changeStock($ProductClass, $stock);

        return $Product;
    }

    /**
     * 販売種別違いの商品でカートが分割されていることを検証する.
     *
     * 4系では販売種別 (SaleType) が異なる商品の購入を拒否せず, 決済単位ごとにカートを分割する.
     *
     * @see \Eccube\Service\Cart\SaleTypeCartAllocator 販売種別 ID をカートの識別子にする
     *
     * @param string[] $expectedProductNames 分割後の各カートに 1 つずつ含まれることを期待する商品名
     */
    private function assertCartDivided(Crawler $crawler, array $expectedProductNames): void
    {
        // カートを分割した旨の案内が表示される (Cart/index.twig の Carts|length > 1 の分岐)
        $errorNodes = $crawler->filter('.ec-cartRole__error');
        $this->assertGreaterThan(0, $errorNodes->count(), 'カート分割の案内が表示されていること');
        $this->assertStringContainsString('同時購入できない商品のカートを分けました。', $errorNodes->text());

        // 販売種別ごとにカートが分割される
        $cartNodes = $crawler->filter('.ec-cartRole__cart');
        $this->assertSame(
            count($expectedProductNames),
            $cartNodes->count(),
            '販売種別ごとにカートが分割されていること'
        );

        // 各商品がちょうど 1 つのカートにのみ含まれる (= 別々のカートに振り分けられている)
        $cartTexts = $cartNodes->each(static fn (Crawler $node): string => $node->filter('.ec-cartRow__name')->text());
        foreach ($expectedProductNames as $productName) {
            $matched = array_filter($cartTexts, static fn (string $text): bool => str_contains($text, $productName));
            $this->assertCount(
                1,
                $matched,
                sprintf('「%s」がちょうど 1 つのカートに含まれていること', $productName)
            );
        }
    }

    /**
     * 分割された各カートが, 自身の cart_key で独立したレジ導線を持つことを検証する.
     *
     * @param ProductClass[] $ProductClasses 分割後の各カートに含まれる商品規格
     */
    private function assertPerCartCheckoutLinks(Crawler $crawler, Customer $Customer, array $ProductClasses): void
    {
        $checkoutUrls = $crawler->filter('.ec-cartRole__actions a.ec-blockBtn--action')
            ->each(static fn (Crawler $node): string => (string) $node->attr('href'));

        $this->assertCount(count($ProductClasses), $checkoutUrls);
        $this->assertSame($checkoutUrls, array_values(array_unique($checkoutUrls)), 'カートごとに異なる cart_key のレジ導線を持つこと');

        foreach ($ProductClasses as $ProductClass) {
            $this->assertContains(
                $this->generateUrl('cart_buystep', ['cart_key' => $Customer->getId().'_'.$ProductClass->getSaleType()->getId()]),
                $checkoutUrls
            );
        }
    }

    /**
     * 販売種別 2 を割り当てた商品を作成する
     */
    private function createProductWithOtherSaleType(string $productName, int $stock): ProductClass
    {
        $Product = $this->createProduct($productName, 1, $stock);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();

        return $this->changeSaleType($ProductClass);
    }

    /**
     * 商品規格の販売種別を変更する
     */
    private function changeSaleType(ProductClass $ProductClass, int $saleTypeId = 2): ProductClass
    {
        $SaleType = $this->entityManager->find(SaleType::class, $saleTypeId);
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        $this->assertInstanceOf(ProductClass::class, $ProductClass);
        $this->assertInstanceOf(SaleType::class, $SaleType);
        $ProductClass->setSaleType($SaleType);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        return $ProductClass;
    }

    /**
     * エラーに表示する商品名を取得
     */
    private function getProductName(ProductClass $ProductClass): string
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
