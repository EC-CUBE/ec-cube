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
        $crawler = $client->request('POST',
            $this->app->url('product_detail', array('id' => $id))
        );

        $this->assertTrue($client->getResponse()->isSuccessful());

        $favoriteForm = $crawler->selectButton('お気に入りに追加')->form();
        $favoriteForm['mode'] = 'add_favorite';

        $client->submit($favoriteForm);
        $crawler = $client->followRedirect();

        $this->assertContains('お気に入りに追加済みです', $crawler->filter('#detail_cart_box')->html());
    }
}
