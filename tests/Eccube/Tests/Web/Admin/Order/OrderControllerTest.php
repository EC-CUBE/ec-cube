<?php

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

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
    }

    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_order')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
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
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order')));

        $DeletedOrder = $this->app['eccube.repository.order']->find($Order->getId());

        $this->expected = 1;
        $this->actual = $DeletedOrder->getDelFlg();
        $this->verify();
    }

    public function testExportOrder()
    {
        $this->expectOutputRegex('/user-[0-9]@example.com/', 'user-[0-9]@example.com が含まれる CSV が出力されるか');

        $this->client->request(
            'POST',
            $this->app->path('admin_order_export_order'),
            array(
                'admin_search_customer' => array(
                    '_token' => 'dummy'
                )
            )
        );
    }

    public function testExportShipping()
    {
        $this->expectOutputRegex('/user-[0-9]@example.com/', 'user-[0-9]@example.com が含まれる CSV が出力されるか');

        $this->client->request(
            'POST',
            $this->app->path('admin_order_export_shipping'),
            array(
                'admin_search_customer' => array(
                    '_token' => 'dummy'
                )
            )
        );
    }
}
