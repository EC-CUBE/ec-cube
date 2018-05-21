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

namespace Eccube\Tests\Web\Admin\Shipping;

use Eccube\Entity\Order;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;
use Eccube\Entity\Master\ShippingStatus;
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
            $this->generateUrl('admin_shipping_new')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testShippingMessageNoticeWhenPost()
    {
        $Customer = $this->createCustomer();
        /** @var Order $Order */
        $Order = $this->createOrder($Customer);

        $shippingId = $Order->getShippings()->first()->getId();

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping_edit', ['id' => $shippingId])
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

    public function testNewShippingEmptyShipment()
    {
        $arrFormData = $this->createShippingForm();
        $arrFormData['ShippingStatus'] = ShippingStatus::PREPARED;
        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_new'),
            [
                'shipping' => $arrFormData,
            ]
        );

        $crawler = $this->client->followRedirect();

        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('出荷情報を登録しました。', $success);
    }

    public function testEditShippingStatusShipped()
    {
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $Shipping->setShippingStatus($this->entityManager->find(ShippingStatus::class, ShippingStatus::PREPARED));
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->assertNull($Shipping->getShippingDate());
        $arrFormData = $this->createShippingForm($Shipping);
        $arrFormData['ShippingStatus'] = ShippingStatus::SHIPPED;
        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_edit', ['id' => $Shipping->getId()]),
            [
                'shipping' => $arrFormData,
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

    public function testEditShippingStatusShippedWithNotifyMail()
    {
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $Shipping->setShippingStatus($this->entityManager->find(ShippingStatus::class, ShippingStatus::PREPARED));
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->assertNull($Shipping->getShippingDate());
        $arrFormData = $this->createShippingForm($Shipping);
        $arrFormData['ShippingStatus'] = ShippingStatus::SHIPPED;
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

    public function testEditShippingStatusPrepared()
    {
        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $Shipping->setShippingStatus($this->entityManager->find(ShippingStatus::class, ShippingStatus::SHIPPED));
        $Shipping->setShippingDate(new \DateTime());
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->assertNotNull($Shipping->getShippingDate());
        $arrFormData = $this->createShippingForm($Shipping);
        $arrFormData['ShippingStatus'] = ShippingStatus::PREPARED;
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
     * @param Shipping $Shipping
     *
     * @return array
     */
    private function createShippingForm(Shipping $Shipping = null): array
    {
        /** @var Generator $faker */
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

        if ($Shipping instanceof Shipping && $Shipping->getId()) {
            $arrFormData = [
                'name' => [
                    'name01' => $Shipping->getName01(),
                    'name02' => $Shipping->getName02(),
                ],
                'kana' => [
                    'kana01' => $Shipping->getKana01(),
                    'kana02' => $Shipping->getKana02(),
                ],
                'company_name' => $Shipping->getCompanyName(),
                'zip' => [
                    'zip01' => $Shipping->getZip01(),
                    'zip02' => $Shipping->getZip02(),
                ],
                'address' => [
                    'pref' => $Shipping->getPref()->getId(),
                    'addr01' => $Shipping->getAddr01(),
                    'addr02' => $Shipping->getAddr02(),
                ],
                'tel' => [
                    'tel01' => $Shipping->getTel01(),
                    'tel02' => $Shipping->getTel02(),
                    'tel03' => $Shipping->getTel03(),
                ],
                'fax' => [
                    'fax01' => $Shipping->getFax01(),
                    'fax02' => $Shipping->getFax02(),
                    'fax03' => $Shipping->getFax03(),
                ],
                'Delivery' => $Shipping->getDelivery()->getId(),
                'OrderItems' => [],
                Constant::TOKEN_NAME => 'dummy',
            ];
            /** @var OrderItem $OrderItem */
            foreach ($Shipping->getOrderItems() as $OrderItem) {
                $arrFormData['OrderItems'][$OrderItem->getId()]['id'] = $OrderItem->getId();
            }
        } else {
            $arrFormData = [
                'name' => [
                    'name01' => $faker->lastName,
                    'name02' => $faker->firstName,
                ],
                'kana' => [
                    'kana01' => $faker->lastKanaName,
                    'kana02' => $faker->firstKanaName,
                ],
                'company_name' => $faker->company,
                'zip' => [
                    'zip01' => $faker->postcode1(),
                    'zip02' => $faker->postcode2(),
                ],
                'address' => [
                    'pref' => $faker->numberBetween(1, 47),
                    'addr01' => $faker->city,
                    'addr02' => $faker->streetAddress,
                ],
                'tel' => [
                    'tel01' => $tel[0],
                    'tel02' => $tel[1],
                    'tel03' => $tel[2],
                ],
                'fax' => [
                    'fax01' => $tel[0],
                    'fax02' => $tel[1],
                    'fax03' => $tel[2],
                ],
                'Delivery' => 1,
                Constant::TOKEN_NAME => 'dummy',
            ];
        }

        return $arrFormData;
    }
}
