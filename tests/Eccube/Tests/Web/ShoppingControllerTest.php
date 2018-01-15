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
use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\CartService;
use Symfony\Component\DomCrawler\Crawler;

class ShoppingControllerTest extends AbstractShoppingControllerTestCase
{
    /**
     * @var BaseInfoRepository
     */
    private $baseInfoRepository;

    public function setUp()
    {
        parent::setUp();
        $this->baseInfoRepository = $this->container->get(BaseInfoRepository::class);
    }

    public function testRoutingShoppingLogin()
    {
        $client = $this->client;
        $client->request('GET', '/shopping/login');
        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('cart')));
    }

    public function testShoppingIndexWithCartUnlock()
    {
        $this->container->get(CartService::class)->unlock();
        $this->client->request('GET', $this->generateUrl('shopping'));

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('cart')));
    }

    public function testComplete()
    {
        $this->container->get('session')->set('eccube.front.shopping.order.id', 111);
        $this->client->request('GET', $this->generateUrl('shopping_complete'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertNull($this->container->get('session')->get('eccube.front.shopping.order.id'));
    }

    public function testShoppingError()
    {
        $this->client->request('GET', $this->generateUrl('shopping_error'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * カート→購入確認画面→完了画面
     */
    public function testCompleteWithLogin()
    {
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // 手続き画面
        $crawler = $this->scenarioConfirm($Customer);
        $this->expected = 'ご注文手続き';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        // 確認画面
        $crawler = $this->scenarioComplete($Customer, $this->generateUrl('shopping_confirm'));
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        // 完了画面
        $this->scenarioComplete($Customer, $this->generateUrl('shopping_order'));
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_complete')));

        $BaseInfo = $this->baseInfoRepository->get();
        $mailCollector = $this->getMailCollector(false);
        $Messages = $mailCollector->getMessages();
        $Message = $Messages[0];

        $this->expected = '[' . $BaseInfo->getShopName() . '] ご注文ありがとうございます';
        $this->actual = $Message->getSubject();
        $this->verify();

        // 生成された受注のチェック
        $Order = $this->container->get(OrderRepository::class)->findOneBy(
            array(
                'Customer' => $Customer
            )
        );

        $OrderNew = $this->container->get(OrderStatusRepository::class)->find(OrderStatus::NEW);
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
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // 確認画面
        $this->scenarioConfirm($Customer);

        // お届け先指定画面
        $token = $this->getCsrfToken('_shopping_order');
        $this->scenarioRedirectTo($Customer, [
            'shopping_order_mode' => 'delivery',
            '_token' => $token
        ]);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 購入確認画面→お届け先設定
     */
    public function testDeliveryWithPost()
    {
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // 確認画面
        $this->scenarioConfirm($Customer);

        // お届け先指定画面
        $token = $this->getCsrfToken('_shopping_order');
        $this->scenarioRedirectTo($Customer, [
            '_shopping_order' => [
                'Shippings' => [
                    0 => [
                        'Delivery' => 1,
                        'DeliveryTime' => 1
                    ],
                ],
                'Payment' => 1,
                'message' => $this->getFaker()->realText(),
                '_token' => $token
            ],
            ['shopping_order_mode' => 'delivery']
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));
    }

    /**
     * 購入確認画面→お届け先設定(入力エラー)
     */
    public function testDeliveryWithError()
    {
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // 確認画面
        $this->scenarioConfirm($Customer);

        // お届け先指定
        $token = $this->getCsrfToken('_shopping_order');
        $crawler = $this->scenarioRedirectTo($Customer, [
            '_shopping_order' => [
                'Shippings' => [
                    0 => [
                        'Delivery' => 5, // delivery=5 は無効な値
                        'DeliveryTime' => 1
                    ],
                ],
                'Payment' => 1,
                'message' => $this->getFaker()->realText(),
                '_token' => $token
            ],
            ['shopping_order_mode' => 'delivery']
        ]);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = '有効な値ではありません。';
        $this->actual = $crawler->filter('p.ec-errorMessage')->text();
        $this->verify();
    }

    /**
     * 購入確認画面→支払い方法選択
     */
    public function testPaymentWithPost()
    {
        $Customer = $this->createCustomer();

        // カート画面mer);
        $this->scenarioCartIn($Customer);

        // 確認画面
        $this->scenarioConfirm($Customer);

        // 支払い方法選択
        $token = $this->getCsrfToken('_shopping_order');
        $this->scenarioRedirectTo($Customer, [
            '_shopping_order' => [
                'Shippings' => [
                    0 => [
                        'Delivery' => 1,
                        'DeliveryTime' => 1
                    ],
                ],
                'Payment' => 1,
                'message' => $this->getFaker()->realText(),
                '_token' => $token
            ],
            ['shopping_order_mode' => 'payment']
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));
    }

    /**
     * 購入確認画面→支払い方法選択(エラー)
     */
    public function testPaymentWithError()
    {
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // 確認画面
        $this->scenarioConfirm($Customer);

        // 支払い方法選択
        $shoppingToken = $this->getCsrfToken('_shopping_order');
        $this->loginTo($Customer);
        $crawler = $this->scenarioRedirectTo($Customer, [
            '_shopping_order' => [
                'Shippings' => [
                    0 => [
                        'Delivery' => 1,
                        'DeliveryTime' => 1
                    ],
                ],
                'Payment' => 100, // payment=100 は無効な値
                'message' => $this->getFaker()->realText(),
                '_token' => $shoppingToken,
            ],
            ['shopping_order_mode' => 'payment']
        ]);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = '有効な値ではありません。';
        $this->actual = $crawler->filter('p.ec-errorMessage')->text();
        $this->verify();
    }

    /**
     * 購入確認画面→お届け先の設定
     */
    public function testShippingChangeWithPost()
    {
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // 確認画面
        $crawler = $this->scenarioConfirm($Customer);

        // お届け先指定画面
        $shipping_url = $crawler->filter('div.ec-orderDelivery__change > a')->attr('href');
        $this->scenarioComplete($Customer, $shipping_url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 購入確認画面→お届け先の設定→お届け先一覧
     */
    public function testShippingShipping()
    {
        $Customer = $this->logIn();

        // カート画面
        $this->scenarioCartIn($Customer);

        // 確認画面
        $crawler = $this->scenarioConfirm($Customer);

        // お届け先指定画面
        //*[@id="shopping-form"]/div/div[1]/div[3]/div[2]/div/a
        #shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div.ec-orderDelivery__title > div > a
        $shipping_url = $crawler->filter('div.ec-orderDelivery__change > a')->attr('href');
        $crawler = $this->scenarioComplete($Customer, $shipping_url);

        $shipping_url = str_replace('shipping_change', 'shipping', $shipping_url);

        // お届け先一覧
        $this->loginTo($Customer);
        $crawler = $this->client->request(
            'GET',
            $shipping_url
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'お届け先の指定';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();
    }

    /**
     * 購入確認画面→お届け先の設定→お届け先追加→購入完了
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1305
     */
    public function testShippingShippingPost()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;

        // カート画面
        $this->scenarioCartIn($Customer);
        // 確認画面
        $crawler = $this->scenarioConfirm($Customer);
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

        // https://github.com/EC-CUBE/ec-cube/issues/1305
        $this->assertRegexp('/111-111-111/', $this->parseMailCatcherSource($Message), '変更した FAX 番号が一致するか');
        $this->assertRegexp('/222-222-222/', $this->parseMailCatcherSource($Message), '変更した 電話番号が一致するか');
    }

    /**
     * @link https://github.com/EC-CUBE/ec-cube/issues/1280
     */
    public function testShippingEditTitle()
    {
        // FIXME ShoppingController の登録チェックが実装されたら有効にする
        $this->markTestIncomplete('ShoppingController is not implemented.');
        $Customer = $this->createCustomer();
        $this->logIn();
        $client = $this->client;
        $this->scenarioCartIn($Customer);

        /** @var $crawler Crawler*/
        $crawler = $this->scenarioConfirm($Customer);
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $shippingCrawler = $crawler->filter('#shipping_confirm_box--0');
        $url = $shippingCrawler->selectLink('変更')->link()->getUri();
        $url = str_replace('shipping_change', 'shipping_edit', $url);

        // Get shipping edit
        $crawler = $client->request('GET', $url);

        // Title
        $this->assertContains('お届け先の追加', $crawler->html());
    }
}
