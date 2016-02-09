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
        $client = $this->client;
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
        $client = $this->client;
        // カート画面
        $this->scenarioCartIn($client);

        // 確認画面
        $crawler = $this->scenarioConfirm($client);
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        // 完了画面
        $crawler = $this->scenarioComplete($client, $this->app->path('shopping_confirm'));
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

    /**
     * 購入確認画面→お届け先設定(未入力)
     */
    public function testDeliveryWithNotInput()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;
        // カート画面
        $this->scenarioCartIn($client);

        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // お届け先指定画面
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_delivery')
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * 購入確認画面→お届け先設定
     */
    public function testDeliveryWithPost()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;

        // カート画面
        $this->scenarioCartIn($client);

        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // お届け先指定画面
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_delivery'),
            array(
                'shopping' => array(
                    'shippings' => array(
                        0 => array(
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

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));
    }

    /**
     * 購入確認画面→お届け先設定(入力エラー)
     */
    public function testDeliveryWithError()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;
        // カート画面
        $this->scenarioCartIn($client);

        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // お届け先指定
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_delivery'),
            array(
                'shopping' => array(
                    'shippings' => array(
                        0 => array(
                            'delivery' => 5, // delivery=5 は無効な値
                            'deliveryTime' => 1
                        ),
                    ),
                    'payment' => 1,
                    'message' => $faker->text(),
                    '_token' => 'dummy'
                )
            )
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->expected = '有効な値ではありません。';
        $this->actual = $crawler->filter('p.errormsg')->text();
        $this->verify();
    }

    /**
     * 購入確認画面→支払い方法選択
     */
    public function testPaymentWithPost()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;

        // カート画面
        $this->scenarioCartIn($client);

        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // 支払い方法選択
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_payment'),
            array(
                'shopping' => array(
                    'shippings' => array(
                        0 => array(
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

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));
    }

    /**
     * 購入確認画面→支払い方法選択(エラー)
     */
    public function testPaymentWithError()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;

        // カート画面
        $this->scenarioCartIn($client);
        // 確認画面
        $crawler = $this->scenarioConfirm($client);
        // 支払い方法選択
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_payment'),
            array(
                'shopping' => array(
                    'shippings' => array(
                        0 => array(
                            'delivery' => 1,
                            'deliveryTime' => 1
                        ),
                    ),
                    'payment' => 100, // payment=100 は無効な値
                    'message' => $faker->text(),
                    '_token' => 'dummy'
                )
            )
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->expected = '有効な値ではありません。';
        $this->actual = $crawler->filter('p.errormsg')->text();
        $this->verify();
    }

    /**
     * 購入確認画面→お届け先の設定
     */
    public function testShippingChangeWithPost()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;
        // カート画面
        $this->scenarioCartIn($client);
        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // お届け先指定画面
        $shipping_url = $crawler->filter('a.btn-shipping')->attr('href');
        $crawler = $this->scenarioComplete($client, $shipping_url);

        // /shipping/shipping_change/<id> から /shipping/shipping/<id> へリダイレクト
        $shipping_url = str_replace('shipping_change', 'shipping', $shipping_url);
        $this->assertTrue($client->getResponse()->isRedirect($shipping_url));
    }

    /**
     * 購入確認画面→お届け先の設定→お届け先一覧
     */
    public function testShippingShipping()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;
        // カート画面
        $this->scenarioCartIn($client);
        // 確認画面
        $crawler = $this->scenarioConfirm($client);
        // お届け先指定画面
        $shipping_url = $crawler->filter('a.btn-shipping')->attr('href');
        $crawler = $this->scenarioComplete($client, $shipping_url);

        $shipping_url = str_replace('shipping_change', 'shipping', $shipping_url);

        // お届け先一覧
        $crawler = $client->request(
            'GET',
            $shipping_url
        );

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'お届け先の指定';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();
    }

    /**
     * 購入確認画面→お届け先の設定→お届け先追加→購入完了
     */
    public function testShippingShippingPost()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;

        // カート画面
        $this->scenarioCartIn($client);
        // 確認画面
        $crawler = $this->scenarioConfirm($client);
        // お届け先の設定
        $shipping_url = $crawler->filter('a.btn-shipping')->attr('href');
        $crawler = $this->scenarioComplete($client, $shipping_url);

        // お届け先一覧
        $shipping_url = str_replace('shipping_change', 'shipping', $shipping_url);

        $crawler = $client->request(
            'GET',
            $shipping_url
        );

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'お届け先の指定';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $shipping_edit_url = $crawler->filter('a.btn-default')->attr('href');

        // お届け先入力画面
        $crawler = $client->request(
            'GET',
            $shipping_edit_url
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        // お届け先設定画面へ遷移し POST 送信
        $formData = $this->createShippingFormData();
        $formData['tel'] = array(
            'tel01' => 222,
            'tel02' => 222,
            'tel03' => 222,
        );
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );

        $crawler = $client->request(
            'POST',
            $shipping_edit_url,
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // ご注文完了
        $this->scenarioComplete($client, $this->app->path('shopping_confirm'));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        // FIXME https://github.com/EC-CUBE/ec-cube/issues/1305
        // $this->assertRegexp('/111-111-111/', $this->parseMailCatcherSource($Message), '変更した FAX 番号が一致するか');
        $this->assertRegexp('/222-222-222/', $this->parseMailCatcherSource($Message), '変更した 電話番号が一致するか');
    }


    public function createShippingFormData()
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
            '_token' => 'dummy'
        );
        return $form;
    }

    protected function scenarioCartIn($client)
    {
        $crawler = $client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->app['eccube.service.cart']->lock();
        return $crawler;
    }

    protected function scenarioInput($client, $formData)
    {
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_nonmember'),
            array('nonmember' => $formData)
        );
        $this->app['eccube.service.cart']->lock();
        return $crawler;
    }

    protected function scenarioConfirm($client)
    {
        $crawler = $client->request('GET', $this->app->path('shopping'));
        return $crawler;
    }

    protected function scenarioComplete($client, $confirm_url)
    {
        $faker = $this->getFaker();
        $crawler = $client->request(
            'POST',
            $confirm_url,
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
        return $crawler;
    }
}
