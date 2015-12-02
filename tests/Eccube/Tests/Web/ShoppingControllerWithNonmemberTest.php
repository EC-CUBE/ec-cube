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

class ShoppingControllerWithNonmemberTest extends AbstractWebTestCase
{

    protected $Product;

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

    public function testIndexWithCartUnlock()
    {
        $this->app['eccube.service.cart']->unlock();

        $client = $this->createClient();
        $crawler = $client->request('GET', '/shopping');

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('cart')));
    }

    public function testIndexWithCartNotFound()
    {
        $this->app['eccube.service.cart']->lock();

        $client = $this->createClient();
        $crawler = $client->request('GET', '/shopping');

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('cart')));
    }

    /**
     * 非会員情報入力→購入確認画面
     */
    public function testConfirmWithNonmember()
    {
        $client = $this->createClient();
        $client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->app['eccube.service.cart']->lock();

        $formData = $this->createNonmemberFormData();
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_nonmember'),
            array('nonmember' => $formData)
        );
        $this->app['eccube.service.cart']->lock();

        $crawler = $client->request('GET', $this->app->path('shopping'));
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * 非会員情報入力→購入確認画面→完了画面
     */
    public function testCompleteWithNonmember()
    {
        $faker = $this->getFaker();
        $client = $this->createClient();
        $client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->app['eccube.service.cart']->lock();

        $formData = $this->createNonmemberFormData();
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_nonmember'),
            array('nonmember' => $formData)
        );
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
    }

    public function testNonmemberWithCartUnlock()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app->path('shopping_nonmember'));

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('cart')));  }

    public function testNonmemberWithCustomerLogin()
    {
        $client = $this->createClient();

        // ユーザーが会員ログイン済みの場合
        $this->logIn();
        $client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->app['eccube.service.cart']->lock();

        $crawler = $client->request('GET', $this->app->path('shopping_nonmember'));
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));
    }

    public function testNonmemberInput()
    {
        $client = $this->createClient();
        $client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->app['eccube.service.cart']->lock();

        $crawler = $client->request('GET', $this->app->path('shopping_nonmember'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testNonmemberInputWithPost()
    {
        $client = $this->createClient();
        $client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->app['eccube.service.cart']->lock();

        $formData = $this->createNonmemberFormData();
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_nonmember'),
            array('nonmember' => $formData)
        );

        $Nonmember = $this->app['session']->get('eccube.front.shopping.nonmember');
        $this->assertNotNull($Nonmember);
        $this->assertNotNull($this->app['session']->get('eccube.front.shopping.nonmember.customeraddress'));

        $this->expected = $formData['name']['name01'];
        $this->actual = $Nonmember['customer']->getName01();
        $this->verify();

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));
    }

    public function createNonmemberFormData()
    {
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

        $email = $faker->safeEmail;

        $form = array(
            'name' => array(
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ),
            'kana' => array(
                'kana01' => $faker->lastKanaName ,
                'kana02' => $faker->firstKanaName,
            ),
            'company_name' => $faker->company,
            'zip' => array(
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ),
            'address' => array(
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ),
            'tel' => array(
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ),
            'email' => array(
                'first' => $email,
                'second' => $email,
            ),
            '_token' => 'dummy'
        );
        return $form;
    }
}
