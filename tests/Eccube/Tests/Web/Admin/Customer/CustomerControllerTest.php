<?php

namespace Eccube\Tests\Web\Admin\Customer;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Entity\Master\CsvType;

/**
 * Class CustomerControllerTest
 * @package Eccube\Tests\Web\Admin\Customer
 */
class CustomerControllerTest extends AbstractAdminWebTestCase
{
    /**
     * Setup
     */
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

    /**
     * tearDown
     */
    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    /**
     * testIndex
     */
    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->app->path('admin_customer')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * testIndexPaging
     */
    public function testIndexPaging()
    {
        for ($i = 20; $i < 70; $i++) {
            $this->createCustomer('user-'.$i.'@example.com');
        }

        $this->client->request(
            'GET',
            $this->app->path('admin_customer_page', array('page_no' => 2))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * testIndezWithPost
     */
    public function testIndexWithPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->path('admin_customer'),
            array('admin_search_customer' => array('_token' => 'dummy'))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果 10 件 が該当しました';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->verify();
    }

    /**
     * testIndexWithPostSex
     */
    public function testIndexWithPostSex()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->path('admin_customer'),
            array('admin_search_customer' => array('_token' => 'dummy', 'sex' => 2))
        );
        $this->expected = '検索';
        $this->actual = $crawler->filter('h3.box-title')->text();
        $this->assertContains($this->expected, $this->actual);
    }

    /**
     * testResend
     */
    public function testResend()
    {
        $Customer = $this->createCustomer();
        $this->client->request(
            'PUT',
            $this->app->path('admin_customer_resend', array('id' => $Customer->getId()))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_customer')));

        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->expected = '['.$BaseInfo->getShopName().'] 会員登録のご確認';
        $this->actual = $Message->subject;
        $this->verify();
    }

    /**
     * testDelete
     */
    public function testDelete()
    {
        $Customer = $this->createCustomer();
        $this->client->request(
            'DELETE',
            $this->app->path('admin_customer_delete', array('id' => $Customer->getId()))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_customer_page', array('page_no' => 1)).'?resume=1'));

        $DeletedCustomer = $this->app['eccube.repository.customer']->find($Customer->getId());

        $this->expected = 1;
        $this->actual = $DeletedCustomer->getDelFlg();
        $this->verify();
    }

    /**
     * testExport
     */
    public function testExport()
    {
        $this->expectOutputRegex('/user-[0-9]@example.com/', 'user-[0-9]@example.com が含まれる CSV が出力されるか');

        $this->client->request(
            'POST',
            $this->app->path('admin_customer_export'),
            array('admin_search_customer' => array('_token' => 'dummy'))
        );
    }
}
