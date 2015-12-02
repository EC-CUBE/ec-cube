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

class ShoppingControllerTest extends AbstractWebTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->initializeMailCatcher();
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    public function testRoutingShoppingLogin()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/shopping/login');
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('cart')));
    }

    public function testShoppingIndexWithCartUnlock()
    {
        $this->app['eccube.service.cart']->unlock();

        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app->path('shopping'));

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('cart')));
    }

    public function testComplete()
    {
        $this->app['session']->set('eccube.front.shopping.order.id', 111);

        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app->path('shopping_complete'));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertNull($this->app['session']->get('eccube.front.shopping.order.id'));
    }

    public function testShoppingError()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app->path('shopping_error'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * カート→購入確認画面→完了画面
     */
    public function testCompleteWithLogin()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->createClient();
        $client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->app['eccube.service.cart']->lock();

        $crawler = $client->request('GET', $this->app->path('shopping'));
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_confirm'),
            array('shopping' =>
                  array(
                      'shippings' =>
                      array(0 =>
                            array(
                                'delivery' => 1,
                                'deliveryTime' => 1
                            ),
                      ),
                      'payment' => 1,
                      'message' => $faker->text(),
                      '_token' => 'dummy'
                  )
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] ご注文ありがとうございます';
        $this->actual = $Message->subject;
        $this->verify();

        // 生成された受注のチェック
        $Order = $this->app['eccube.repository.order']->findOneBy(
            array(
                'Customer' => $Customer
            )
        );

        $OrderNew = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $this->expected = $OrderNew;
        $this->actual = $Order->getOrderStatus();
        $this->verify();

        $this->expected = $Customer->getName01();
        $this->actual = $Order->getName01();
        $this->verify();
    }
}
