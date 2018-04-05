<?php

namespace Eccube\Tests\Web\Admin\Shipping;

use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class ShippingControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        // FIXME: Should remove exist data before generate data for test
        $this->deleteAllRows(array('dtb_order'));

        $Sex = $this->container->get(SexRepository::class)->find(1);
        $Payment = $this->container->get(PaymentRepository::class)->find(1);
        $OrderStatus = $this->container->get(OrderStatusRepository::class)->find(OrderStatus::NEW);
        for ($i = 0; $i < 10; $i++) {
            $Customer = $this->createCustomer('user-' . $i . '@example.com');
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
        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('#search_form #search_total_count')->text();
        $this->verify();

        $this->expectOutputRegex('/user-[0-9]@example.com/', 'user-[0-9]@example.com が含まれる CSV が出力されるか');

        $this->client->request(
            'GET',
            $this->generateUrl('admin_order_export_shipping')
        );
    }
}
