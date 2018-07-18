<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Customer;

use Eccube\Entity\Master\CsvType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class CustomerControllerTest
 */
class CustomerControllerTest extends AbstractAdminWebTestCase
{
    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();
        for ($i = 0; $i < 10; $i++) {
            $this->createCustomer('user-'.$i.'@example.com');
        }
        // sqlite では CsvType が生成されないので、ここで作る
        $CsvType = $this->entityManager->find(CsvType::class, 2);
        if (!is_object($CsvType)) {
            $CsvType = new CsvType();
            $CsvType->setId(2);
            $CsvType->setName('会員CSV');
            $CsvType->setSortNo(4);
            $this->entityManager->persist($CsvType);
            $this->entityManager->flush();
        }
    }

    /**
     * tearDown
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * testIndex
     */
    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_customer')
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
            $this->generateUrl('admin_customer_page', ['page_no' => 2])
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
            $this->generateUrl('admin_customer'),
            ['admin_search_customer' => ['_token' => 'dummy']]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：10件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();
    }

    /**
     * testIndexWithPostSex
     */
    public function testIndexWithPostSex()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_customer'),
            ['admin_search_customer' => ['_token' => 'dummy', 'sex' => 2]]
        );
        $this->expected = '検索';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->assertContains($this->expected, $this->actual);
    }

    /**
     * testIndexWithPostSearchByEmail
     */
    public function testIndexWithPostSearchByEmail()
    {
        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_customer'),
            ['admin_search_customer' => ['_token' => 'dummy', 'multi' => 'ser-7']]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();
    }

    /**
     * testIndexWithPostSearchById
     */
    public function testIndexWithPostSearchById()
    {
        $Customer = $this->container->get(CustomerRepository::class)->findOneBy([], ['id' => 'DESC']);

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_customer'),
            ['admin_search_customer' => ['_token' => 'dummy', 'multi' => $Customer->getId()]]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();
    }

    /**
     * testIndexWithPostSearchByProductName
     */
    public function testIndexWithPostSearchByProductName()
    {
        $Customer = $this->container->get(CustomerRepository::class)->findOneBy([], ['id' => 'DESC']);
        $Order = $this->createOrder($Customer);
        $ProductName = $Order->getOrderItems()->filter(function ($OrderItems) {
            return $OrderItems->isProduct();
        })->first()->getProductName();

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_customer'),
            ['admin_search_customer' => ['_token' => 'dummy', 'buy_product_name' => $ProductName]]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '検索結果：1件が該当しました';
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();
    }

    /**
     * testResend
     */
    public function testResend()
    {
        $this->client->enableProfiler();
        $Customer = $this->createCustomer();
        $this->client->request(
            'PUT',
            $this->generateUrl('admin_customer_resend', ['id' => $Customer->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_customer')));

        $Messages = $this->getMailCollector(false)->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $Messages[0];

        $BaseInfo = $this->container->get(BaseInfoRepository::class)->get();
        $this->expected = '['.$BaseInfo->getShopName().'] 会員登録のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();

        //test mail resend to 仮会員.
        $this->assertContains($BaseInfo->getEmail02(), $Message->toString());
    }

    /**
     * testDelete
     */
    public function testDelete()
    {
        $Customer = $this->createCustomer();
        $id = $Customer->getId();
        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_customer_delete', ['id' => $Customer->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_customer_page',
                ['page_no' => 1]).'?resume=1'));

        $DeletedCustomer = $this->container->get(CustomerRepository::class)->find($id);

        $this->assertNull($DeletedCustomer);
    }

    /**
     * testExport
     */
    public function testExport()
    {
        $this->expectOutputRegex('/user-[0-9]@example.com/');

        $this->client->request(
            'POST',
            $this->generateUrl('admin_customer_export'),
            ['admin_search_customer' => ['_token' => 'dummy']]
        );
    }
}
