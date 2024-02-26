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

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Eccube\Entity\Delivery;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Entity\ProductClass;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Repository\TradeLawRepository;
use Eccube\Tests\Fixture\Generator;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mime\Email;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ShoppingControllerTest extends AbstractShoppingControllerTestCase
{
    use MailerAssertionsTrait;

    /**
     * @var BaseInfoRepository
     */
    private $baseInfoRepository;

    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var EntityRepository|ObjectRepository|TradeLawRepository
     */
    private $tradeLawRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseInfoRepository = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class);
        $this->paymentRepository = $this->entityManager->getRepository(\Eccube\Entity\Payment::class);
        $this->tradeLawRepository = $this->entityManager->getRepository(\Eccube\Entity\TradeLaw::class);
    }

    public function testRoutingShoppingLogin()
    {
        $crawler = $this->client->request('GET', '/shopping/login');
        $this->expected = 'ログイン';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();
    }

    public function testComplete()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        static::getContainer()->get('session')->set('eccube.front.shopping.order.id', $Order->getId());
        $this->client->request('GET', $this->generateUrl('shopping_complete'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertNull(static::getContainer()->get('session')->get('eccube.front.shopping.order.id'));
    }


    /**
     * 危険なXSS htmlインジェクションが削除されたことを確認するテスト

     * 下記のものをチェックします。
     *     ・ ID属性の追加
     *     ・ <script> スクリプトインジェクション
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/5372
     * @return void
     */
    public function testCompleteWithXssInjectionAttack()
    {
        // Create a new news item for the homepage with a XSS attack (via <script> AND id attribute injection)
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setCompleteMessage("
                <div id='dangerous-id' class='safe_to_use_class'>
                    <p>注文完了テストメッセージ＃１</p>
                    <script>alert('XSS Attack')</script>
                    <a href='https://www.google.com'>safe html</a>
                </div>
        ");

        $this->entityManager->flush($Order);

        // 1つの新着情報を保存した後にホームページにアクセスする。
        // Request Homepage after saving a single news item
        static::getContainer()->get('session')->set('eccube.front.shopping.order.id', $Order->getId());
        $crawler = $this->client->request('GET', $this->generateUrl('shopping_complete'));

        // <div>タグから危険なid属性が削除されていることを確認する。
        // Find that dangerous id attributes are removed from <div> tags.
        $testNewsArea_notFoundTest = $crawler->filter('#test-news-id');
        $this->assertEquals(0, $testNewsArea_notFoundTest->count());

        // 安全なclass属性が出力されているかどうかを確認する。
        // Find if classes (which are safe) have been outputted
        $testNewsArea = $crawler->filter('.safe_to_use_class');
        $this->assertEquals(1, $testNewsArea->count());

        // 安全なHTMLが存在するかどうかを確認する
        // Find if the safe HTML exists
        $this->assertStringContainsString('<p>注文完了テストメッセージ＃１</p>', $testNewsArea->outerHtml());
        $this->assertStringContainsString('<a href="https://www.google.com">safe html</a>', $testNewsArea->outerHtml());

        // 安全でないスクリプトが存在しないかどうかを確認する
        // Find if the unsafe script does not exist
        $this->assertStringNotContainsString("<script>alert('XSS Attack')</script>", $testNewsArea->outerHtml());
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
        $crawler = $this->scenarioCheckout($Customer);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_complete')));

        $BaseInfo = $this->baseInfoRepository->get();
        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$BaseInfo->getShopName().'] ご注文ありがとうございます';
        $this->actual = $Message->getSubject();
        $this->verify();

        // 生成された受注のチェック
        $Order = $this->entityManager->getRepository(\Eccube\Entity\Order::class)->findOneBy(
            [
                'Customer' => $Customer,
            ]
        );

        $OrderNew = $this->entityManager->getRepository(\Eccube\Entity\Master\OrderStatus::class)->find(OrderStatus::NEW);
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
            '_token' => $token,
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
        $this->scenarioRedirectTo($Customer, [
            '_shopping_order' => [
                'Shippings' => [
                    0 => [
                        'Delivery' => 1,
                        'DeliveryTime' => 1,
                    ],
                ],
                'Payment' => 1,
                'use_point' => 0,
                'message' => $this->getFaker()->realText(),
                '_token' => 'dummy',
            ],
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
                        'DeliveryTime' => 1,
                    ],
                ],
                'Payment' => 1,
                'use_point' => 0,
                'message' => $this->getFaker()->realText(),
                '_token' => $token,
            ],
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

        // カート画面
        $this->scenarioCartIn($Customer);

        // 確認画面
        $this->scenarioConfirm($Customer);

        // 支払い方法選択
        $this->scenarioRedirectTo($Customer, [
            '_shopping_order' => [
                'Shippings' => [
                    0 => [
                        'Delivery' => 1,
                        'DeliveryTime' => 1,
                    ],
                ],
                'Payment' => 1,
                'use_point' => 0,
                'message' => $this->getFaker()->realText(),
                '_token' => 'dummy',
            ],
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));
    }

    /**
     * 購入確認画面→支払い方法失敗する、レイアウトヘッダーとフッター確認
     */
    public function testOrtderConfirmLayout()
    {
        $this->markTestIncomplete('ShoppingController is not implemented.');
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;

        // カート画面
        $this->scenarioCartIn($Customer);

        // 確認画面
        $crawler = $this->scenarioConfirm($Customer);

        // 支払い方法選択
        $crawler = $client->request(
            'POST',
            $this->generateUrl('shopping_payment'),
            [
                'shopping' => [
                    'shippings' => [
                        0 => [
                            'delivery' => 1,
                            'deliveryTime' => 1,
                        ],
                    ],
                    'payment' => 0,
                    'message' => $faker->text(),
                    '_token' => 'dummy',
                ],
            ]
        );
        // レイアウトヘッダーの部分確認
        $this->expected = 'header';
        $this->actual = $crawler->filter('header')->attr('id');
        $this->verify();

        // レイアウトフッターの部分確認
        $this->expected = 'footer';
        $this->actual = $crawler->filter('footer')->attr('id');
        $this->verify();

        // 確認画面で支払方法エラー表示する確認
        $this->expected = '有効な値ではありません。';
        $this->actual = $crawler->filter('P.errormsg')->text();
        $this->verify();
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
                        'DeliveryTime' => 1,
                    ],
                ],
                'Payment' => 100, // payment=100 は無効な値
                'message' => $this->getFaker()->realText(),
                '_token' => $shoppingToken,
            ],
            ['shopping_order_mode' => 'payment'],
        ]);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = 'お支払い方法を選択してください。';
        $this->actual = $crawler->filter('p.ec-errorMessage')->text();
        $this->verify();
    }

    /**
     * 購入確認画面
     */
    public function testPaymentEmpty()
    {
        $this->markTestIncomplete('ShoppingController is not implemented.');
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $client = $this->client;

        // カート画面
        $this->scenarioCartIn($Customer);

        // 支払い方法のMINとMAXルール変更
        $PaymentColl = $this->paymentRepository->findAll();
        foreach ($PaymentColl as $Payment) {
            $Payment->setRuleMin(0);
            $Payment->setRuleMax(0);
        }
        // 確認画面
        $crawler = $this->scenarioConfirm($Customer);

        $BaseInfo = $this->baseInfoRepository->get();
        $email02 = $BaseInfo->getEmail02();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->expected = '合計金額に対して可能な支払い方法がありません。'.$email02.'にお問い合わせ下さい。';
        $this->actual = $crawler->filter('p.errormsg')->text();
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
        $shippingId = $crawler->filter('div.ec-orderDelivery__change > button')->attr('data-id');
        $this->scenarioRedirectTo($Customer, [
            '_shopping_order' => [
                'Shippings' => [
                    0 => [
                        'Delivery' => 1,
                        'DeliveryTime' => 1,
                    ],
                ],
                'Payment' => 1,
                'use_point' => 0,
                'message' => $this->getFaker()->realText(),
                'redirect_to' => $this->generateUrl('shopping_shipping', ['id' => $shippingId], UrlGeneratorInterface::ABSOLUTE_PATH),
                '_token' => 'dummy',
            ],
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_shipping', ['id' => $shippingId])));
    }

    /**
     * 購入確認画面→お届け先の設定→お届け先追加→購入完了
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1305
     */
    public function testShippingShippingPost()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

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
        $formData['phone_number'] = $faker->phoneNumber;
        $crawler = $client->request(
            'POST',
            $shipping_edit_url,
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // ご注文完了
        $this->scenarioComplete($client, $this->generateUrl('shopping_confirm'));

        $BaseInfo = $this->baseInfoRepository->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        // https://github.com/EC-CUBE/ec-cube/issues/1305
        $this->assertMatchesRegularExpression('/222-222-222/', $this->parseMailCatcherSource($Message), '変更した 電話番号が一致するか');
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/issues/1280
     */
    public function testShippingEditTitle()
    {
        // FIXME ShoppingController の登録チェックが実装されたら有効にする
        $this->markTestIncomplete('ShoppingController is not implemented.');
        $Customer = $this->createCustomer();
        $this->logIn();
        $client = $this->client;
        $this->scenarioCartIn($Customer);

        /** @var $crawler Crawler */
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
        $this->assertStringContainsString('お届け先の追加', $crawler->html());
    }

    /**
     * カート→購入確認画面→完了画面(配送業者を変更する)
     */
    public function testCompleteWithChangeDeliveryName()
    {
        $Customer = $this->createCustomer();
        $SaleTypeNormal = $this->entityManager->find(SaleType::class, SaleType::SALE_TYPE_NORMAL);
        $Delivery = static::getContainer()->get(Generator::class)->createDelivery();
        $Delivery->setSaleType($SaleTypeNormal);
        $this->entityManager->flush($Delivery);
        $Payments = $this->paymentRepository->findAll();
        $this->setUpPayments($Delivery, $Payments);
        $this->entityManager->flush();

        // カート画面
        $this->scenarioCartIn($Customer);

        // 手続き画面
        $crawler = $this->scenarioConfirm($Customer);
        $this->expected = 'ご注文手続き';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        // 確認画面
        $crawler = $this->scenarioComplete(
            $Customer,
            $this->generateUrl('shopping_confirm'),
            [
                [
                    'Delivery' => $Delivery->getId(),
                    'DeliveryTime' => null,
                ],
            ]
        );

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        // 完了画面
        $crawler = $this->scenarioComplete(
            $Customer,
            $this->generateUrl('shopping_checkout'),
            [],
            true
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_complete')));

        $BaseInfo = $this->baseInfoRepository->get();
        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$BaseInfo->getShopName().'] ご注文ありがとうございます';
        $this->actual = $Message->getSubject();
        $this->verify();

        // 生成された受注のチェック
        $Order = $this->entityManager->getRepository(\Eccube\Entity\Order::class)->findOneBy(
            [
                'Customer' => $Customer,
            ]
        );

        $OrderNew = $this->entityManager->getRepository(\Eccube\Entity\Master\OrderStatus::class)->find(OrderStatus::NEW);
        $this->expected = $OrderNew;
        $this->actual = $Order->getOrderStatus();
        $this->verify();

        $this->expected = $Customer->getName01();
        $this->actual = $Order->getName01();
        $this->verify();

        $Shipping = $Order->getShippings()->first();

        $this->expected = $Delivery->getName();
        $this->actual = $Shipping->getShippingDeliveryName();
        $this->verify();
    }

    /**
     * カート→購入確認画面→完了画面(テキストメールサニタイズ)
     */
    public function testCompleteWithSanitize()
    {
        $Customer = $this->createCustomer();
        $Customer->setName01('<Sanitize&>');
        $this->entityManager->flush();

        // カート画面
        $this->scenarioCartIn($Customer);

        // 手続き画面
        $crawler = $this->scenarioConfirm($Customer);
        $this->expected = 'ご注文手続き';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        // 確認画面
        $crawler = $this->scenarioComplete(
            $Customer,
            $this->generateUrl('shopping_confirm'),
            [
                [
                    'Delivery' => 1,
                    'DeliveryTime' => null,
                ],
            ]
        );

        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('.ec-pageHeader h1')->text();
        $this->verify();

        // 完了画面
        $crawler = $this->scenarioComplete(
            $Customer,
            $this->generateUrl('shopping_checkout'),
            [],
            true
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_complete')));

        $BaseInfo = $this->baseInfoRepository->get();
        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$BaseInfo->getShopName().'] ご注文ありがとうございます';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->assertEmailTextBodyContains($Message, '＜Sanitize&＞', 'テキストメールがサニタイズされている');
        $this->assertEmailHtmlBodyContains($Message, '&lt;Sanitize&amp;&gt;', 'HTMLメールがサニタイズされている');
    }

    /**
     * 取引法を無効にすると、配信設定ページに取引法テスト文字が表示されないことを確認すること。
     * Check that with no trade law enabled, no trade law test will appear on the delivery settings page.
     * @return void
     */
    public function testDeliveryPageWithNoTradeLawsEnabled() {
        // Disable all trade laws
        $tradeLaws = $this->tradeLawRepository->findAll();
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setName(sprintf('Trade名称_%s', $id));
            $tradeLaw->setDescription(sprintf('Trade説明_%s', $id));
            $tradeLaw->setDisplayOrderScreen(false);
            $id++;
        }
        $this->entityManager->flush();

        // Create case for delivery screen to appear
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // ご注文手続きページ
        // Request delivery page
        $crawler = $this->scenarioConfirm($Customer);
        $this->assertStringNotContainsString('Trade名称', $crawler->outerHtml());
        $this->assertStringNotContainsString('Trade説明', $crawler->outerHtml());
    }

    /**
     * Check that with all trade laws enabled that trade law text will appear on the delivery settings page.
     * すべての取引法を有効にすると、取引法のテキストがご注文手続きページに表示されることを確認すること。
     * @return void
     */
    public function testDeliveryPageWithTradeLawsEnabled() {
        // Enable all trade laws
        $tradeLaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setName(sprintf('Trade名称_%s', $id));
            $tradeLaw->setDescription(sprintf('Trade説明_%s', $id));
            $tradeLaw->setDisplayOrderScreen(true);
            $id++;
        }
        $this->entityManager->flush();

        // Create case for delivery screen to appear
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // ご注文手続きページ
        // Request delivery page
        $crawler = $this->scenarioConfirm($Customer);
        $headerId = 5;

        foreach($tradeLaws as $tradeLaw) {
            $this->assertStringContainsString($tradeLaw->getDescription(), $crawler->outerHtml());
            // Check sort order
            $this->assertEquals(
                $tradeLaw->getName(),
                $crawler->filter('.ec-rectHeading')->eq($headerId)->filter('h2')->first()->text()
            );
            $headerId++;
        }
    }

    /**
     * Check that with no trade law enabled, no trade law test will appear on the delivery settings page.
     * @return void
     */
    public function testConfirmationPageWithNoTradeLawsEnabled() {
        // Disable all trade laws
        $tradeLaws = $this->tradeLawRepository->findAll();
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setName(sprintf('Trade名称_%s', $id));
            $tradeLaw->setDescription(sprintf('Trade説明_%s', $id));
            $tradeLaw->setDisplayOrderScreen(false);
            $id++;
        }
        $this->entityManager->flush();


        // Create case for delivery screen to appear
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // ご注文手続きページ
        $crawler = $this->scenarioConfirm($Customer);

        // 確認画面
        $crawler = $this->scenarioComplete(
            $Customer,
            $this->generateUrl('shopping_confirm'),
            [
                [
                    'Delivery' => 1,
                    'DeliveryTime' => null,
                ],
            ]
        );

        $this->assertStringNotContainsString('Trade名称', $crawler->outerHtml());
        $this->assertStringNotContainsString('Trade説明', $crawler->outerHtml());
    }

    /**
     * Check that with all trade laws enabled, trade law test will appear on the delivery settings page.
     * @return void
     */
    public function testConfirmationPageWithTradeLawsEnabled() {
        // Disable all trade laws
        $tradeLaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setName(sprintf('Trade名称_%s', $id));
            $tradeLaw->setDescription(sprintf('Trade説明_%s', $id));
            $tradeLaw->setDisplayOrderScreen(true);
            $id++;
        }
        $this->entityManager->flush();


        // Create case for delivery screen to appear
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // ご注文手続きページ
        $crawler = $this->scenarioConfirm($Customer);

        // 確認画面
        $crawler = $this->scenarioComplete(
            $Customer,
            $this->generateUrl('shopping_confirm'),
            [
                [
                    'Delivery' => 1,
                    'DeliveryTime' => null,
                ],
            ]
        );

        $headerId = 5;
        foreach($tradeLaws as $tradeLaw) {
            $this->assertStringContainsString($tradeLaw->getDescription(), $crawler->outerHtml());
            // Check sort order
            $this->assertEquals(
                $tradeLaw->getName(),
                $crawler->filter('.ec-rectHeading')->eq($headerId)->filter('h2')->first()->text()
            );
            $headerId++;
        }
    }

    /**
     *  Delivery Page
     * Test that no trade law data will be visible even if the display_order_screen is true
     * when name is empty or null
     *
     * @return void
     */
    public function testDeliveryPageInvalidTradeLawDataEmptyName() {
        $tradeLaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setDisplayOrderScreen(false);
            if ($id == 0) {
                $tradeLaw->setName('');
                $tradeLaw->setDescription('Trade：テスト説明');
                $tradeLaw->setDisplayOrderScreen(true);
            }
            $id++;
        }
        $this->entityManager->flush();

        // Create case for delivery screen to appear
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // ご注文手続きページ
        // Request delivery page
        $crawler = $this->scenarioConfirm($Customer);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringNotContainsString('Trade：テスト説明', $crawler->outerHtml());
    }

    /**
     * Delivery Page
     * Test that no trade law data will be visible even if the display_order_screen is true
     * when description is empty or null
     *
     * @return void
     */
    public function testDeliveryPageInvalidTradeLawDataEmptyDescription() {
        $tradeLaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setDisplayOrderScreen(false);
            if ($id == 0) {
                $tradeLaw->setName('Trade：テスト名称');
                $tradeLaw->setDescription('');
                $tradeLaw->setDisplayOrderScreen(true);
            }
            $id++;
        }
        $this->entityManager->flush();

        // Create case for delivery screen to appear
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // ご注文手続きページ
        // Request delivery page
        $crawler = $this->scenarioConfirm($Customer);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringNotContainsString('Trade：テスト名称', $crawler->outerHtml());
    }


    /**
     * Confirmation Page
     * Test that no trade law data will be visible even if the display_order_screen is true
     * when name is empty or null
     *
     * @return void
     */
    public function testConfirmationPageInvalidTradeLawDataEmptyName() {
        // Disable all trade laws
        $tradeLaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setDisplayOrderScreen(false);
            if ($id == 0) {
                $tradeLaw->setName('Trade：テスト名称');
                $tradeLaw->setDescription('');
                $tradeLaw->setDisplayOrderScreen(true);
            }
            $id++;
        }
        $this->entityManager->flush();


        // Create case for delivery screen to appear
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // ご注文手続きページ
        $crawler = $this->scenarioConfirm($Customer);

        // 確認画面
        $crawler = $this->scenarioComplete(
            $Customer,
            $this->generateUrl('shopping_confirm'),
            [
                [
                    'Delivery' => 1,
                    'DeliveryTime' => null,
                ],
            ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringNotContainsString('Trade：テスト名称', $crawler->outerHtml());
    }

    /**
     * Confirmation Page
     * Test that no trade law data will be visible even if the display_order_screen is true
     * when description is empty or null
     * @return void
     */
    public function testConfirmationPageInvalidTradeLawDataEmptyDescription() {
        // Disable all trade laws
        $tradeLaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $id = 0;
        foreach($tradeLaws as $tradeLaw) {
            $tradeLaw->setDisplayOrderScreen(false);
            if ($id == 0) {
                $tradeLaw->setName('');
                $tradeLaw->setDescription('Trade：テスト説明');
                $tradeLaw->setDisplayOrderScreen(true);
            }
            $id++;
        }
        $this->entityManager->flush();


        // Create case for delivery screen to appear
        $Customer = $this->createCustomer();

        // カート画面
        $this->scenarioCartIn($Customer);

        // ご注文手続きページ
        $crawler = $this->scenarioConfirm($Customer);

        // 確認画面
        $crawler = $this->scenarioComplete(
            $Customer,
            $this->generateUrl('shopping_confirm'),
            [
                [
                    'Delivery' => 1,
                    'DeliveryTime' => null,
                ],
            ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringNotContainsString('Trade：テスト説明', $crawler->outerHtml());
    }

    /**
     * Check can use point when has payment limit
     * https://github.com/EC-CUBE/ec-cube/issues/3916
     */
    public function testPaymentLimitAndPointCombination()
    {
        $Customer = $this->createCustomer();
        $Customer->setPoint(99999);
        $this->entityManager->flush($Customer);

        $price = 27777;
        $pointUse = 27777;
        /** @var ProductClass $ProductClass */
        $ProductClass = $this->entityManager->getRepository(\Eccube\Entity\ProductClass::class)->find(2);
        $ProductClass->setPrice02($price);
        $this->entityManager->flush($ProductClass);

        $Delivery = static::getContainer()->get(Generator::class)->createDelivery();
        $Delivery->setSaleType($ProductClass->getSaleType());
        $this->entityManager->flush($Delivery);

        $COD1 = static::getContainer()->get(Generator::class)->createPayment($Delivery, 'COD1', 0, 0, 30000);
        $COD2 = static::getContainer()->get(Generator::class)->createPayment($Delivery, 'COD2', 0, 30001, 300000);

        // カート画面
        $this->scenarioCartIn($Customer, 2);

        // 確認画面
        $this->scenarioConfirm($Customer);

        // without use point with payment: COD2
        $this->scenarioRedirectTo($Customer, [
            '_shopping_order' => [
                'Shippings' => [
                    0 => [
                        'Delivery' => $Delivery->getId(),
                        'DeliveryTime' => null,
                    ],
                ],
                'Payment' => $COD2->getId(),
                'use_point' => 0,
                'message' => $this->getFaker()->realText(),
                '_token' => 'dummy',
            ],
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));
        $crawler = $this->client->followRedirect();
        $html = $crawler->filter('body')->html();
        $this->assertStringNotContainsString($COD1->getMethod(), $html);
        $this->assertStringContainsString($COD2->getMethod(), $html);

        // use point with payment: COD1
        $this->scenarioRedirectTo($Customer, [
            '_shopping_order' => [
                'Shippings' => [
                    0 => [
                        'Delivery' => $Delivery->getId(),
                        'DeliveryTime' => null,
                    ],
                ],
                'Payment' => $COD2->getId(),
                'use_point' => $pointUse,
                'message' => $this->getFaker()->realText(),
                '_token' => 'dummy',
            ],
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));
        $crawler = $this->client->followRedirect();

        $html = $crawler->filter('body')->html();
        $this->assertStringContainsString($COD1->getMethod(), $html);
        $this->assertStringNotContainsString($COD2->getMethod(), $html);
    }

    /**
     * @param Delivery $Delivery
     * @param Payment[] $Payments
     */
    private function setUpPayments(Delivery $Delivery, array $Payments)
    {
        foreach ($Payments as $Payment) {
            $PaymentOption = new PaymentOption();
            $PaymentOption
                ->setDeliveryId($Delivery->getId())
                ->setPaymentId($Payment->getId())
                ->setDelivery($Delivery)
                ->setPayment($Payment);
            $Payment->addPaymentOption($PaymentOption);
            $this->entityManager->persist($PaymentOption);
            $this->entityManager->flush($PaymentOption);
        }
    }
}
