<?php

declare(strict_types=1);

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

use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Member;
use Eccube\Entity\Order;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class IndexControllerTest extends AbstractAdminWebTestCase
{
    protected ?Member $Member = null;

    protected ?OrderStatusRepository $orderStatusRepository = null;

    protected ?OrderRepository $orderRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Member = $this->createMember();
        $this->orderStatusRepository = $this->entityManager->getRepository(OrderStatus::class);
        $this->orderRepository = $this->entityManager->getRepository(Order::class);
    }

    public function testRoutingAdminIndex()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_homepage'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminChangePassword()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_change_password'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/issues/1143
     *
     * @param int $hour
     */
    #[DataProvider(methodName: 'indexWithSalesProvider')]
    #[Group(name: 'decimal')]
    public function testIndexWithSales($hour)
    {
        $Customer = $this->createCustomer();
        $Today = new \DateTime();
        $Today->setTime($hour, 0);
        $Yesterday = new \DateTime('-1 days');

        $OrderNew = $this->orderStatusRepository->find(OrderStatus::NEW);
        $OrderPending = $this->orderStatusRepository->find(OrderStatus::PENDING);
        $OrderCancel = $this->orderStatusRepository->find(OrderStatus::CANCEL);
        $OrderProcessing = $this->orderStatusRepository->find(OrderStatus::PROCESSING);

        // bulk 生成 → OrderDate 設定 → 1 回 flush. createOrder ループ (内部で createProduct/createDelivery が走る) を避ける.
        $todaysSales = '0';
        $todaysOrders = $this->createOrders(array_fill(0, 3, $Customer), ['orderStatus' => $OrderNew]);
        foreach ($todaysOrders as $Order) {
            $Order->setOrderDate($Today);
            $todaysSales = bcadd($todaysSales, $Order->getPaymentTotal(), 2);
        }
        $yesterdaysSales = '0';
        $yesterdaysOrders = $this->createOrders(array_fill(0, 3, $Customer), ['orderStatus' => $OrderNew]);
        foreach ($yesterdaysOrders as $Order) {
            $Order->setOrderDate($Yesterday);
            $yesterdaysSales = bcadd($yesterdaysSales, $Order->getPaymentTotal(), 2);
        }
        $this->entityManager->flush();

        // excludes: ステータス別に 2 件ずつ bulk 生成し、Today / Yesterday を割り当てる.
        foreach ([$OrderCancel, $OrderPending, $OrderProcessing] as $OrderStatus) {
            $excludeOrders = $this->createOrders(array_fill(0, 2, $Customer), ['orderStatus' => $OrderStatus]);
            $excludeOrders[0]->setOrderDate($Today);
            $excludeOrders[1]->setOrderDate($Yesterday);
        }
        $this->entityManager->flush();

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_homepage')
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        preg_match('/^￥([0-9,]+) \/ ([0-9]+)/u', trim($crawler->filter('#chart-statistics > div.card-body > div.row:nth-child(1) > div:nth-child(2) > div')->text()), $match);
        $this->expected = number_format((float) $todaysSales);
        $this->actual = $match[1];
        $this->verify('本日の売上');

        $this->expected = '3';
        $this->actual = $match[2];
        $this->verify('本日の売上件数');

        preg_match('/^￥([0-9,]+) \/ ([0-9]+)/u', trim($crawler->filter('#chart-statistics > div.card-body > div.row:nth-child(1) > div:nth-child(3) > div')->text()), $match);
        $this->expected = number_format((float) $yesterdaysSales);
        $this->actual = $match[1];
        $this->verify('昨日の売上');

        $this->expected = '3';
        $this->actual = $match[2];
        $this->verify('昨日の売上件数');

        preg_match('/^￥([0-9,]+) \/ ([0-9]+)/u', trim($crawler->filter('#chart-statistics > div.card-body > div.row:nth-child(1) > div:nth-child(1) > div')->text()), $match);
        $this->expected = number_format((float) ((new \DateTime('today'))->format('m') === (new \DateTime('yesterday'))->format('m') ? bcadd($todaysSales, $yesterdaysSales, 2) : $todaysSales));
        $this->actual = $match[1];
        $this->verify('今月の売上');

        $this->expected = (new \DateTime('today'))->format('m') === (new \DateTime('yesterday'))->format('m') ? '6' : '3';
        $this->actual = $match[2];
        $this->verify('今月の売上件数');
    }

    public static function indexWithSalesProvider(): \Iterator
    {
        yield [8];
        yield [10];
    }

    public function testChangePasswordWithPost()
    {
        $this->logIn($this->Member);
        $client = $this->client;

        $form = $this->createChangePasswordFormData();
        $current_password = $form['current_password'];
        $new_password = $form['change_password']['first'];

        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($hasher->isPasswordValid($this->Member, $current_password));
        $this->assertFalse($hasher->isPasswordValid($this->Member, $new_password));

        $client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_change_password'),
            ['admin_change_password' => $form]
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_change_password')));

        $this->assertFalse($hasher->isPasswordValid($this->Member, $current_password));
        $this->assertTrue($hasher->isPasswordValid($this->Member, $new_password));
    }

    public function testChangePasswordWithPostInvalid()
    {
        $this->logIn($this->Member);
        $client = $this->client;

        $client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_change_password'),
            []
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    protected function createChangePasswordFormData()
    {
        $faker = $this->getFaker();

        $password = $faker->lexify('????????????').'a1';

        return [
            'current_password' => 'password',
            'change_password' => [
                'first' => $password,
                'second' => $password,
            ],
            '_token' => 'dummy',
        ];
    }
}
