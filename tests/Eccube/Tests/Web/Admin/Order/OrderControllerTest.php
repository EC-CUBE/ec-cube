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

class OrderControllerTest extends AbstractAdminWebTestCase
{

    public function setUp()
    {
        parent::setUp();

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
            $this->generateUrl('admin_order')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testSearchOrderById()
    {
        $Order = $this->container->get(OrderRepository::class)->findOneBy(array());

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_order'), array(
            'admin_search_order' => array(
                '_token' => 'dummy',
                'multi' => $Order->getId(),
            )
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();
    }

    public function testSearchOrderByName()
    {
        $Order = $this->container->get(OrderRepository::class)->findOneBy(array());
        $companyName = $Order->getCompanyName();
        $OrderList = $this->container->get(OrderRepository::class)->findBy(array('company_name' => $companyName));
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

        $this->expected = '検索結果 ' . $cnt . ' 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
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

        $this->expected = '検索結果 10 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
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

        $this->expected = '検索結果 10 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
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
            $this->generateUrl(
                'admin_order_page', array('page_no' => 1)
            ).'?resume=1'
        ));

        $DeletedOrder = $this->container->get(OrderRepository::class)->find($id);

        $this->assertNull($DeletedOrder);
    }

    public function testExportOrder()
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
            array(
                'admin_search_order' => array(
                    '_token' => 'dummy',
                    'email' => 'user-'
                )
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = '検索結果 10 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        $this->expectOutputRegex('/user-[0-9]@example.com/', 'user-[0-9]@example.com が含まれる CSV が出力されるか');

        $this->client->request(
            'GET',
            $this->generateUrl('admin_order_export_order')
        );
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
            array(
                'admin_search_order' => array(
                    '_token' => 'dummy',
                    'email' => 'user-'
                )
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expected = '検索結果 10 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        $this->expectOutputRegex('/user-[0-9]@example.com/', 'user-[0-9]@example.com が含まれる CSV が出力されるか');

        $this->client->request(
            'GET',
            $this->generateUrl('admin_order_export_shipping')
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

        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        /* @var $customer \Eccube\Entity\Customer */
        $customer = $this->container->get(CustomerRepository::class)->findOneBy(array('email' => 'user-1@example.com'));

        $this->assertContains($customer->getName01(), $crawler->filter('div#result_list_main__body')->html());
    }
}
