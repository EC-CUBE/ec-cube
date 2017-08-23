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

use Symfony\Component\DomCrawler\Crawler;

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

    /**
     * 購入確認画面→お届け先の設定画面(非会員)へ遷移する
     */
    public function testShippingEdit()
    {
        // FIXME お届け先情報編集機能が実装されたら有効にする
        $this->markTestIncomplete('Shipping edit is not implemented.');

        $faker = $this->getFaker();
        $client = $this->createClient();

        $this->scenarioCartIn($client);
        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);
        $crawler = $this->scenarioConfirm($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        preg_match('/\/(\d)$/', $shipping_edit_change_url, $matches);

        // 値を保持してお届け先設定画面へ遷移
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_redirect_to'),
            array(
                '_shopping_order' => array(
                    'Shippings' => array(
                        0 => array(
                            'Delivery' => 1,
                            'DeliveryTime' => 1
                        ),
                    ),
                    'Payment' => 1,
                    'message' => $faker->text(),
                    '_token' => 'dummy',
                    'mode' => 'shipping_edit_change',
                    'param' => $matches[1],
                )
            )
        );

        // お届け先設定画面へリダイレクト.
        $shipping_edit_url = str_replace('shipping_edit_change', 'shipping_edit', $shipping_edit_change_url);
        $this->assertTrue($client->getResponse()->isRedirect($shipping_edit_url));

        // お届け先設定画面が表示される.
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
        // FIXME お届け先情報編集機能が実装されたら有効にする
        $this->markTestIncomplete('Shipping edit is not implemented.');

        $faker = $this->getFaker();
        $client = $this->createClient();

        $this->scenarioCartIn($client);
        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);
        $crawler = $this->scenarioConfirm($client);

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        preg_match('/\/(\d)$/', $shipping_edit_change_url, $matches);

        // 値を保持してお届け先設定画面へ遷移
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_redirect_to'),
            array(
                '_shopping_order' => array(
                    'Shippings' => array(
                        0 => array(
                            'Delivery' => 1,
                            'DeliveryTime' => 1
                        ),
                    ),
                    'Payment' => 1,
                    'message' => $faker->text(),
                    '_token' => 'dummy',
                    'mode' => 'shipping_edit_change',
                    'param' => $matches[1],
                )
            )
        );

        // お届け先設定画面へリダイレクト.
        $shipping_edit_url = str_replace('shipping_edit_change', 'shipping_edit', $shipping_edit_change_url);
        $this->assertTrue($client->getResponse()->isRedirect($shipping_edit_url));

        // お届け先設定画面が表示される.
        $crawler = $client->request('GET', $shipping_edit_url);
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'お届け先の変更';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->assertContains($this->expected, $this->actual);

        // お届け先設定画面で、入力値を変更しPOST送信
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
}
