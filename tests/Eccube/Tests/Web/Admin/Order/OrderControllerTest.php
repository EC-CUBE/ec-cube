<?php

declare(strict_types=1);

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
use Eccube\Entity\Customer;
use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\Sex;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;

final class OrderControllerTest extends AbstractAdminWebTestCase
{
    use MailerAssertionsTrait;

    protected ?OrderStatusRepository $orderStatusRepository = null;

    protected ?PaymentRepository $paymentRepository = null;

    protected ?SexRepository $sexRepository = null;

    protected ?CsvTypeRepository $csvTypeRepository = null;

    protected ?OrderRepository $orderRepository = null;

    protected ?CustomerRepository $customerRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderStatusRepository = $this->entityManager->getRepository(OrderStatus::class);
        $this->paymentRepository = $this->entityManager->getRepository(Payment::class);
        $this->sexRepository = $this->entityManager->getRepository(Sex::class);
        $this->csvTypeRepository = $this->entityManager->getRepository(CsvType::class);
        $this->orderRepository = $this->entityManager->getRepository(Order::class);
        $this->customerRepository = $this->entityManager->getRepository(Customer::class);
        // FIXME: Should remove exist data before generate data for test
        $this->deleteAllRows(['dtb_order_item']);
        $this->deleteAllRows(['dtb_shipping']);
        $this->deleteAllRows(['dtb_order']);
        // dtb_customer も CSV と重複する可能性があるため事前に削除する.
        // ※ CsvFixture::load() は内部で beginTransaction/commit を呼び DAMA の
        //   savepoint と完全には整合しないため、シナリオ間で email や secret_key の
        //   UNIQUE 制約が衝突する場合がある. setUp で先に消すことで毎テスト独立した
        //   状態から CSV を投入する.
        $this->deleteAllRows(['dtb_customer_address']);
        $this->deleteAllRows(['dtb_customer']);
        // Phase (b): order-search シナリオの CSV から Customer / Order / Shipping / OrderItem を一括投入.
        // 詳細は tests/Eccube/Tests/Fixture/csv/order-search/README.md を参照.
        // ※ OrderItem の product_id / product_class_id は NULL で、Shipping の delivery_id も NULL.
        //   これは本テストが商品参照や配送方法を要求しないための簡略化であり、実運用データとは乖離する.
        $this->loadCsvFixtures('order-search');
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
            Request::METHOD_GET,
            $this->generateUrl('admin_order')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexInitial()
    {
        // 初期表示時検索条件テスト
        $crawler = $this->client->request(
            Request::METHOD_GET,
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
        $this->assertInstanceOf(Order::class, $Order);

        $crawler = $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_order'), [
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
            Request::METHOD_POST, $this->generateUrl('admin_order'), [
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
        $this->assertInstanceOf(Order::class, $Order);
        $companyName = $Order->getCompanyName();
        $OrderList = $this->orderRepository->findBy(['company_name' => $companyName]);
        $cnt = count($OrderList);

        $crawler = $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_order'), [
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
            Request::METHOD_POST, $this->generateUrl('admin_order'), [
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
            Request::METHOD_POST,
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
            Request::METHOD_POST,
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
            Request::METHOD_GET,
            $this->generateUrl('admin_order_page', ['page_no' => 2])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }

    public function testBulkDelete()
    {
        $Customer = $this->createCustomer();
        $NewOrders = $this->createOrders(array_fill(0, 5, $Customer));
        $orderIds = array_map(static fn ($o) => $o->getId(), $NewOrders);

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_bulk_delete'),
            ['ids' => $orderIds]
        );

        $Orders = $this->entityManager->getRepository(Order::class)->findBy(['id' => $orderIds]);
        $this->assertCount(0, $Orders);
    }

    public function testExportOrder()
    {
        // 受注件数を11件にしておく
        $Order = $this->createOrder($this->createCustomer('dummy-user@example.com'));
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
        $this->assertInstanceOf(OrderStatus::class, $OrderStatus);
        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->flush();

        // 10件ヒットするはずの検索条件
        $crawler = $this->client->request(
            Request::METHOD_POST,
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

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_order_export_order')
        );

        $content = $this->client->getInternalResponse()->getContent();
        $this->assertMatchesRegularExpression('/user-[0-9]@example.com/', $content);
    }

    /**
     * ソートしてから受注CSVをダウンロードしてもエラーにならないことのテスト.
     *
     * 受注検索のクエリは Shipping を fetch join しているため, Shipping 側の列でソートすると
     * LimitSubqueryWalker が例外を投げ, CSV が最後まで出力されなかった.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6713
     */
    #[DataProvider(methodName: 'dataSortKeyProvider')]
    public function testExportOrderWithSortKey(string $sortKey): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order'),
            [
                'admin_search_order' => [
                    '_token' => 'dummy',
                    'email' => 'user-',
                    'sortkey' => $sortKey,
                    'sorttype' => 'a',
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_order_export_order')
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // ヘッダ行だけでなくデータ行が出力されていること.
        $content = $this->client->getInternalResponse()->getContent();
        $this->assertMatchesRegularExpression('/user-[0-9]@example.com/', $content);
    }

    /**
     * 配送CSVも同じクエリビルダを使うため, 同様にソート後もエラーにならないこと.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6713
     */
    #[DataProvider(methodName: 'dataSortKeyProvider')]
    public function testExportShippingWithSortKey(string $sortKey): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order'),
            [
                'admin_search_order' => [
                    '_token' => 'dummy',
                    'email' => 'user-',
                    'sortkey' => $sortKey,
                    'sorttype' => 'a',
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_order_export_shipping')
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $content = $this->client->getInternalResponse()->getContent();
        $this->assertMatchesRegularExpression('/user-[0-9]@example.com/', $content);
    }

    /**
     * @return \Iterator<int<0, max>, array{string}>
     */
    public static function dataSortKeyProvider(): \Iterator
    {
        // Shipping (to-many) の列。#6713 で報告されたエラーになる3キー。
        yield ['shipping_status'];
        yield ['tracking_number'];
        yield ['delivery'];
        // association (o.OrderStatus) のため wrap-queries の対象外。従来どおり動くこと。
        yield ['order_status'];
        // Order (to-one) の列。従来どおり動くこと。
        yield ['purchase_price'];
        // ソート未指定。
        yield [''];
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
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order'),
            [
                'admin_search_order' => $form,
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();

        /** @var Customer $customer */
        $customer = $this->customerRepository->findOneBy(['email' => 'user-1@example.com']);

        $this->assertStringContainsString($customer->getName01(), $crawler->filter('table#search_result')->html());
    }

    /**
     * @param int $orderStatusId
     */
    #[DataProvider(methodName: 'dataBulkOrderStatusProvider')]
    public function testBulkOrderStatus($orderStatusId)
    {
        // Generator が生成する Order の OrderStatus は既定で PROCESSING のため,
        // 受注一覧からの一括ステータス更新の起点となる NEW の受注をここで用意する.
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
        $this->assertInstanceOf(OrderStatus::class, $OrderStatus);
        for ($i = 0; $i < 2; $i++) {
            $Order = $this->createOrder($this->createCustomer('bulk-order-status-'.$i.'@example.com'));
            $Order->setOrderStatus($OrderStatus);
        }
        $this->entityManager->flush();

        $TargetOrderStatus = $this->orderStatusRepository->find($orderStatusId);
        $this->assertInstanceOf(OrderStatus::class, $TargetOrderStatus);

        // case true
        $orderIds = [];
        $Orders = $this->orderRepository->findBy(['OrderStatus' => $OrderStatus], [], 2);
        $this->assertCount(2, $Orders);
        foreach ($Orders as $Order) {
            $this->assertEquals(null, $Order->getPaymentDate());
            $orderIds[] = $Order->getId();
            $Shippings = $Order->getShippings();
            foreach ($Shippings as $Shipping) {
                $this->client->request(
                    Request::METHOD_PUT,
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

        // 更新後は対象の OrderStatus へ遷移している (NEW -> PAID は pay, NEW -> DELIVERED は ship).
        $result = $this->orderRepository->findBy(['id' => $orderIds, 'OrderStatus' => $TargetOrderStatus]);
        if ($orderStatusId == OrderStatus::PAID) {
            // 入金日は pay 遷移 (workflow.order.transition.pay) でのみ設定される.
            foreach ($result as $Order) {
                $this->assertInstanceOf(\DateTime::class, $Order->getPaymentDate());
            }
        }

        $this->assertCount(count($orderIds), $result);
    }

    public static function dataBulkOrderStatusProvider(): \Iterator
    {
        yield [OrderStatus::PAID];
        yield [OrderStatus::DELIVERED];
    }

    public function testBulkOrderStatusInvalidMethod()
    {
        $this->client->request(
            Request::METHOD_GET,
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
        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testBulkOrderStatusInvalidStatus()
    {
        $Order = $this->orderRepository->findOneBy([]);
        $this->assertInstanceOf(Order::class, $Order);
        $Shipping = $Order->getShippings()->first();
        $this->client->request(
            Request::METHOD_PUT,
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
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testBulkOrderStatusShippingNotFound()
    {
        $this->client->request(
            Request::METHOD_PUT,
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
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testSimpleUpdateOrderStatusWithSendMail()
    {
        $orderIds = [];
        $OrderStatusNew = $this->orderStatusRepository->find(OrderStatus::NEW);
        $OrderStatusDelivered = $this->orderStatusRepository->find(OrderStatus::DELIVERED);

        // Generator が生成する Order の OrderStatus は既定で PROCESSING のため,
        // NEW の受注をここで用意する. 用意しないと findBy が 0 件を返し,
        // 以降の foreach が回らずアサーションが 1 つも実行されない (空振りで green になる).
        for ($i = 0; $i < 2; $i++) {
            $Order = $this->createOrder($this->createCustomer('simple-update-status-'.$i.'@example.com'));
            $Order->setOrderStatus($OrderStatusNew);
        }
        $this->entityManager->flush();

        $Orders = $this->orderRepository->findBy(['OrderStatus' => $OrderStatusNew], [], 2);
        $this->assertCount(2, $Orders, 'NEW の受注が用意されていること');
        foreach ($Orders as $Order) {
            $this->assertEquals(null, $Order->getPaymentDate());
            $orderIds[] = $Order->getId();
            $Shippings = $Order->getShippings();
            foreach ($Shippings as $Shipping) {
                $crawler = $this->client->request(
                    Request::METHOD_PUT,
                    $this->generateUrl('admin_shipping_update_order_status', ['id' => $Shipping->getId()]),
                    [
                        'order_status' => $OrderStatusDelivered->getId(),
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

                $this->assertEmailCount(1);
                /** @var Email $Message */
                $Message = $this->getMailerMessage(0);

                $this->assertStringContainsString('商品出荷のお知らせ', (string) $Message->getSubject());
                $this->assertEquals($Order->getEmail(), $Message->getTo()[0]->getAddress());
            }
        }

        $result = $this->orderRepository->findBy(['id' => $orderIds, 'OrderStatus' => $OrderStatusDelivered]);
        foreach ($result as $Order) {
            $Shippings = $Order->getShippings();
            foreach ($Shippings as $Shipping) {
                $this->assertInstanceOf(\DateTime::class, $Shipping->getShippingDate());
                $this->assertInstanceOf(\DateTime::class, $Shipping->getMailSendDate());
            }
        }

        $this->assertCount(count($orderIds), $result);
    }

    public function testUpdateTrackingNumber()
    {
        $Order = $this->orderRepository->findOneBy([]);
        $this->assertInstanceOf(Order::class, $Order);
        $Shipping = $Order->getShippings()->first();
        $this->client->request(
            Request::METHOD_PUT,
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
        $this->assertInstanceOf(Order::class, $Order);
        $Shipping = $Order->getShippings()->first();
        $this->client->request(
            Request::METHOD_PUT,
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
        $this->assertInstanceOf(OrderStatus::class, $OrderStatus);
        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->flush();

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_order').'?order_status_id=4'
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }
}
