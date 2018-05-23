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

use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\ShippingStatus;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\OrderItemRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class ShippingControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    public function setUp()
    {
        parent::setUp();

        $this->shippingRepository = $this->container->get(ShippingRepository::class);

        // FIXME: Should remove exist data before generate data for test
        $this->deleteAllRows(['dtb_order']);

        $Sex = $this->container->get(SexRepository::class)->find(1);
        $Payment = $this->container->get(PaymentRepository::class)->find(1);
        $OrderStatus = $this->container->get(OrderStatusRepository::class)->find(OrderStatus::NEW);
        for ($i = 0; $i < 10; $i++) {
            $Customer = $this->createCustomer('user-'.$i.'@example.com');
            $Customer->setSex($Sex);
            $Order = $this->createOrder($Customer);
            $Order->setOrderStatus($OrderStatus);
            $Order->setPayment($Payment);
            $this->entityManager->flush();
        }

        // sqlite では CsvType が生成されないので、ここで作る
        $OrderCsvType = $this->container->get(CsvTypeRepository::class)->find(3);
        if (!is_object($OrderCsvType)) {
            $OrderCsvType = new CsvType();
            $OrderCsvType->setId(3);
            $OrderCsvType->setName('受注CSV');
            $OrderCsvType->setSortNo(4);
            $this->entityManager->persist($OrderCsvType);
            $this->entityManager->flush();
        }
        $ShipCsvType = $this->container->get(CsvTypeRepository::class)->find(4);
        if (!is_object($ShipCsvType)) {
            $ShipCsvType = new CsvType();
            $ShipCsvType->setId(4);
            $ShipCsvType->setName('配送CSV');
            $ShipCsvType->setSortNo(5);
            $this->entityManager->persist($ShipCsvType);
            $this->entityManager->flush();
        }
    }

    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexInitial()
    {
        // 初期表示時検索条件テスト
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果 : 10 件が該当しました';
        $this->actual = $crawler->filter('#search_form .c-outsideBlock__contents.mb-3 span')->text();
        $this->verify();
    }

    public function testSearchOrderByName()
    {
        $faker = $this->getFaker();
        /** @var Shipping $Shipping */
        $Shipping = $this->shippingRepository->findOneBy([]);
        $name = $Shipping->getName01();
        $name .= $faker->realText(10);
        $Shipping->setName01($name);
        $this->entityManager->flush($Shipping);

        $Shippings = $this->shippingRepository->findBy(['name01' => $name]);
        $cnt = count($Shippings);

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_shipping'), [
                'admin_search_shipping' => [
                    '_token' => 'dummy',
                    'multi' => $name,
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果 : '.$cnt.' 件が該当しました';
        $this->actual = $crawler->filter('#search_form .c-outsideBlock__contents.mb-3 span')->text();
        $this->verify();

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_shipping'), [
                'admin_search_shipping' => [
                    '_token' => 'dummy',
                    'name' => $name,
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果 : '.$cnt.' 件が該当しました';
        $this->actual = $crawler->filter('#search_form .c-outsideBlock__contents.mb-3 span')->text();
        $this->verify();
    }

    public function testIndexWithNext()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping').'?page_count=30',
            [
                'admin_search_shipping' => [
                    '_token' => 'dummy',
                ],
            ]
        );

        // 次のページへ遷移
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping_page', ['page_no' => 2])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果 : 10 件が該当しました';
        $this->actual = $crawler->filter('#search_form .c-outsideBlock__contents.mb-3 span')->text();
        $this->verify();
    }

    public function testBulkDelete()
    {
        $shippingIds = [];
        $orderItemIds = [];

        $Customer = $this->createCustomer();

        for ($i = 0; $i < 5; $i++) {
            $Order = $this->createOrder($Customer);

            $Shippings = $Order->getShippings();

            /** @var Shipping $Shipping */
            foreach ($Shippings as $Shipping) {
                $shippingIds[] = $Shipping->getId();

                $OrderItems = $Shipping->getOrderItems();

                /** @var OrderItem $OrderItem */
                foreach ($OrderItems as $OrderItem) {
                    $orderItemIds[] = $OrderItem->getId();
                }
            }
        }

        $this->entityManager->flush();

        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_bulk_delete'),
            ['ids' => $shippingIds]
        );

        $Shippings = $this->container->get(ShippingRepository::class)->findBy(['id' => $shippingIds]);
        $this->assertCount(0, $Shippings);

        $OrderItems = $this->container->get(OrderItemRepository::class)->findBy(['id' => $orderItemIds]);
        /** @var OrderItem $OrderItem */
        foreach ($OrderItems as $OrderItem) {
            $this->assertNull($OrderItem->getShipping());
        }
    }

    public function testExportShipping()
    {
        // 受注件数を11件にしておく
        $Order = $this->createOrder($this->createCustomer('dummy-user@example.com'));
        $OrderStatus = $this->container->get(OrderStatusRepository::class)->find(OrderStatus::NEW);
        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->flush();

        // 10件ヒットするはずの検索条件
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order'),
            [
                'admin_search_order' => [
                    '_token' => 'dummy',
                    'email' => 'user-',
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();

        $this->expectOutputRegex('/user-[0-9]@example.com/', 'user-[0-9]@example.com が含まれる CSV が出力されるか');

        $this->client->request(
            'GET',
            $this->generateUrl('admin_order_export_shipping')
        );
    }

    public function testMarkAsShipped()
    {
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $Shipping->setShippingStatus($this->entityManager->find(ShippingStatus::class, ShippingStatus::PREPARED));
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->client->request(
            'PUT',
            $this->generateUrl('admin_shipping_mark_as_shipped', ['id' => $Shipping->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $Messages = $this->getMailCollector(false)->getMessages();
        self::assertEquals(0, count($Messages));
    }

    public function testMarkAsShipped_sendNotifyMail()
    {
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $Shipping->setShippingStatus($this->entityManager->find(ShippingStatus::class, ShippingStatus::PREPARED));
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->client->request(
            'PUT',
            $this->generateUrl('admin_shipping_mark_as_shipped', ['id' => $Shipping->getId()]),
            ['notificationMail' => 'on']
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $Messages = $this->getMailCollector(false)->getMessages();
        self::assertEquals(1, count($Messages));

        /** @var \Swift_Message $Message */
        $Message = $Messages[0];

        self::assertRegExp('/\[.*?\] 商品出荷のお知らせ/', $Message->getSubject());
        self::assertEquals([$Order->getEmail() => null], $Message->getTo());
    }

    public function testSendNotifyMail()
    {
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $Shipping->setShippingStatus($this->entityManager->find(ShippingStatus::class, ShippingStatus::SHIPPED));
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
}
