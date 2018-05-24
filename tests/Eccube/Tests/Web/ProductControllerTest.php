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

use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\ClassCategoryRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Client;

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
        //set 金 rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => '金']);
        $ClassCategory->setSortNo(3);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set 銀 rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => '銀']);
        $ClassCategory->setSortNo(2);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set プラチナ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'プラチナ']);
        $ClassCategory->setSortNo(1);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        $client = $this->client;
        $crawler = $client->request('GET', $this->generateUrl('product_detail', ['id' => '1']));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $classCategory = $crawler->filter('#classcategory_id1')->text();
        //選択してください, 金, 銀, プラチナ sort by rank setup above.
        $this->expected = '選択してください金銀プラチナ';
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
        $this->assertContains('お気に入りに追加済みです', $html);
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
        $this->assertContains('お気に入りに追加済みです', $html);
    }
}
