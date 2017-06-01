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

use Eccube\Application;
use Faker\Generator;
use Symfony\Component\HttpKernel\Client;

/**
 * Class ShoppingControllerWithNonmemberTest
 * @package Eccube\Tests\Web
 */
class ShoppingControllerWithNonmemberTest extends AbstractShoppingControllerTestCase
{
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
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

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
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->scenarioComplete($client, $this->app->path('shopping_confirm'));

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

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('cart')));
    }

    public function testNonmemberWithCustomerLogin()
    {
        $client = $this->client;

        // ユーザーが会員ログイン済みの場合
        $this->logIn();
        $this->scenarioCartIn($client);

        $crawler = $client->request('GET', $this->app->path('shopping_nonmember'));
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));
    }

    public function testNonmemberInput()
    {
        $client = $this->createClient();
        $this->scenarioCartIn($client);

        $crawler = $client->request('GET', $this->app->path('shopping_nonmember'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testNonmemberInputWithPost()
    {
        $client = $this->createClient();
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $Nonmember = $this->app['session']->get('eccube.front.shopping.nonmember');
        $this->assertNotNull($Nonmember);
        $this->assertNotNull($this->app['session']->get('eccube.front.shopping.nonmember.customeraddress'));

        $this->expected = $formData['name']['name01'];
        $this->actual = $Nonmember['customer']->getName01();
        $this->verify();

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));
    }

    public function testShippingEditChange()
    {
        $faker = $this->getFaker();
        $client = $this->createClient();

        $this->scenarioCartIn($client);
        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);
        $crawler = $this->scenarioConfirm($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $crawler = $client->request('GET', $crawler->filter('a.btn-shipping-edit')->attr('href'));

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));
    }

    /**
     * 購入確認画面→お届け先の設定(非会員)
     */
    public function testShippingEditChangeWithPost()
    {
        $faker = $this->getFaker();
        $client = $this->createClient();

        $this->scenarioCartIn($client);
        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);
        $crawler = $this->scenarioConfirm($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $crawler = $client->request('POST', $crawler->filter('a.btn-shipping-edit')->attr('href'));

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * 購入確認画面→お届け先の設定(非会員)
     */
    public function testShippingEditChangeWithPostVerify()
    {
        $faker = $this->getFaker();
        $client = $this->createClient();

        $this->scenarioCartIn($client);
        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        // 購入確認画面
        $crawler = $this->scenarioConfirm($client);
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $crawler = $this->scenarioComplete($client, $shipping_edit_change_url);

        // お届け先設定画面へ遷移
        $shipping_edit_url = str_replace('shipping_edit_change', 'shipping_edit', $shipping_edit_change_url);
        $this->assertTrue($client->getResponse()->isRedirect($shipping_edit_url));
    }

    public function testShippingEdit()
    {
        $faker = $this->getFaker();
        $client = $this->createClient();

        $this->scenarioCartIn($client);
        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);
        $crawler = $this->scenarioConfirm($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $crawler = $this->scenarioComplete($client, $shipping_edit_change_url);

        // お届け先設定画面へ遷移
        $shipping_edit_url = str_replace('shipping_edit_change', 'shipping_edit', $shipping_edit_change_url);

        $crawler = $client->request('GET', $shipping_edit_url);
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'お届け先の変更';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->assertContains($this->expected, $this->actual);
    }

    /**
     * 購入確認画面→お届け先の設定(非会員)→お届け先変更→購入完了
     */
    public function testShippingEditWithPostToComplete()
    {
        $faker = $this->getFaker();
        $client = $this->createClient();

        $this->scenarioCartIn($client);
        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);
        $crawler = $this->scenarioConfirm($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $crawler = $this->scenarioComplete($client, $shipping_edit_change_url);

        // お届け先設定画面へ遷移し POST 送信
        $shipping_edit_url = str_replace('shipping_edit_change', 'shipping_edit', $shipping_edit_change_url);
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

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

        $this->assertRegexp('/111-111-111/', $this->parseMailCatcherSource($Message), '変更した FAX 番号が一致するか');
    }

    /**
     * @link https://github.com/EC-CUBE/ec-cube/issues/1696
     * @link https://github.com/EC-CUBE/ec-cube/issues/1267
     */
    public function testEditCustomerNotAjaxRequest()
    {
        /** @var $app Application*/
        $app = $this->app;
        /** @var $client Client*/
        $client = $this->client;

        $crawler = $this->scenarioShoppingConfirmPage($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $data = $this->createCustomerAjaxForm();
        $app['eccube.service.cart']->clear();
        $client->request('POST', $app->url('shopping_customer'), $data, array());

        $response = $client->getResponse();
        // Test if response is OK
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        // Test if Content-Type is valid application/json
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        // Test content
        $this->assertEquals('{"status":"NG"}', $response->getContent());
        // Test that response is not empty
        $this->assertNotEmpty($client->getResponse()->getContent());
    }

    /**
     * @link https://github.com/EC-CUBE/ec-cube/issues/1696
     * @link https://github.com/EC-CUBE/ec-cube/issues/1267
     */
    public function testEditCustomerByAjaxRequestInvalidKana()
    {
        /** @var $app Application*/
        $app = $this->app;
        /** @var $client Client*/
        $client = $this->client;

        $crawler = $this->scenarioShoppingConfirmPage($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $data = $this->createCustomerAjaxForm();
        $data['customer_kana01'] = 'test';
        $client->request('POST', $app->url('shopping_customer'), $data, array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));

        $response = $client->getResponse();
        // Test if response is OK
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        // Test if Content-Type is valid application/json
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        // Test content
        $this->assertEquals('{"status":"NG"}', $response->getContent());
        // Test that response is not empty
        $this->assertNotEmpty($client->getResponse()->getContent());
    }

    /**
     * @link https://github.com/EC-CUBE/ec-cube/issues/1696
     * @link https://github.com/EC-CUBE/ec-cube/issues/1267
     */
    public function testEditCustomerByAjaxRequestInvalidPref()
    {
        /** @var $app Application*/
        $app = $this->app;
        /** @var $client Client*/
        $client = $this->client;

        $crawler = $this->scenarioShoppingConfirmPage($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $data = $this->createCustomerAjaxForm();
        $data['customer_pref'] = 'test';
        $client->request('POST', $app->url('shopping_customer'), $data, array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));

        $response = $client->getResponse();
        // Test if response is OK
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        // Test if Content-Type is valid application/json
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        // Test content
        $this->assertEquals('{"status":"NG"}', $response->getContent());
        // Test that response is not empty
        $this->assertNotEmpty($client->getResponse()->getContent());
    }

    /**
     * @link https://github.com/EC-CUBE/ec-cube/issues/1696
     * @link https://github.com/EC-CUBE/ec-cube/issues/1267
     */
    public function testEditCustomerByAjaxRequestOrderCannotFound()
    {
        /** @var $app Application*/
        $app = $this->app;
        /** @var $client Client*/
        $client = $this->client;

        $crawler = $this->scenarioShoppingConfirmPage($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $data = $this->createCustomerAjaxForm();
        $app['eccube.service.cart']->clear();
        $client->request('POST', $app->url('shopping_customer'), $data, array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));

        $this->assertTrue($client->getResponse()->isRedirect($app->url('shopping_error')));
    }

    /**
     * @link https://github.com/EC-CUBE/ec-cube/issues/1696
     * @link https://github.com/EC-CUBE/ec-cube/issues/1267
     */
    public function testEditCustomerByAjaxRequestSuccess()
    {
        /** @var $app Application*/
        $app = $this->app;
        /** @var $client Client*/
        $client = $this->client;

        $crawler = $this->scenarioShoppingConfirmPage($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $data = $this->createCustomerAjaxForm();
        $client->request('POST', $app->url('shopping_customer'), $data, array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));

        $response = $client->getResponse();
        // Test if response is OK
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        // Test if Content-Type is valid application/json
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        // Test content
        $this->assertContains('"status":"OK"', $response->getContent());
        // Test that response is not empty
        $this->assertNotEmpty($client->getResponse()->getContent());
    }

    public function createNonmemberFormData()
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $form = parent::createShippingFormData();
        $form['email'] = array(
            'first' => $email,
            'second' => $email
        );
        return $form;
    }

    /**
     * @link https://github.com/EC-CUBE/ec-cube/issues/1280
     */
    public function testShippingEditTitle()
    {
        $client = $this->createClient();
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        /** @var $crawler Crawler*/
        $crawler = $this->scenarioConfirm($client);
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $shippingCrawler = $crawler->filter('#shipping_confirm_box--0');
        $url = $shippingCrawler->selectLink('変更')->link()->getUri();
        $url = str_replace('shipping_edit_change', 'shipping_edit', $url);

        // Get shipping edit
        $crawler = $client->request('GET', $url);
        // Title
        $this->assertContains('お届け先の変更', $crawler->html());
        // Header
        $this->assertContains('お届け先の変更', $crawler->filter('title')->html());
    }

    /**
     * @return array
     */
    private function createCustomerAjaxForm()
    {
        /** @var $faker Generator */
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);
        $data = array(
            'customer_name01' => $faker->firstName,
            'customer_name02' => $faker->lastName,
            'customer_kana01' => $faker->firstKanaName,
            'customer_kana02' => $faker->lastKanaName,

            'customer_zip01' => $faker->numberBetween(100, 999),
            'customer_zip02' => $faker->numberBetween(1000, 9999),

            'customer_pref' => '福井県',
            'customer_addr01' => $faker->city,
            'customer_addr02' => $faker->streetAddress,

            'customer_tel01' => $tel[0],
            'customer_tel02' => $tel[1],
            'customer_tel03' => $tel[2],

            'customer_email' => $faker->safeEmail,
            'customer_company_name' => $faker->word,
        );

        return $data;
    }

    /**
     * @param $client
     * @return mixed
     */
    private function scenarioShoppingConfirmPage($client)
    {
        $this->scenarioCartIn($client);
        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);
        $crawler = $this->scenarioConfirm($client);

        return $crawler;
    }
}
