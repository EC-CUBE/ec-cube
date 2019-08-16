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

namespace Eccube\Tests\Web;

use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\ClassCategoryRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductControllerTest extends AbstractWebTestCase
{
    /**
     * @var BaseInfoRepository
     */
    private $baseInfoRepository;

    /**
     * @var ClassCategoryRepository
     */
    private $classCategoryRepository;

    public function setUp()
    {
        parent::setUp();
        $this->baseInfoRepository = $this->container->get(BaseInfoRepository::class);
        $this->classCategoryRepository = $this->container->get(ClassCategoryRepository::class);
    }

    public function testRoutingList()
    {
        $client = $this->client;
        $client->request('GET', $this->generateUrl('product_list'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingDetail()
    {
        $client = $this->client;
        $client->request('GET', $this->generateUrl('product_detail', ['id' => '1']));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingProductFavoriteAdd()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);

        $client = $this->client;
        $client->request('POST',
            $this->generateUrl('product_add_favorite', ['id' => '1'])
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('mypage_login')));
    }

    /**
     * test with category id is invalid.
     */
    public function testCategoryNotFound()
    {
        $client = $this->client;
        $message = 'ご指定のカテゴリは存在しません';
        $crawler = $client->request('GET', $this->generateUrl('product_list', ['category_id' => 'XXX']));
        $this->assertContains($message, $crawler->html());
    }

    /**
     * test with category id is valid.
     */
    public function testCategoryFound()
    {
        $client = $this->client;
        $message = '商品が見つかりました';
        $crawler = $client->request('GET', $this->generateUrl('product_list', ['category_id' => '6']));
        $this->assertContains($message, $crawler->html());
    }

    /**
     * testProductClassSortByRank
     */
    public function testProductClassSortByRank()
    {
        /* @var $ClassCategory \Eccube\Entity\ClassCategory */
        //set チョコ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'チョコ']);
        $ClassCategory->setSortNo(3);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set 抹茶 rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => '抹茶']);
        $ClassCategory->setSortNo(2);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set バニラ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'バニラ']);
        $ClassCategory->setSortNo(1);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        $client = $this->client;
        $crawler = $client->request('GET', $this->generateUrl('product_detail', ['id' => '1']));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $classCategory = $crawler->filter('#classcategory_id1')->text();
        //選択してください, チョコ, 抹茶, バニラ sort by rank setup above.
        $this->expected = '選択してくださいチョコ抹茶バニラ';
        $this->actual = $classCategory;
        $this->verify();
    }

    /**
     * Test product can add favorite when out of stock.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1637
     */
    public function testProductFavoriteAddWhenOutOfStock()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);
        $Product = $this->createProduct('Product no stock', 1);
        /** @var $ProductClass ProductClass */
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(0);
        $ProductStock = $ProductClass->getProductStock();
        $ProductStock->setStock(0);
        $this->entityManager->flush();
        $id = $Product->getId();
        $user = $this->createCustomer();
        $this->loginTo($user);

        /** @var $client Client */
        $client = $this->client;
        /** @var $crawler Crawler */
        $crawler = $client->request('GET', $this->generateUrl('product_detail', ['id' => $id]));

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Case 1: render check
        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertContains('ただいま品切れ中です', $html);
        $this->assertContains('お気に入りに追加', $html);

        $favoriteForm = $crawler->selectButton('お気に入りに追加')->form();

        $client->submit($favoriteForm);
        $crawler = $client->followRedirect();

        // Case 2: after add favorite check
        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertContains('ただいま品切れ中です', $html);
        $this->assertContains('お気に入りに追加済です', $html);
    }

    /**
     * Test product can add favorite
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1637
     */
    public function testProductFavoriteAdd()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);
        $Product = $this->createProduct('Product stock', 1);
        $id = $Product->getId();
        $user = $this->createCustomer();
        $this->loginTo($user);

        /** @var $client Client */
        $client = $this->client;
        /** @var $crawler Crawler */
        $crawler = $client->request('GET', $this->generateUrl('product_detail', ['id' => $id]));

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Case 3: render check when 商品在庫>0
        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertContains('カートに入れる', $html);
        $this->assertContains('お気に入りに追加', $html);

        $favoriteForm = $crawler->selectButton('お気に入りに追加')->form();

        $client->submit($favoriteForm);
        $crawler = $client->followRedirect();

        // Case 4: after add favorite when 商品在庫>0
        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertContains('カートに入れる', $html);
        $this->assertContains('お気に入りに追加済です', $html);
    }


    /**
     * 商品詳細 → ログイン画面 → お気に入り追加 → 商品詳細(お気に入り登録済み)
     */
    public function testProductFavoriteAddThroughLogin()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setOptionFavoriteProduct(true);
        $Product = $this->createProduct();
        $id = $Product->getId();

        $user = $this->createCustomer();

        /** @var $client Client */
        $client = $this->client;

        /** @var $crawler Crawler */
        $crawler = $client->request('GET', $this->generateUrl('product_detail', ['id' => $id]));

        $this->assertTrue($client->getResponse()->isSuccessful());

        // お気に入りに追加をクリック
        $favoriteForm = $crawler->selectButton('お気に入りに追加')->form();
        $client->submit($favoriteForm);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage_login')));

        // ログインフォームへメールアドレス・パスワードを入力
        $crawler = $client->followRedirect();
        $loginForm = $crawler->selectButton('ログイン')->form();
        $loginForm['login_email'] = $user->getEmail();
        $loginForm['login_pass'] = 'password';

        // ログインをクリック
        $client->submit($loginForm);

        // ログイン実行後、お気に入り追加へリダイレクト
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('product_add_favorite', ['id' => $Product->getId()], UrlGeneratorInterface::ABSOLUTE_URL)));
        $crawler = $client->followRedirect();

        // お気に入り追加実行後、商品詳細ページへリダイレクト
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('product_detail', ['id' => $Product->getId()])));
        $crawler = $client->followRedirect();

        $html = $crawler->filter('div.ec-productRole__profile')->html();
        $this->assertContains('お気に入りに追加済です', $html);
    }
}
