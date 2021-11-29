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

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Shipping;
use Eccube\Repository\ShippingRepository;

class ShippingControllerTest extends AbstractEditControllerTestCase
{
    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->shippingRepository = $this->entityManager->getRepository(\Eccube\Entity\Shipping::class);
    }

    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping_edit', ['id' => '99999'])
        );
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function testShippingMessageNoticeWhenPost()
    {
        $Customer = $this->createCustomer();
        /** @var Order $Order */
        $Order = $this->createOrder($Customer);

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping_edit', ['id' => $Order->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('登録')->form();

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $info = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-primary')->text();
        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('保存しました', $success);
        $this->assertContains('出荷に関わる情報が変更されました。送料の変更が必要な場合は、受注管理より手動で変更してください。', $info);
    }

    public function testEditAddTrackingNumber()
    {
        $trackingNumber = '11111111111111111111';

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $shippingId = $Shipping->getId();

        $this->assertNull($Shipping->getTrackingNumber());

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping_edit', ['id' => $Order->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('登録')->form();
        $form['form[shippings][0][tracking_number]']->setValue($trackingNumber);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('保存しました', $success);

        $expectedShipping = $this->entityManager->find(Shipping::class, $shippingId);
        $this->assertEquals($trackingNumber, $expectedShipping->getTrackingNumber());
    }

    /**
     * 出荷先の追加と削除のテスト
     */
    public function testAddAndDeleteShipping()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $OrderId = $Order->getId();
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();

        // 編集前は出荷先が１個
        $this->assertEquals(1, $Order->getShippings()->count());

        // 出荷登録画面表示
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping_edit', ['id' => $Order->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 出荷先を追加ボタンを押下
        $form = $crawler->selectButton('登録')->form();
        $form['form[add_shipping]']->setValue('1');

        $this->client->submit($form);
        $crawler = $this->client->getCrawler();

        // 出荷登録フォームが２個に増えていることを確認
        $card1 = $crawler->filter('#form1 > div.c-contentsArea__cols > div > div > div:nth-child(1) > div.card-header > div > div.col-8 > div > span')->text();
        $this->assertContains('出荷情報(1)', $card1);
        $card2 = $crawler->filter('#form1 > div.c-contentsArea__cols > div > div > div:nth-child(2) > div.card-header > div > div.col-8 > div > span')->text();
        $this->assertContains('出荷情報(2)', $card2);

        // ２個の出荷登録フォームを作成
        $shippingFormData = $this->createShippingFormDataForEdit($Shipping);
        $newShippingFormData = $this->createShippingFormData($this->createProduct());
        $formData['shippings'] = [$shippingFormData, $newShippingFormData];
        $formData['_token'] = 'dummy';
        $formData['add_shipping'] = '';

        // 登録
        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_edit', ['id' => $Order->getId()]),
            [
                'form' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_shipping_edit', ['id' => $Order->getId()])));

        // 出荷先が２個で登録されていることを確認
        $expectedOrder = $this->entityManager->find(Order::class, $OrderId);
        $this->assertEquals(2, $expectedOrder->getShippings()->count());

        // 1個の出荷登録フォームを作成
        $formData['shippings'] = [$shippingFormData];
        $formData['_token'] = 'dummy';
        $formData['add_shipping'] = '';

        // 登録
        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_edit', ['id' => $Order->getId()]),
            [
                'form' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_shipping_edit', ['id' => $Order->getId()])));

        // 出荷先が1個で登録されていることを確認
        $expectedOrder = $this->entityManager->find(Order::class, $OrderId);
        $this->assertEquals(1, $expectedOrder->getShippings()->count());
    }

    /**
     * 出荷済みの出荷に対して出荷完了メール送信リクエストを送信する
     */
    public function testSendNotifyMail()
    {
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();

        $shippingDate = new \DateTime();
        $Shipping->setShippingDate($shippingDate);
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->client->request(
            'PUT',
            $this->generateUrl('admin_shipping_notify_mail', ['id' => $Shipping->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $Messages = $this->getMailCollector(false)->getMessages();
        self::assertEquals(1, count($Messages));

        /** @var \Swift_Message $Message */
        $Message = $Messages[0];

        self::assertRegExp('/\[.*?\] 商品出荷のお知らせ/', $Message->getSubject());
        self::assertEquals([$Order->getEmail() => null], $Message->getTo());
    }

    public function testSendNotifyMailWithSanitize()
    {
        $this->client->enableProfiler();
        $Customer = $this->createCustomer();
        $Customer->setName01('<Sanitize&>');

        $Order = $this->createOrder($Customer);
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();

        $shippingDate = new \DateTime();
        $Shipping->setShippingDate($shippingDate);
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->client->request(
            'PUT',
            $this->generateUrl('admin_shipping_notify_mail', ['id' => $Shipping->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $Messages = $this->getMailCollector(false)->getMessages();
        self::assertEquals(1, count($Messages));

        /** @var \Swift_Message $Message */
        $Message = $Messages[0];

        self::assertRegExp('/\[.*?\] 商品出荷のお知らせ/', $Message->getSubject());
        self::assertEquals([$Order->getEmail() => null], $Message->getTo());

        $this->assertContains('＜Sanitize&＞', $Message->getBody(), 'テキストメールがサニタイズされている');

        $MultiPart = $Message->getChildren();
        foreach ($MultiPart as $Part) {
            if ($Part->getContentType() == 'text/html') {
                $this->assertContains('&lt;Sanitize&amp;&gt;', $Part->getBody(), 'HTMLメールがサニタイズされている');
            }
        }
    }

    public function testNotSendNotifyMail()
    {
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();

        $this->client->request(
            'PUT',
            $this->generateUrl('admin_shipping_notify_mail', ['id' => $Shipping->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $Messages = $this->getMailCollector(false)->getMessages();
        self::assertEquals(1, count($Messages));
    }

    /**
     * 発送管理で追加した商品明細の税額が計算されている
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/4193
     */
    public function testCalculateTax()
    {
        /** @var Product $Product */
        $Product = $this->createProduct('test', 2);
        /** @var ProductClass $ProductClass1 */
        $ProductClass1 = $Product->getProductClasses()[0];
        $ProductClass1->setPrice02(1000);
        /** @var ProductClass $ProductClass2 */
        $ProductClass2 = $Product->getProductClasses()[1];
        $ProductClass2->setPrice02(2000);

        $this->entityManager->persist($Product);
        $this->entityManager->persist($ProductClass1);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->flush();

        /** @var Customer $Customer */
        $Customer = $this->createCustomer();
        $Order = $this->createOrderWithProductClasses($Customer, [$ProductClass1]);
        $Shipping = $Order->getShippings()->first();
        $this->entityManager->persist($Order);

        $shippingFormData = $this->createShippingFormDataForEdit($Shipping);
        $shippingFormData['OrderItems'][] = [
            'ProductClass' => $ProductClass2->getId(),
            'price' => $ProductClass2->getPrice02(),
            'quantity' => '1',
            'product_name' => $Product->getName(),
            'order_item_type' => OrderItemType::PRODUCT,
        ];

        $formData['shippings'] = [$shippingFormData];
        $formData['_token'] = 'dummy';
        $formData['add_shipping'] = '';

        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_edit', ['id' => $Order->getId()]),
            [
                'form' => $formData,
                'mode' => 'register',
            ]
        );

        // 税額が計算されている
        /** @var Order $Order */
        $Order = $this->entityManager->find(Order::class, $Order->getId());
        self::assertEquals(100, $Order->getProductOrderItems()[0]->getTax());
        self::assertEquals(200, $Order->getProductOrderItems()[1]->getTax());
    }
}
