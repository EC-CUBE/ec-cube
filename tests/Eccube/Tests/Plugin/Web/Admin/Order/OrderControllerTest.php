<?php

namespace Eccube\Tests\Plugin\Web\Admin\Order;

use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\Admin\AbstractAdminWebTestCase;

class OrderControllerTest extends AbstractAdminWebTestCase
{

    public function setUp()
    {
        parent::setUp();
        $Sex = $this->app['eccube.repository.master.sex']->find(1);
        $Payment = $this->app['eccube.repository.payment']->find(1);
        $OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        for ($i = 0; $i < 10; $i++) {
            $Customer = $this->createCustomer('user-'.$i.'@example.com');
            $Customer->setSex($Sex);
            $Order = $this->createOrder($Customer);
            $Order->setOrderStatus($OrderStatus);
            $Order->setPayment($Payment);
            $this->app['orm.em']->flush();
        }

        // sqlite では CsvType が生成されないので、ここで作る
        $OrderCsvType = $this->app['eccube.repository.master.csv_type']->find(3);
        if (!is_object($OrderCsvType)) {
            $OrderCsvType = new CsvType();
            $OrderCsvType->setId(3);
            $OrderCsvType->setName('受注CSV');
            $OrderCsvType->setRank(4);
            $this->app['orm.em']->persist($OrderCsvType);
            $this->app['orm.em']->flush();
        }
        $ShipCsvType = $this->app['eccube.repository.master.csv_type']->find(4);
        if (!is_object($ShipCsvType)) {
            $ShipCsvType = new CsvType();
            $ShipCsvType->setId(4);
            $ShipCsvType->setName('配送CSV');
            $ShipCsvType->setRank(5);
            $this->app['orm.em']->persist($ShipCsvType);
            $this->app['orm.em']->flush();
        }
    }

    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_order')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE,
        );

        $this->verifyOutputString($expected);
    }

    public function testIndexWithPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order'),
            array(
                'admin_search_order' => array(
                    '_token' => 'dummy'
                )
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE,
            EccubeEvents::ADMIN_ORDER_INDEX_SEARCH,
        );

        $this->verifyOutputString($expected);
    }

    public function testIndexWithNext()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order').'?page_count=3',
            array(
                'admin_search_order' => array(
                    '_token' => 'dummy',
                    'status' => 1,
                    'sex' => array(1, 2),
                    'payment' => array(1, 2, 3, 4)
                )
            )
        );

        // 次のページへ遷移
        $crawler = $this->client->request(
            'GET',
            $this->app->url('admin_order_page', array('page_no' => 2))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            // 初回の検索処理
            EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE,
            EccubeEvents::ADMIN_ORDER_INDEX_SEARCH,
            // 次のページ遷移時の検索処理
            EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE,
            EccubeEvents::ADMIN_ORDER_INDEX_SEARCH,
        );

        $this->verifyOutputString($expected);
    }

    public function testDelete()
    {
        $Order = $this->createOrder($this->createCustomer());

        $crawler = $this->client->request(
            'DELETE',
            $this->app->path('admin_order_delete', array('id' => $Order->getId()))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order')));

        $expected = array(
            EccubeEvents::ADMIN_ORDER_DELETE_COMPLETE,
        );

        $this->verifyOutputString($expected);
    }
}
