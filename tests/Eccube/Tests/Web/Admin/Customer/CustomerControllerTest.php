<?php

namespace Eccube\Tests\Web\Admin\Customer;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Entity\Master\CsvType;

class CustomerControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->initializeMailCatcher();
        for ($i = 0; $i < 10; $i++) {
            $this->createCustomer('user-'.$i.'@example.com');
        }
        // sqlite では CsvType が生成されないので、ここで作る
        $CsvType = $this->app['eccube.repository.master.csv_type']->find(2);
        if (!is_object($CsvType)) {
            $CsvType = new CsvType();
            $CsvType->setId(2);
            $CsvType->setName('会員CSV');
            $CsvType->setRank(4);
            $this->app['orm.em']->persist($CsvType);
            $this->app['orm.em']->flush();
        }
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->app->path('admin_customer')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexWithPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->path('admin_customer'),
            array(
                'admin_search_customer' => array(
                    '_token' => 'dummy'
                )
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果 10 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();
    }

    public function testResend()
    {
        $Customer = $this->createCustomer();
        $crawler = $this->client->request(
            'PUT',
            $this->app->path('admin_customer_resend', array('id' => $Customer->getId()))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_customer')));

        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->expected = '[' . $BaseInfo->getShopName() . '] 会員登録のご確認';
        $this->actual = $Message->subject;
        $this->verify();
    }

    public function testDelete()
    {
        $Customer = $this->createCustomer();
        $crawler = $this->client->request(
            'DELETE',
            $this->app->path('admin_customer_delete', array('id' => $Customer->getId()))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_customer')));

        $DeletedCustomer = $this->app['eccube.repository.customer']->find($Customer->getId());

        $this->expected = 1;
        $this->actual = $DeletedCustomer->getDelFlg();
        $this->verify();
    }

    public function testExport()
    {
        $this->expectOutputRegex('/user-[0-9]@example.com/', 'user-[0-9]@example.com が含まれる CSV が出力されるか');

        $this->client->request(
            'POST',
            $this->app->path('admin_customer_export'),
            array(
                'admin_search_customer' => array(
                    '_token' => 'dummy'
                )
            )
        );
    }
}
