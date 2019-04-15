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
use Eccube\Entity\ProductClass;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Client;

class ProductControllerTest extends AbstractWebTestCase
{

    public function testRoutingList()
    {
        $client = $this->client;
        $client->request('GET', $this->app->url('product_list'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingDetail()
    {
        $client = $this->client;
        $client->request('GET', $this->app->url('product_detail', array('id' => '1')));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingProductFavoriteAdd()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionFavoriteProduct(Constant::ENABLED);

        $client = $this->client;
        $client->request('POST',
            $this->app->url('product_detail', array('id' => '1'))
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * test with category id is invalid.
     */
    public function testCategoryNotFound()
    {
        $client = $this->client;
        $message = 'ご指定のカテゴリは存在しません。';
        $crawler = $client->request('GET', $this->app->url('product_list', array('category_id' => 'XXX')));
        $this->assertContains($message, $crawler->html());
    }

    /**
     * test with category id is valid.
     */
    public function testCategoryFound()
    {
        $client = $this->client;
        $message = '商品がみつかりました';
        $crawler = $client->request('GET', $this->app->url('product_list', array('category_id' => '6')));
        $this->assertContains($message, $crawler->html());
    }

    /**
     * testProductClassSortByRank
     */
    public function testProductClassSortByRank()
    {
        /* @var $ClassCategory \Eccube\Entity\ClassCategory */
        //set 金 rank
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(array('name' => '金'));
        $ClassCategory->setRank(3);
        $this->app['orm.em']->persist($ClassCategory);
        $this->app['orm.em']->flush($ClassCategory);
        //set 銀 rank
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(array('name' => '銀'));
        $ClassCategory->setRank(2);
        $this->app['orm.em']->persist($ClassCategory);
        $this->app['orm.em']->flush($ClassCategory);
        //set プラチナ rank
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(array('name' => 'プラチナ'));
        $ClassCategory->setRank(1);
        $this->app['orm.em']->persist($ClassCategory);
        $this->app['orm.em']->flush($ClassCategory);
        $client = $this->client;
        $crawler = $client->request('GET', $this->app->url('product_detail', array('id' => '1')));
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
     * @link https://github.com/EC-CUBE/ec-cube/issues/1637
     */
    public function testProductFavoriteAddWhenOutOfStock()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionFavoriteProduct(Constant::ENABLED);
        $Product = $this->createProduct('Product no stock', 1);
        /** @var $ProductClass ProductClass */
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStockUnlimited(Constant::DISABLED);
        $ProductClass->setStock(0);
        $ProductStock = $ProductClass->getProductStock();
        $ProductStock->setStock(0);
        $this->app['orm.em']->flush();
        $id = $Product->getId();
        $user = $this->createCustomer();
        $this->loginTo($user);

        /** @var $client Client */
        $client = $this->client;
        /** @var $crawler Crawler */
        $crawler = $client->request('GET', $this->app->url('product_detail', array('id' => $id)));

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Case 1: render check
        $html = $crawler->filter('#detail_cart_box')->html();
        $this->assertContains('ただいま品切れ中です', $html);
        $this->assertContains('お気に入りに追加', $html);

        $favoriteForm = $crawler->selectButton('お気に入りに追加')->form();
        $favoriteForm['mode'] = 'add_favorite';

        $client->submit($favoriteForm);
        $crawler = $client->followRedirect();

        // Case 2: after add favorite check
        $html = $crawler->filter('#detail_cart_box')->html();
        $this->assertContains('ただいま品切れ中です', $html);
        $this->assertContains('お気に入りに追加済みです', $html);
    }

    /**
     * Test product can add favorite
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1637
     */
    public function testProductFavoriteAdd()
    {
        // お気に入り商品機能を有効化
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionFavoriteProduct(Constant::ENABLED);
        $Product = $this->createProduct('Product stock', 1);
        $id = $Product->getId();
        $user = $this->createCustomer();
        $this->loginTo($user);

        /** @var $client Client */
        $client = $this->client;
        /** @var $crawler Crawler */
        $crawler = $client->request('GET', $this->app->url('product_detail', array('id' => $id)));

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Case 3: render check when 商品在庫>0
        $html = $crawler->filter('#detail_cart_box')->html();
        $this->assertContains('カートに入れる', $html);
        $this->assertContains('お気に入りに追加', $html);

        $favoriteForm = $crawler->selectButton('お気に入りに追加')->form();
        $favoriteForm['mode'] = 'add_favorite';

        $client->submit($favoriteForm);
        $crawler = $client->followRedirect();

        // Case 4: after add favorite when 商品在庫>0
        $html = $crawler->filter('#detail_cart_box')->html();
        $this->assertContains('カートに入れる', $html);
        $this->assertContains('お気に入りに追加済みです', $html);
    }
}
