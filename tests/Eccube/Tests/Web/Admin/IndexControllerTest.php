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

namespace Eccube\Tests\Web\Admin;

use DateTime;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Member;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;

class IndexControllerTest extends AbstractAdminWebTestCase
{
    /** @var Member */
    protected $Member;

    /** @var OrderStatusRepository */
    protected $orderStatusRepository;

    /** @var OrderRepository */
    protected $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Member = $this->createMember();
        $this->orderStatusRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\OrderStatus::class);
        $this->orderRepository = $this->entityManager->getRepository(\Eccube\Entity\Order::class);
    }

    public function testRoutingAdminIndex()
    {
        $this->client->request('GET', $this->generateUrl('admin_homepage'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminChangePassword()
    {
        $this->client->request('GET', $this->generateUrl('admin_change_password'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/issues/1143
     *
     * @param int $hour
     *
     * @dataProvider indexWithSalesProvider
     */
    public function testIndexWithSales($hour)
    {
        $Customer = $this->createCustomer();
        $Today = new DateTime();
        $Today->setTime($hour, 0);
        $Yesterday = new DateTime('-1 days');

        $OrderNew = $this->orderStatusRepository->find(OrderStatus::NEW);
        $OrderPending = $this->orderStatusRepository->find(OrderStatus::PENDING);
        $OrderCancel = $this->orderStatusRepository->find(OrderStatus::CANCEL);
        $OrderProcessing = $this->orderStatusRepository->find(OrderStatus::PROCESSING);

        $todaysSales = 0;
        for ($i = 0; $i < 3; $i++) {
            $Order = $this->createOrder($Customer);
            $Order->setOrderStatus($OrderNew);
            $Order->setOrderDate($Today);
            $this->entityManager->flush();
            $todaysSales += $Order->getPaymentTotal();
        }
        $yesterdaysSales = 0;
        for ($i = 0; $i < 3; $i++) {
            $Order = $this->createOrder($Customer);
            $Order->setOrderStatus($OrderNew);
            $Order->setOrderDate($Yesterday);
            $this->entityManager->flush();
            $yesterdaysSales += $Order->getPaymentTotal();
        }

        // excludes
        foreach ([$OrderCancel, $OrderPending, $OrderProcessing] as $OrderStatus) {
            foreach ([$Today, $Yesterday] as $OrderDate) {
                $Order = $this->createOrder($Customer);
                $Order->setOrderStatus($OrderStatus);
                $Order->setOrderDate($OrderDate);
                $this->entityManager->flush();
            }
        }

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_homepage')
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        preg_match('/^￥([0-9,]+) \/ ([0-9]+)/u', trim($crawler->filter('#chart-statistics > div.card-body > div.row:nth-child(1) > div:nth-child(2) > div')->text()), $match);
        $this->expected = $todaysSales;
        $this->actual = str_replace(',', '', $match[1]);
        $this->verify('本日の売上');

        $this->expected = 3;
        $this->actual = str_replace(',', '', $match[2]);
        $this->verify('本日の売上件数');

        preg_match('/^￥([0-9,]+) \/ ([0-9]+)/u', trim($crawler->filter('#chart-statistics > div.card-body > div.row:nth-child(1) > div:nth-child(3) > div')->text()), $match);
        $this->expected = $yesterdaysSales;
        $this->actual = str_replace(',', '', $match[1]);
        $this->verify('昨日の売上');

        $this->expected = 3;
        $this->actual = str_replace(',', '', $match[2]);
        $this->verify('昨日の売上件数');

        preg_match('/^￥([0-9,]+) \/ ([0-9]+)/u', trim($crawler->filter('#chart-statistics > div.card-body > div.row:nth-child(1) > div:nth-child(1) > div')->text()), $match);
        $this->expected = (new DateTime('today'))->format('m') === (new DateTime('yesterday'))->format('m') ? $todaysSales + $yesterdaysSales : $todaysSales;
        $this->actual = str_replace(',', '', $match[1]);
        $this->verify('今月の売上');

        $this->expected = (new DateTime('today'))->format('m') === (new DateTime('yesterday'))->format('m') ? 6 : 3;
        $this->actual = str_replace(',', '', $match[2]);
        $this->verify('今月の売上件数');
    }

    public function indexWithSalesProvider()
    {
        return [
            [8],
            [10],
        ];
    }

    public function testChangePasswordWithPost()
    {
        $this->logIn($this->Member);
        $client = $this->client;

        $form = $this->createChangePasswordFormData();
        $current_password = $form['current_password'];
        $new_password = $form['change_password']['first'];

        $hasher = static::getContainer()->get('security.user_password_hasher');
        self::assertTrue($hasher->isPasswordValid($this->Member, $current_password));
        self::assertFalse($hasher->isPasswordValid($this->Member, $new_password));

        $client->request(
            'POST',
            $this->generateUrl('admin_change_password'),
            ['admin_change_password' => $form]
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_change_password')));

        self::assertFalse($hasher->isPasswordValid($this->Member, $current_password));
        self::assertTrue($hasher->isPasswordValid($this->Member, $new_password));
    }

    public function testChangePasswordWithPostInvalid()
    {
        $this->logIn($this->Member);
        $client = $this->client;

        $client->request(
            'POST',
            $this->generateUrl('admin_change_password'),
            []
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    protected function createChangePasswordFormData()
    {
        $faker = $this->getFaker();

        $password = $faker->lexify('????????????').'a1';

        $form = [
            'current_password' => 'password',
            'change_password' => [
                'first' => $password,
                'second' => $password,
            ],
            '_token' => 'dummy',
        ];

        return $form;
    }
}
