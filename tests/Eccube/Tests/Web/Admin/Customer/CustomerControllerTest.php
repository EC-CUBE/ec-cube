<?php

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

namespace Eccube\Tests\Web\Admin\Customer;

use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\Master\OrderStatusRepository;
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
            ['admin_search_customer' => ['_token' => 'dummy', 'sex' => [2]]]
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
        $Customer = $this->entityManager->getRepository(\Eccube\Entity\Customer::class)->findOneBy([], ['id' => 'DESC']);

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
     *
     * @dataProvider indexWithPostSearchByProductNameProvider
     */
    public function testIndexWithPostSearchByProductName(int $orderStatusId, string $expected)
    {
        $Customer = $this->entityManager->getRepository(\Eccube\Entity\Customer::class)->findOneBy([], ['id' => 'DESC']);
        $Order = $this->createOrder($Customer);

        /** @var OrderStatus $OrderStatus */
        $OrderStatus = self::$container->get(OrderStatusRepository::class)->find($orderStatusId);
        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->flush();

        $ProductName = $Order->getOrderItems()->filter(function ($OrderItems) {
            return $OrderItems->isProduct();
        })->first()->getProductName();

        $crawler = $this->client->request(
            'POST', $this->generateUrl('admin_customer'),
            ['admin_search_customer' => ['_token' => 'dummy', 'buy_product_name' => $ProductName]]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = $expected;
        $this->actual = $crawler->filter('div.c-outsideBlock__contents.mb-5 > span')->text();
        $this->verify();
    }

    /**
     * @return array[]
     */
    public function indexWithPostSearchByProductNameProvider()
    {
        return [
            [OrderStatus::NEW, '検索結果：1件が該当しました'], // 新規受付
            [OrderStatus::CANCEL, '検索結果：1件が該当しました'], // 注文取消し
            [OrderStatus::IN_PROGRESS, '検索結果：1件が該当しました'], // 対応中
            [OrderStatus::DELIVERED, '検索結果：1件が該当しました'], // 発送済み
            [OrderStatus::PAID, '検索結果：1件が該当しました'], // 入金済み
            [OrderStatus::PENDING, '検索結果：0件が該当しました'], // 決済処理中
            [OrderStatus::PROCESSING, '検索結果：0件が該当しました'], // 購入処理中
            [OrderStatus::RETURNED, '検索結果：1件が該当しました'], // 返品
        ];
    }

    /**
     * testResend
     */
    public function testResend()
    {
        $this->client->enableProfiler();
        $Customer = $this->createCustomer();
        $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_resend', ['id' => $Customer->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_customer')));

        $Messages = $this->getMailCollector(false)->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $Messages[0];

        $BaseInfo = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class)->get();
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

        $DeletedCustomer = $this->entityManager->getRepository(\Eccube\Entity\Customer::class)->find($id);

        $this->assertNull($DeletedCustomer);
    }

    /**
     * testExport
     */
    public function testExport()
    {
        $this->expectOutputRegex('/user-[0-9]@example.com/');

        $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_export'),
            ['admin_search_customer' => ['_token' => 'dummy']]
        );
    }
}
