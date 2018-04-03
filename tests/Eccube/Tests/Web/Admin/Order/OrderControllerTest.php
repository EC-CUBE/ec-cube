<?php

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Eccube\Entity\Order;
use Eccube\Common\Constant;

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

        $this->orderStatusRepository = $this->container->get(OrderStatusRepository::class);
        $this->paymentRepository = $this->container->get(PaymentRepository::class);
        $this->sexRepository = $this->container->get(SexRepository::class);
        $this->csvTypeRepository = $this->container->get(CsvTypeRepository::class);
        $this->orderRepository = $this->container->get(OrderRepository::class);
        $this->customerRepository = $this->container->get(CustomerRepository::class);

        // FIXME: Should remove exist data before generate data for test
        $this->deleteAllRows(array('dtb_order'));

        $Sex = $this->sexRepository->find(1);
        $Payment = $this->paymentRepository->find(1);
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
        for ($i = 0; $i < 10; $i++) {
            $Customer = $this->createCustomer('user-'.$i.'@example.com');
            $Customer->setSex($Sex);
            $Order = $this->createOrder($Customer);
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

    public function testSearchOrderById()
    {
        $Order = $this->orderRepository->findOneBy(array());

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_order'), array(
            'admin_search_order' => array(
                '_token' => 'dummy',
                'multi' => $Order->getId(),
            )
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_order'), array(
                'admin_search_order' => array(
                    '_token' => 'dummy',
                    'order_id' => $Order->getId(),
                )
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }

    public function testSearchOrderByName()
    {
        $Order = $this->orderRepository->findOneBy(array());
        $companyName = $Order->getCompanyName();
        $OrderList = $this->orderRepository->findBy(array('company_name' => $companyName));
        $cnt = count($OrderList);

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_order'), array(
            'admin_search_order' => array(
                '_token' => 'dummy',
                'multi' => $companyName,
            )
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：' . $cnt . '件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_order'), array(
                'admin_search_order' => array(
                    '_token' => 'dummy',
                    'company_name' => $companyName,
                )
            )
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
            array(
                'admin_search_order' => array(
                    '_token' => 'dummy'
                )
            )
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
            array(
                'admin_search_order' => array(
                    '_token' => 'dummy',
                    'status' => 1,
                    'sex' => array('1', '2'),
                    'payment' => array('1', '2', '3', '4')
                )
            )
        );

        // 次のページへ遷移
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_order_page', array('page_no' => 2))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();
    }


    public function testDelete()
    {
        $Order = $this->createOrder($this->createCustomer());
        $id = $Order->getId();

        // 出荷と明細の紐付けを解除してから削除する.
        $Items = $Order->getItems();
        foreach ($Items as $Item) {
            $Item->setShipping(null);
        }
        $this->entityManager->flush();

        $crawler = $this->client->request(
            'DELETE',
            $this->generateUrl('admin_order_delete', array('id' => $Order->getId()))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('admin_order', array('resume' => 1))
        ));

        $DeletedOrder = $this->orderRepository->find($id);

        $this->assertNull($DeletedOrder);
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
            array(
                'admin_search_order' => array(
                    '_token' => 'dummy',
                    'email' => 'user-'
                )
            )
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
     * @link https://github.com/EC-CUBE/ec-cube/issues/1995
     */
    public function testSearchWithEmail()
    {
        $form = array(
            '_token' => 'dummy',
            'email' => 'user-1',
        );
        /* @var $crawler Crawler */
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order'),
            array(
                'admin_search_order' => $form,
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();

        /* @var $customer \Eccube\Entity\Customer */
        $customer = $this->customerRepository->findOneBy(array('email' => 'user-1@example.com'));

        $this->assertContains($customer->getName01(), $crawler->filter('table#search_result')->html());
    }

    /**
     * @param int $orderStatusId
     *
     * @dataProvider dataBulkOrderStatusProvider
     */
    public function testBulkOrderStatus($orderStatusId)
    {
        // case true
        $orderIds = [];
        /** @var Order[] $Orders */
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
        $Orders = $this->orderRepository->findBy(['OrderStatus' => $OrderStatus], [], 2);
        foreach ($Orders as $Order) {
            $orderIds[] = $Order->getId();
            $this->assertEquals(null, $Order->getShippingDate());
            $this->assertEquals(null, $Order->getPaymentDate());
        }

        $OrderStatus = $this->orderStatusRepository->find($orderStatusId);
        $this->client->request(
            'POST',
            $this->generateUrl('admin_order_bulk_order_status', ['id' => $orderStatusId]),
            [
                'ids' => $orderIds,
                Constant::TOKEN_NAME => 'dummy'
            ]
        );

        $result = $this->orderRepository->findBy(['id' => $orderIds, 'OrderStatus' => $OrderStatus]);
        if ($orderStatusId == OrderStatus::PAID) {
            foreach ($result as $Order) {
                $this->assertNotNull($Order->getPaymentDate());
            }
        }

        if ($orderStatusId == OrderStatus::DELIVERED) {
            foreach ($result as $Order) {
                $this->assertNotNull($Order->getShippingDate());
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
            [OrderStatus::DELIVERED]
        ];
    }

    public function testBulkOrderStatusInvalidMethod()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_order_bulk_order_status', ['id' => OrderStatus::NEW]),
            [
                Constant::TOKEN_NAME => 'dummy'
            ]
        );
        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }

    public function testBulkOrderStatusInvalidStatus()
    {
        $this->client->request(
            'POST',
            $this->generateUrl('admin_order_bulk_order_status', ['id' => 0]),
            [
                Constant::TOKEN_NAME => 'dummy'
            ]
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
