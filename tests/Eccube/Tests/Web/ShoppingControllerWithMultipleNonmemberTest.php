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

/**
 * 非会員複数配送指定のテストケース.
 *
 * @author Kentaro Ohkouchi
 */
class ShoppingControllerWithMultipleNonmemberTest extends AbstractShoppingControllerTestCase
{

    public function setUp()
    {
        parent::setUp();

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        // 複数配送を有効に
        $BaseInfo->setOptionMultipleShipping(1);
        $this->app['orm.em']->flush($BaseInfo);
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    /**
     * 非会員情報入力→購入確認画面→複数配送設定→お届け先追加→複数配送設定→購入確認画面→完了画面
     */
    public function testCompleteWithNonmember()
    {
        $faker = $this->getFaker();
        $client = $this->createClient();
        $this->scenarioCartIn($client);
        $this->scenarioCartIn($client); // 2個カート投入

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        // 複数配送画面
        $crawler = $client->request('GET', $this->app->path('shopping_shipping_multiple'));

        // お届け先情報入力画面
        $crawler = $client->request('GET', $this->app->path('shopping_shipping_multiple_edit'));

        $form = $this->createShippingFormData();
        $form['fax'] = array(
            'fax01' => $form['tel']['tel01'],
            'fax02' => $form['tel']['tel02'],
            'fax03' => $form['tel']['tel03'],
        );

        // お届け先追加
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $form)
        );

        $crawler = $client->request('GET', $this->app->path('shopping_shipping_multiple'));

        // 配送先1, 配送先2の情報を返す
        $shippings = $crawler->filter('#form_shipping_multiple_0_shipping_0_customer_address > option')->each(
            function ($node, $i) {
                return array(
                    'customer_address' => $node->attr('value'),
                    'quantity' => 1
                );
            }
        );

        // 複数配送設定
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_shipping_multiple'),
            array('form' =>
                  array(
                      'shipping_multiple' =>
                      array(0 =>
                            array(
                                // 配送先1, 配送先2 の 情報を渡す
                                'shipping' => $shippings
                            )
                      ),
                      '_token' => 'dummy'
                  )
            )
        );

        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // 完了画面
        $crawler = $this->scenarioComplete(
            $client,
            $this->app->path('shopping_confirm'),
            array(
                // 配送先1
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1
                ),
                // 配送先2
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1
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

        $body = $this->parseMailCatcherSource($Message);
        $this->assertRegexp('/◎お届け先2/', $body, '複数配送のため, お届け先2が存在する');
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
