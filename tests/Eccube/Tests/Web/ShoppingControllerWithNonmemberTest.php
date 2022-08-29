<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Service\OrderHelper;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mime\Email;

/**
 * Class ShoppingControllerWithNonmemberTest
 */
class ShoppingControllerWithNonmemberTest extends AbstractShoppingControllerTestCase
{
    use MailerAssertionsTrait;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
    }

    public function testRoutingShoppingLogin()
    {
        $crawler = $this->client->request('GET', '/shopping/login');
        $this->expected = 'ログイン';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();
    }

    public function testIndexWithCartNotFound()
    {
        // お客様情報を入力済の状態にするため, セッションにエンティティをセット.
        $session = static::getContainer()->get('session');
        $session->set(OrderHelper::SESSION_NON_MEMBER, new Customer());

        $this->client->request('GET', '/shopping');

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('cart')));
    }

    /**
     * 非会員情報入力→注文手続画面
     */
    public function testConfirmWithNonmember()
    {
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);
        $this->client->followRedirect();

        $crawler = $this->scenarioConfirm();
        $this->expected = 'ご注文手続き';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 非会員情報入力→注文手続画面→購入確認画面→完了画面
     */
    public function testCompleteWithNonmember()
    {
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);
        $this->client->followRedirect();

        $crawler = $this->scenarioConfirm();
        $this->expected = 'ご注文手続き';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        $crawler = $this->scenarioComplete(null, $this->generateUrl('shopping_confirm'));
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        $this->scenarioCheckout();
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_complete')));

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$this->BaseInfo->getShopName().'] ご注文ありがとうございます';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testNonmemberWithCartUnlock()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('shopping_nonmember'));

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('cart')));
    }

    public function testNonmemberWithCustomerLogin()
    {
        // ユーザーが会員ログイン済みの場合
        $Customer = $this->createCustomer();
        $this->scenarioCartIn($Customer);

        $this->loginTo($Customer);
        $crawler = $this->client->request('GET', $this->generateUrl('shopping_nonmember'));
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));
    }

    public function testNonmemberInput()
    {
        $this->scenarioCartIn();

        $crawler = $this->client->request('GET', $this->generateUrl('shopping_nonmember'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testNonmemberInputWithPost()
    {
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $Nonmember = static::getContainer()->get(OrderHelper::class)->getNonMember('eccube.front.shopping.nonmember');
        $this->assertNotNull($Nonmember);
        $this->assertNotNull(static::getContainer()->get('session')->get('eccube.front.shopping.nonmember.customeraddress'));

        $this->expected = $formData['name']['name01'];
        $this->actual = $Nonmember->getName01();
        $this->verify();

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));
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
            [
                '_shopping_order' => [
                    'Shippings' => [
                        0 => [
                            'Delivery' => 1,
                            'DeliveryTime' => 1,
                        ],
                    ],
                    'Payment' => 1,
                    'message' => $faker->realText(),
                    '_token' => 'dummy',
                    'mode' => 'shipping_edit_change',
                    'param' => $matches[1],
                ],
            ]
        );

        // お届け先設定画面へリダイレクト.
        $shipping_edit_url = str_replace('shipping_edit_change', 'shipping_edit', $shipping_edit_change_url);
        $this->assertTrue($client->getResponse()->isRedirect($shipping_edit_url));

        // お届け先設定画面が表示される.
        $crawler = $client->request('GET', $shipping_edit_url);
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'お届け先の変更';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->assertStringContainsString($this->expected, $this->actual);
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
            [
                '_shopping_order' => [
                    'Shippings' => [
                        0 => [
                            'Delivery' => 1,
                            'DeliveryTime' => 1,
                        ],
                    ],
                    'Payment' => 1,
                    'message' => $faker->realText(),
                    '_token' => 'dummy',
                    'mode' => 'shipping_edit_change',
                    'param' => $matches[1],
                ],
            ]
        );

        // お届け先設定画面へリダイレクト.
        $shipping_edit_url = str_replace('shipping_edit_change', 'shipping_edit', $shipping_edit_change_url);
        $this->assertTrue($client->getResponse()->isRedirect($shipping_edit_url));

        // お届け先設定画面が表示される.
        $crawler = $client->request('GET', $shipping_edit_url);
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'お届け先の変更';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->assertStringContainsString($this->expected, $this->actual);

        // お届け先設定画面で、入力値を変更しPOST送信
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        $crawler = $client->request(
            'POST',
            $shipping_edit_url,
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // ご注文完了
        $this->scenarioComplete($client, $this->app->path('shopping_confirm'));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

//        $this->assertMatchesRegularExpression('/111-111-111/', $this->parseMailCatcherSource($Message), '変更した FAX 番号が一致するか');
    }

    /**
     * 非会員情報入力→注文者情報変更
     */
    public function testCustomer()
    {
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);
        $this->client->followRedirect();

        $crawler = $this->scenarioConfirm();
        $this->expected = 'ご注文手続き';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $faker = $this->getFaker();
        $crawler = $this->client->request(
            'POST',
            '/shopping/customer',
            [
                'customer_name01' => $faker->lastName,
                'customer_name02' => $faker->firstName,
                'customer_kana01' => $faker->lastKanaName,
                'customer_kana02' => $faker->firstKanaName,
                'customer_company_name' => $faker->company,
                'customer_phone_number' => str_replace('-', '', $faker->phoneNumber),
                'customer_postal_code' => str_replace('-', '', $faker->postcode),
                'customer_pref' => '秋田県',
                'customer_addr01' => $faker->city,
                'customer_addr02' => $faker->streetAddress,
                'customer_email' => $faker->safeEmail
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
        );

        $this->assertEquals('OK', json_decode($this->client->getResponse()->getContent())->status);
    }

    /**
     * 非会員情報入力→注文者情報変更_不正電話番号
     */
    public function testCustomerNonDigitPhoneNumber()
    {
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);
        $this->client->followRedirect();

        $crawler = $this->scenarioConfirm();
        $this->expected = 'ご注文手続き';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $faker = $this->getFaker();
        $crawler = $this->client->request(
            'POST',
            '/shopping/customer',
            [
                'customer_name01' => $faker->lastName,
                'customer_name02' => $faker->firstName,
                'customer_kana01' => $faker->lastKanaName,
                'customer_kana02' => $faker->firstKanaName,
                'customer_company_name' => $faker->company,
                'customer_phone_number' => '3.0e2',
                'customer_postal_code' => str_replace('-', '', $faker->postcode),
                'customer_pref' => '秋田県',
                'customer_addr01' => $faker->city,
                'customer_addr02' => $faker->streetAddress,
                'customer_email' => $faker->safeEmail
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
        );

        $this->assertEquals('NG', json_decode($this->client->getResponse()->getContent())->status);
    }

    /**
     * 非会員情報入力→注文者情報変更_不正郵便番号
     */
    public function testCustomerNonDigitPoastalCode()
    {
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);
        $this->client->followRedirect();

        $crawler = $this->scenarioConfirm();
        $this->expected = 'ご注文手続き';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $faker = $this->getFaker();
        $crawler = $this->client->request(
            'POST',
            '/shopping/customer',
            [
                'customer_name01' => $faker->lastName,
                'customer_name02' => $faker->firstName,
                'customer_kana01' => $faker->lastKanaName,
                'customer_kana02' => $faker->firstKanaName,
                'customer_company_name' => $faker->company,
                'customer_phone_number' => str_replace('-', '', $faker->phoneNumber),
                'customer_postal_code' => '3.0e2',
                'customer_pref' => '秋田県',
                'customer_addr01' => $faker->city,
                'customer_addr02' => $faker->streetAddress,
                'customer_email' => $faker->safeEmail
            ],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'],
        );

        $this->assertEquals('NG', json_decode($this->client->getResponse()->getContent())->status);
    }

    public function createNonmemberFormData()
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $form = parent::createShippingFormData();
        $form['email'] = [
            'first' => $email,
            'second' => $email,
        ];

        return $form;
    }
}
