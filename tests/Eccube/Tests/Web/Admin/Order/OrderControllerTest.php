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

use Eccube\Common\Constant;
use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class OrderControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var SexRepository
     */
    protected $sexRepository;

    /**
     * @var CsvTypeRepository
     */
    protected $csvTypeRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function setUp()
    {
        parent::setUp();

        $this->orderStatusRepository = $this->entityManager->getRepository(OrderStatus::class);
        $this->paymentRepository = $this->entityManager->getRepository(\Eccube\Entity\Payment::class);
        $this->sexRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\Sex::class);
        $this->csvTypeRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\CsvType::class);
        $this->orderRepository = $this->entityManager->getRepository(\Eccube\Entity\Order::class);
        $this->customerRepository = $this->entityManager->getRepository(\Eccube\Entity\Customer::class);

        // FIXME: Should remove exist data before generate data for test
        $this->deleteAllRows(['dtb_order_item']);
        $this->deleteAllRows(['dtb_shipping']);
        $this->deleteAllRows(['dtb_order']);

        $Sex = $this->sexRepository->find(1);
        $Payment = $this->paymentRepository->find(1);
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
        for ($i = 0; $i < 10; $i++) {
            $Customer = $this->createCustomer('user-'.$i.'@example.com');
            $Customer->setSex($Sex);
            $Order = $this->createOrder($Customer);
            $Order->setOrderNo('order_no_'.$i);
            $Order->setOrderStatus($OrderStatus);
            $Order->setPayment($Payment);
            $this->entityManager->flush();
        }

        // sqlite では CsvType が生成されないので、ここで作る
        $OrderCsvType = $this->csvTypeRepository->find(3);
        if (!is_object($OrderCsvType)) {
            $OrderCsvType = new CsvType();
            $OrderCsvType->setId(3);
            $OrderCsvType->setName('受注CSV');
            $OrderCsvType->setSortNo(4);
            $this->entityManager->persist($OrderCsvType);
            $this->entityManager->flush();
        }
        $ShipCsvType = $this->csvTypeRepository->find(4);
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
            $this->generateUrl('admin_order')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexInitial()
    {
        // 初期表示時検索条件テスト
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_order')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }

    public function testSearchOrderByOrderNo()
    {
        $Order = $this->orderRepository->findOneBy([]);

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_order'), [
            'admin_search_order' => [
                '_token' => 'dummy',
                'multi' => $Order->getOrderNo(),
            ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_order'), [
                'admin_search_order' => [
                    '_token' => 'dummy',
                    'order_no' => $Order->getOrderNo(),
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }

    public function testSearchOrderByName()
    {
        $Order = $this->orderRepository->findOneBy([]);
        $companyName = $Order->getCompanyName();
        $OrderList = $this->orderRepository->findBy(['company_name' => $companyName]);
        $cnt = count($OrderList);

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_order'), [
            'admin_search_order' => [
                '_token' => 'dummy',
                'multi' => $companyName,
            ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：'.$cnt.'件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_order'), [
                'admin_search_order' => [
                    '_token' => 'dummy',
                    'company_name' => $companyName,
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：'.$cnt.'件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }

    public function testIndexWithPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order'),
            [
                'admin_search_order' => [
                    '_token' => 'dummy',
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }

    public function testIndexWithNext()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order').'?page_count=3',
            [
                'admin_search_order' => [
                    '_token' => 'dummy',
                    'status' => 1,
                    'sex' => ['1', '2'],
                    'payment' => ['1', '2', '3', '4'],
                ],
            ]
        );

        // 次のページへ遷移
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_order_page', ['page_no' => 2])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }

    public function testBulkDelete()
    {
        $orderIds = [];
        $Customer = $this->createCustomer();
        for ($i = 0; $i < 5; $i++) {
            $Order = $this->createOrder($Customer);
            $orderIds[] = $Order->getId();
        }

        $this->entityManager->flush();

        $this->client->request(
            'POST',
            $this->generateUrl('admin_order_bulk_delete'),
            ['ids' => $orderIds]
        );

        $Orders = $this->entityManager->getRepository(\Eccube\Entity\Order::class)->findBy(['id' => $orderIds]);
        $this->assertCount(0, $Orders);
    }

    public function testExportOrder()
    {
        // 受注件数を11件にしておく
        $Order = $this->createOrder($this->createCustomer('dummy-user@example.com'));
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
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
            $this->generateUrl('admin_order_export_order')
        );
    }

    /**
     * Test for issue 1995
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1995
     */
    public function testSearchWithEmail()
    {
        $form = [
            '_token' => 'dummy',
            'email' => 'user-1',
        ];
        /* @var $crawler Crawler */
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order'),
            [
                'admin_search_order' => $form,
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();

        /* @var $customer \Eccube\Entity\Customer */
        $customer = $this->customerRepository->findOneBy(['email' => 'user-1@example.com']);

        $this->assertContains($customer->getName01(), $crawler->filter('table#search_result')->html());
    }

    /**
     * @param int $orderStatusId
     *
     * @dataProvider dataBulkOrderStatusProvider
     */
    public function testBulkOrderStatus($orderStatusId)
    {
        $this->markTestIncomplete('使用していないルーティングのためスキップ.');
        // case true
        $orderIds = [];
        /** @var Order[] $Orders */
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
        $Orders = $this->orderRepository->findBy(['OrderStatus' => $OrderStatus], [], 2);
        foreach ($Orders as $Order) {
            $this->assertEquals(null, $Order->getPaymentDate());
            $orderIds[] = $Order->getId();
            $Shippings = $Order->getShippings();
            foreach ($Shippings as $Shipping) {
                $this->client->request(
                    'PUT',
                    $this->generateUrl('admin_shipping_update_order_status', ['id' => $Shipping->getId()]),
                    [
                        'order_status' => $orderStatusId,
                        Constant::TOKEN_NAME => 'dummy',
                    ],
                    [],
                    [
                        'HTTP_X-Requested-With' => 'XMLHttpRequest',
                        'CONTENT_TYPE' => 'application/json',
                    ]
                );

                $this->assertTrue($this->client->getResponse()->isSuccessful());
            }
        }

        $result = $this->orderRepository->findBy(['id' => $orderIds, 'OrderStatus' => $OrderStatus]);
        if ($orderStatusId == OrderStatus::PAID) {
            foreach ($result as $Order) {
                $this->assertNotNull($Order->getPaymentDate());
            }
        }

        $this->assertEquals(count($orderIds), count($result));
    }

    /**
     * @return array
     */
    public function dataBulkOrderStatusProvider()
    {
        return [
            [OrderStatus::PAID],
            [OrderStatus::DELIVERED],
        ];
    }

    public function testBulkOrderStatusInvalidMethod()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping_update_order_status', ['id' => 1]),
            [
                Constant::TOKEN_NAME => 'dummy',
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }

    public function testBulkOrderStatusInvalidStatus()
    {
        $Order = $this->orderRepository->findOneBy([]);
        $Shipping = $Order->getShippings()->first();
        $this->client->request(
            'PUT',
            $this->generateUrl('admin_shipping_update_order_status', ['id' => $Shipping->getId()]),
            [
                'order_status' => 0,
                Constant::TOKEN_NAME => 'dummy',
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testBulkOrderStatusShippingNotFound()
    {
        $this->client->request(
            'PUT',
            $this->generateUrl('admin_shipping_update_order_status', ['id' => 0]),
            [
                'order_status' => OrderStatus::IN_PROGRESS,
                Constant::TOKEN_NAME => 'dummy',
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testSimpleUpdateOrderStatusWithSendMail()
    {
        $orderIds = [];
        /** @var Order[] $Orders */
        $OrderStatusNew = $this->orderStatusRepository->find(OrderStatus::NEW);
        $OrderStatusDelivered = $this->orderStatusRepository->find(OrderStatus::DELIVERED);
        $Orders = $this->orderRepository->findBy(['OrderStatus' => $OrderStatusNew], [], 2);
        foreach ($Orders as $Order) {
            $this->assertEquals(null, $Order->getPaymentDate());
            $orderIds[] = $Order->getId();
            $Shippings = $Order->getShippings();
            foreach ($Shippings as $Shipping) {
                $this->client->enableProfiler();

                $this->client->request(
                    'PUT',
                    $this->generateUrl('admin_shipping_update_order_status', ['id' => $Shipping->getId()]),
                    [
                        'order_status' => $OrderStatusDelivered,
                        'notificationMail' => 'on',
                        Constant::TOKEN_NAME => 'dummy',
                    ],
                    [],
                    [
                        'HTTP_X-Requested-With' => 'XMLHttpRequest',
                        'CONTENT_TYPE' => 'application/json',
                    ]
                );

                $this->assertTrue($this->client->getResponse()->isSuccessful());

                $Messages = $this->getMailCollector(false)->getMessages();
                $this->assertEquals(1, count($Messages));

                /** @var \Swift_Message $Message */
                $Message = $Messages[0];

                $this->assertRegExp('/\[.*?\] 商品出荷のお知らせ/', $Message->getSubject());
                $this->assertEquals([$Order->getEmail() => null], $Message->getTo());
            }
        }

        $result = $this->orderRepository->findBy(['id' => $orderIds, 'OrderStatus' => $OrderStatusDelivered]);
        foreach ($result as $Order) {
            $Shippings = $Order->getShippings();
            foreach ($Shippings as $Shipping) {
                $this->assertNotNull($Shipping->getShippingDate());
                $this->assertNotNull($Shipping->getMailSendDate());
            }
        }

        $this->assertEquals(count($orderIds), count($result));
    }

    public function testUpdateTrackingNumber()
    {
        $Order = $this->orderRepository->findOneBy([]);
        $Shipping = $Order->getShippings()->first();
        $crawler = $this->client->request(
            'PUT',
            $this->generateUrl('admin_shipping_update_tracking_number', ['id' => $Shipping->getId()]),
            [
                'tracking_number' => '0000-0000-0000',
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $Result = json_decode($this->client->getResponse()->getContent(), true);
        $this->expected = 'OK';
        $this->actual = $Result['status'];
        $this->verify();

        $this->expected = '0000-0000-0000';
        $this->actual = $Shipping->getTrackingNumber();
        $this->verify();
    }

    public function testUpdateTrackingNumberFailure()
    {
        $Order = $this->orderRepository->findOneBy([]);
        $Shipping = $Order->getShippings()->first();
        $crawler = $this->client->request(
            'PUT',
            $this->generateUrl('admin_shipping_update_tracking_number', ['id' => $Shipping->getId()]),
            [
                'tracking_number' => '0000_0000_0000',
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $Result = json_decode($this->client->getResponse()->getContent(), true);
        $this->expected = 'NG';
        $this->actual = $Result['status'];
        $this->verify();

        $this->expected = 'お問い合わせ番号は半角英数字かハイフンのみを入力してください。';
        $this->actual = $Result['messages'][0];
        $this->verify();
    }

    /**
     * Test for PR 5133
     *
     * @see https://github.com/EC-CUBE/ec-cube/pull/5133
     */
    public function testIndexWithOrderStatus()
    {
        // 対応中の受注を追加しておく
        $Order = $this->createOrder($this->createCustomer('dummy-user@example.com'));
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::IN_PROGRESS);
        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->flush();

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_order').'?order_status_id=4'
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }
}
