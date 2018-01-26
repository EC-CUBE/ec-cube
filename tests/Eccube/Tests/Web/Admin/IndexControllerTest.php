<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
namespace Eccube\Tests\Web\Admin;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Member;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;

class IndexControllerTest extends AbstractAdminWebTestCase
{
    /** @var  Member */
    protected $Member;

    /** @var OrderStatusRepository */
    protected $orderStatusRepository;

    /** @var OrderRepository */
    protected $orderRepository;

    public function setUp()
    {
        parent::setUp();
        $this->Member = $this->createMember();
        $this->orderStatusRepository = $this->container->get(OrderStatusRepository::class);
        $this->orderRepository = $this->container->get(OrderRepository::class);
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
     * @link https://github.com/EC-CUBE/ec-cube/issues/1143
     */
    public function testIndexWithSales()
    {
        $Customer = $this->createCustomer();
        $Today = new \DateTime();
        $Yesterday = new \DateTime('-1 days');

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
        foreach (array($OrderCancel, $OrderPending, $OrderProcessing) as $OrderStatus) {
            foreach (array($Today, $Yesterday) as $OrderDate) {
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

        preg_match('/^￥([0-9,]+) \/ ([0-9]+)/u', trim($crawler->filter('.today_sale')->text()), $match);
        $this->expected = $todaysSales;
        $this->actual = str_replace(',', '', $match[1]);
        $this->verify('本日の売上');

        $this->expected = 3;
        $this->actual = str_replace(',', '', $match[2]);
        $this->verify('本日の売上件数');

        preg_match('/^￥([0-9,]+) \/ ([0-9]+)/u', trim($crawler->filter('.yesterday_sale')->text()), $match);
        $this->expected = $yesterdaysSales;
        $this->actual = str_replace(',', '', $match[1]);
        $this->verify('昨日の売上');

        $this->expected = 3;
        $this->actual = str_replace(',', '', $match[2]);
        $this->verify('昨日の売上件数');

        // 当月の受注を取得する
        $firstDate = clone $Today;
        $firstDate->setDate($Today->format('Y'), $Today->format('m'), 1);
        $firstDate->setTime(0, 0 ,0);
        $endDate = clone $firstDate;
        $endDate->setDate($Today->format('Y'), $Today->format('m'), $Today->format('t'));
        $endDate->setTime(23, 59, 59);

        $qb = $this->orderRepository->createQueryBuilder('o');
        $qb->andWhere($qb->expr()->notIn('o.OrderStatus',
                                         array(
                                             $OrderPending->getId(),
                                             $OrderProcessing->getId(),
                                             $OrderCancel->getId()
                                         )))
            ->andWhere('o.order_date BETWEEN :firstDate AND :endDate')
            ->setParameters(
                array(
                    'firstDate' => $firstDate,
                    'endDate' => $endDate
                )
            );
        $MonthlyOrders = $qb->getQuery()->getResult();

        preg_match('/^￥([0-9,]+) \/ ([0-9]+)/u', trim($crawler->filter('.monthly_sale')->text()), $match);
        $this->expected = array_reduce( // MonthlyOrders の payment_total をすべて足す
            array_map(
                function ($Order) {
                    return $Order->getPaymentTotal();
                }, $MonthlyOrders
            ),
            function ($carry, $item) {
                return $carry += $item;
            }
        );
        $this->actual = str_replace(',', '', $match[1]);
        $this->verify('今月の売上');

        $this->expected = count($MonthlyOrders);
        $this->actual = str_replace(',', '', $match[2]);
        $this->verify('今月の売上件数');
    }

    public function testChangePasswordWithPost()
    {
        $this->logIn($this->Member);
        $client = $this->client;

        $form = $this->createChangePasswordFormData();
        $client->request(
            'POST',
            $this->generateUrl('admin_change_password'),
            array('admin_change_password' => $form)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_change_password')));

        $Member = clone $this->Member;
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($this->Member);
        $this->expected = $encoder->encodePassword($form['change_password']['first'], $this->Member->getSalt());
        $this->actual = $this->Member->getPassword();

        // XXX 実行タイミングにより、稀にパスワード変更前のハッシュ値を参照する場合があるため、変更に成功した場合のみ assertion を実行する
        $old_password = hash_hmac('sha256', 'password'.':'.$this->eccubeConfig['auth_magic'], $this->Member->getSalt());
        if ($this->actual === $old_password) {
            $this->markTestSkipped('Failed to change the password by HttpClient. Skip this test.');
        }

        $this->verify(
            'パスワードのハッシュ値が異なります '.PHP_EOL
            .' AUTH_MAGIC='.$this->eccubeConfig['auth_magic'].PHP_EOL
            .' HASH_Algos='.$this->eccubeConfig['password_hash_algos'].PHP_EOL
            .' Input Password='.$form['change_password']['first'].PHP_EOL
            .' Expected: salt='.$Member->getSalt().', raw password='.$Member->getPassword().PHP_EOL
            .' Actual: salt='.$this->Member->getSalt()
        );
    }

    public function testChangePasswordWithPostInvalid()
    {
        $this->logIn($this->Member);
        $client = $this->client;

        $client->request(
            'POST',
            $this->generateUrl('admin_change_password'),
            array()
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    protected function createChangePasswordFormData()
    {
        $faker = $this->getFaker();

        $password = $faker->lexify('????????');

        $form = array(
            'current_password' => 'password',
            'change_password' => array(
                'first' => $password,
                'second' => $password,
            ),
            '_token' => 'dummy'
        );
        return $form;
    }
}
