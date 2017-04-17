<?php

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Master\CsvType;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\DomCrawler\Crawler;

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
    }

    public function testSearchOrderById()
    {
        $Order = $this->app['eccube.repository.order']->findOneBy(array());

        $crawler = $this->client->request(
            'POST', $this->app->url('admin_order'), array(
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
        $Order = $this->app['eccube.repository.order']->findOneBy(array());
        $companyName = $Order->getCompanyName();
        $OrderList = $this->app['eccube.repository.order']->findBy(array('company_name' => $companyName));
        $cnt = count($OrderList);

        $crawler = $this->client->request(
            'POST', $this->app->url('admin_order'), array(
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
            $this->app->url('admin_order'),
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
            $this->app->url('admin_order').'?page_count=3',
            array(
                'admin_search_order' => array(
                    '_token' => 'dummy',
                )
            )
        );

        // 次のページへ遷移
        $crawler = $this->client->request(
            'GET',
            $this->app->url('admin_order_page', array('page_no' => 2))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果 10 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();
    }


    public function testDelete()
    {
        $Order = $this->createOrder($this->createCustomer());

        $crawler = $this->client->request(
            'DELETE',
            $this->app->path('admin_order_delete', array('id' => $Order->getId()))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->app->url(
                'admin_order_page', array('page_no' => 1)
            ).'?resume=1'
        ));

        $DeletedOrder = $this->app['eccube.repository.order']->find($Order->getId());

        $this->expected = 1;
        $this->actual = $DeletedOrder->getDelFlg();
        $this->verify();
    }

    public function testExportOrder()
    {
        // 受注件数を11件にしておく
        $Order = $this->createOrder($this->createCustomer('dummy-user@example.com'));
        $OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $Order->setOrderStatus($OrderStatus);
        $this->app['orm.em']->flush();

        // 10件ヒットするはずの検索条件
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order'),
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
            $this->app->path('admin_order_export_order')
        );
    }

    public function testExportShipping()
    {
        // 受注件数を11件にしておく
        $Order = $this->createOrder($this->createCustomer('dummy-user@example.com'));
        $OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $Order->setOrderStatus($OrderStatus);
        $this->app['orm.em']->flush();

        // 10件ヒットするはずの検索条件
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order'),
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
            $this->app->path('admin_order_export_shipping')
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
            $this->app->url('admin_order'),
            array(
                'admin_search_order' => $form,
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果 1 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();

        /* @var $customer \Eccube\Entity\Customer */
        $customer = $this->app['eccube.repository.customer']->findOneBy(array('email' => 'user-1@example.com'));

        $this->assertContains($customer->getName01(), $crawler->filter('div#result_list_main__body')->html());
    }
}
