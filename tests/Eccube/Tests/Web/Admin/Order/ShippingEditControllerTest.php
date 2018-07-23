<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Order;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Repository\ShippingRepository;
use Eccube\Common\Constant;
use Eccube\Entity\Shipping;
use Eccube\Entity\OrderItem;

class ShippingEditControllerTest extends AbstractAdminWebTestCase
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
        $this->shippingRepository = $this->container->get(ShippingRepository::class);
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

        $form = $crawler->selectButton('出荷情報を登録')->form();

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $info = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-primary')->text();
        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('出荷情報を登録しました。', $success);
        $this->assertContains('出荷に関わる情報が変更されました：送料の変更が必要な場合は、受注管理より手動で変更してください。', $info);
    }

    public function testEditAddShippingDate()
    {
        $this->markTestSkipped('出荷日は更新不可のためスキップ');
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();

        $this->assertNull($Shipping->getShippingDate());
        $arrFormData = $this->createShippingForm($Order);

        $date = new \DateTime();
        $arrFormData[0]['shipping_date'] = $date->format('Y-m-d');

        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_edit', ['id' => $Order->getId()]),
            [
                'shippings' => $arrFormData,
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $Messages = $this->getMailCollector(false)->getMessages();
        self::assertEquals(0, count($Messages));

        $crawler = $this->client->followRedirect();

        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('出荷情報を登録しました。', $success);

        $this->assertNotNull($Shipping->getShippingDate());
    }

    public function testEditAddShippingDateWithNotifyMail()
    {
        $this->markTestSkipped('出荷日は更新不可のためスキップ');
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();

        $this->assertNull($Shipping->getShippingDate());
        $arrFormData = $this->createShippingForm($Shipping);

        $date = new \DateTime();
        $arrFormData['shipping_date'] = $date->format('Y-m-d');

        $arrFormData['notify_email'] = 'on';
        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_edit', ['id' => $Shipping->getId()]),
            [
                'shipping' => $arrFormData,
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $Messages = $this->getMailCollector(false)->getMessages();
        self::assertEquals(1, count($Messages));
        /** @var \Swift_Message $Message */
        $Message = $Messages[0];

        self::assertRegExp('/\[.*?\] 商品出荷のお知らせ/', $Message->getSubject());
        self::assertEquals([$Order->getEmail() => null], $Message->getTo());

        $crawler = $this->client->followRedirect();

        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('出荷情報を登録しました。', $success);

        $this->assertNotNull($Shipping->getShippingDate());
    }

    public function testEditRemoveShippingDate()
    {
        $this->markTestSkipped('出荷日は更新不可のためスキップ');

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $Shipping->setShippingDate(new \DateTime());
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->assertNotNull($Shipping->getShippingDate());
        $arrFormData = $this->createShippingForm($Shipping);

        $arrFormData['shipping_date'] = '';

        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_edit', ['id' => $Shipping->getId()]),
            [
                'shipping' => $arrFormData,
            ]
        );
        $crawler = $this->client->followRedirect();

        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('出荷情報を登録しました。', $success);

        $this->assertNull($Shipping->getShippingDate());
    }

    /**
     * @param Order $Order
     *
     * @return array
     */
    private function createShippingForm(Order $Order = null): array
    {
        $arrFormData = [
            Constant::TOKEN_NAME => 'dummy',
            ];

        if ($Order instanceof Order && $Order->getId()) {
            $Shippings = $Order->getShippings();

            foreach ($Shippings as $key => $Shipping) {
                $shipping = [
                    'name' => [
                        'name01' => $Shipping->getName01(),
                        'name02' => $Shipping->getName02(),
                    ],
                    'kana' => [
                        'kana01' => $Shipping->getKana01(),
                        'kana02' => $Shipping->getKana02(),
                    ],
                    'company_name' => $Shipping->getCompanyName(),
                    'postal_code' => $Shipping->getPostalCode(),
                    'address' => [
                        'pref' => $Shipping->getPref()->getId(),
                        'addr01' => $Shipping->getAddr01(),
                        'addr02' => $Shipping->getAddr02(),
                    ],
                    'phone_number' => $Shipping->getPhoneNumber(),
                    'Delivery' => $Shipping->getDelivery()->getId(),
                    'OrderItems' => [],
                ];

                /** @var OrderItem $OrderItem */
                foreach ($Shipping->getOrderItems() as $OrderItem) {
                    $shipping['OrderItems'][$OrderItem->getId()]['id'] = $OrderItem->getId();
                }

                $arrFormData[$key] = $shipping;
            }
        }

        return $arrFormData;
    }
}
