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

namespace Eccube\Tests\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Tests\EccubeTestCase;
use Eccube\Util\StringUtil;

/**
 * OrderRepository::getQueryBuilderBySearchDataForAdminTest test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderRepositoryGetQueryBuilderBySearchDataAdminTest extends EccubeTestCase
{
    /** @var Customer */
    protected $Customer;
    /** @var Order */
    protected $Order;
    /** @var Order */
    protected $Order1;
    /** @var Order */
    protected $Order2;
    /** @var ArrayCollection */
    protected $Results;
    /** @var ArrayCollection */
    protected $searchData;
    /** @var OrderStatusRepository */
    protected $orderStatusRepo;
    /** @var OrderRepository */
    protected $orderRepo;
    /** @var SexRepository */
    protected $sexRepo;
    /** @var PaymentRepository */
    protected $paymentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->createProduct();

        $this->orderStatusRepo = $this->container->get(OrderStatusRepository::class);
        $this->paymentRepo = $this->container->get(PaymentRepository::class);
        $this->orderRepo = $this->container->get(OrderRepository::class);
        $this->sexRepo = $this->container->get(SexRepository::class);
        $this->Customer = $this->createCustomer();
        $this->entityManager->persist($this->Customer);
        $this->entityManager->flush();

        $this->Order = $this->createOrder($this->Customer);
        $this->Order1 = $this->createOrder($this->Customer);
        $this->Order2 = $this->createOrder($this->createCustomer('test@example.com'));
        // 新規受付にしておく
        $NewStatus = $this->orderStatusRepo->find(OrderStatus::NEW);
        $this->Order1
            ->setOrderStatus($NewStatus)
            ->setOrderDate(new \DateTime());
        $this->Order2
            ->setOrderStatus($NewStatus)
            ->setOrderDate(new \DateTime());
        $this->entityManager->flush();
    }

    public function scenario()
    {
        $this->Results = $this->orderRepo->getQueryBuilderBySearchDataForAdmin($this->searchData)
            ->getQuery()
            ->getResult();
    }

    public function testOrderIdStart()
    {
        $this->searchData = [
            'order_id_start' => $this->Order->getId(),
        ];
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiWithName()
    {
        $this->Order2
            ->setName01('立方')
            ->setName02('隊長');
        $this->entityManager->flush();

        $this->searchData = [
            'multi' => '立方',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiWithKana()
    {
        $this->Order2
            ->setKana01('リッポウ')
            ->setKana02('タイチョウ');
        $this->entityManager->flush();

        $this->searchData = [
            'multi' => 'タイチョウ',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiWithNo()
    {
        $this->entityManager->flush();

        $this->searchData = [
            'multi' => $this->Order2->getOrderNo(),
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderIdEnd()
    {
        $this->searchData = [
            'order_id_end' => $this->Order->getId(),
        ];
        $this->scenario();

        // $this->Order は購入処理中なので 0 件になる
        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStatus()
    {
        $NewStatus = $this->orderStatusRepo->find(OrderStatus::NEW);
        $this->Order1->setOrderStatus($NewStatus);
        $this->Order2->setOrderStatus($NewStatus);
        $this->entityManager->flush();

        $this->searchData = [
            'status' => [
                OrderStatus::NEW,
            ],
        ];
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testMultiStatus()
    {
        $this->Order1->setOrderStatus($this->orderStatusRepo->find(OrderStatus::NEW));
        $this->Order2->setOrderStatus($this->orderStatusRepo->find(OrderStatus::CANCEL));
        $this->entityManager->flush();

        $Statuses = new ArrayCollection([
            OrderStatus::NEW,
            OrderStatus::CANCEL,
            OrderStatus::PENDING,
        ]);
        $this->searchData = [
            'multi_status' => $Statuses,
        ];
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testName()
    {
        $this->Order2
            ->setName01('立方')
            ->setName02('隊長');
        $this->entityManager->flush();

        $this->searchData = [
            'name' => '立方隊長',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testKana()
    {
        $this->Order1
            ->setKana01('セイ')
            ->setKana02('メイ'); // XXX いずれかが NULL だと無視されてしまう
        $this->entityManager->flush();

        $this->searchData = [
            'kana' => 'メ',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testEmail()
    {
        $this->searchData = [
            'email' => 'test@example.com',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPhoneNumber()
    {
        /** @var Order[] $Orders */
        $Orders = $this->orderRepo->findAll();
        // 全受注の Phone Number を変更しておく
        foreach ($Orders as $Order) {
            $Order->setPhoneNumber('9876543210');
        }

        // 1受注のみ検索対象とする
        $this->Order1->setPhoneNumber('0123456789');
        $this->entityManager->flush();

        $this->searchData = [
            'phone_number' => '0123456789',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthStart()
    {
        $this->Customer->setBirth(new \DateTime('2006-09-01'));
        $this->entityManager->flush();

        $this->searchData = [
            'birth_start' => new \DateTime('2006-09-01'),
        ];
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBirthEnd()
    {
        $this->Customer->setBirth(new \DateTime('2006-09-01'));
        $this->entityManager->flush();

        $this->searchData = [
            'birth_end' => new \DateTime('2006-09-01'),
        ];
        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testSex()
    {
        $Male = $this->sexRepo->find(1);
        $Female = $this->sexRepo->find(2);
        $this->Order1->setSex($Male);
        $this->Order2->setSex(null);
        $this->entityManager->flush();

        $Sexs = new ArrayCollection([$Male, $Female]);
        $this->searchData = [
            'sex' => $Sexs,
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderDateStart()
    {
        $this->searchData = [
            'order_date_start' => new \DateTime('- 1 days'),
        ];

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderDateEnd()
    {
        $this->searchData = [
            'order_date_end' => new \DateTime('+ 1 days'),
        ];

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateStart()
    {
        $this->searchData = [
            'update_date_start' => new \DateTime('- 1 days'),
        ];

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateEnd()
    {
        $this->searchData = [
            'update_date_end' => new \DateTime('+ 1 days'),
        ];

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaymentDateStart()
    {
        $Status = $this->orderStatusRepo->find(OrderStatus::PAID);
        $this->orderRepo->changeStatus($this->Order2->getId(), $Status);

        $this->searchData = [
            'payment_date_start' => new \DateTime('- 1 days'),
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaymentDateEnd()
    {
        $Status = $this->orderStatusRepo->find(OrderStatus::PAID);
        $this->orderRepo->changeStatus($this->Order2->getId(), $Status);
        $this->searchData = [
            'payment_date_end' => new \DateTime('+ 1 days'),
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCommitDateStart()
    {
        $this->markTestSkipped('order.shipping_dateは不要と思われる.');
        $Status = $this->orderStatusRepo->find(OrderStatus::DELIVERED);
        $this->orderRepo->changeStatus($this->Order2->getId(), $Status);

        $this->searchData = [
            'shipping_date_start' => new \DateTime('- 1 days'),
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCommitDateEnd()
    {
        $this->markTestSkipped('order.shipping_dateは不要と思われる.');
        $Status = $this->orderStatusRepo->find(OrderStatus::DELIVERED);
        $this->orderRepo->changeStatus($this->Order2->getId(), $Status);
        $this->searchData = [
            'shipping_date_end' => new \DateTime('+ 1 days'),
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaymentTotalStart()
    {
        $this->Order->setPaymentTotal(99);
        $this->Order1->setPaymentTotal(100);
        $this->Order2->setPaymentTotal(101);
        $this->entityManager->flush();

        // XXX 0 が無視されてしまう
        $this->searchData = [
            'payment_total_start' => 100,
        ];

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaymentTotalEnd()
    {
        $this->Order->setPaymentTotal(99);
        $this->Order1->setPaymentTotal(100);
        $this->Order2->setPaymentTotal(101);
        $this->entityManager->flush();

        $this->searchData = [
            'payment_total_end' => 100,
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testBuyProductName()
    {
        foreach ($this->Order1->getOrderItems() as $item) {
            $item->setProductName('アイス');
        }
        foreach ($this->Order2->getOrderItems() as $item) {
            $item->setProductName('アイス');
        }
        $this->entityManager->flush();

        $this->searchData = [
            'buy_product_name' => 'アイス',
        ];

        $this->scenario();

        $this->expected = 2;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * @param array $searchPaymentNos
     * @param int $expected
     *
     * @dataProvider dataPaymentProvider
     */
    public function testPayment(array $searchPaymentNos, int $expected)
    {
        // データの準備
        $Payments = [];
        for ($i = 1; $i < 4; $i++) {
            $Payments[$i] = $this->paymentRepo->find($i);
        }

        // 支払い方法1, 2をそれぞれ設定
        $this->Order1->setPayment($Payments[1]);
        $this->Order2->setPayment($Payments[2]);

        $this->entityManager->flush();

        // Paymentの検索リストを作成
        $Payments = array_filter($Payments, function ($Payment) use($searchPaymentNos){
            return in_array($Payment->getId(), $searchPaymentNos);
        });

        // 検索
        $this->searchData = [
            'payment' => $Payments,
        ];

        $this->scenario();

        $this->expected = $expected;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * Data for case check Payment.
     *
     * @return array
     */
    public function dataPaymentProvider()
    {
        return [
            [[1], 1],
            [[1,2], 2],
            [[2,3], 1],
            [[3], 0],
        ];
    }

    public function testCompanyName()
    {
        $this->Order2->setCompanyName('ダミー会社名');
        $this->entityManager->flush();

        $this->searchData = [
            'company_name' => 'ダミー会社名',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderNo()
    {
        $this->Order2->setOrderNo('12345678abcd');
        $this->entityManager->flush();

        $this->searchData = [
            'order_no' => '12345678abcd',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testTrackingNumber()
    {
        $this->Order2->getShippings()[0]->setTrackingNumber('1234abcdefgh');
        $this->entityManager->flush();

        $this->searchData = [
            'tracking_number' => '1234abcdefgh',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * @param array $checks
     * @param int $expected
     *
     * @dataProvider dataShippingMailProvider
     */
    public function testShippingMail(array $checks, int $expected)
    {
        $this->Order2->getShippings()[0]->setMailSendDate(new \DateTime());
        $this->entityManager->flush();

        $this->searchData = [
            'shipping_mail' => $checks,
        ];
        $this->scenario();

        $this->expected = $expected;
        $this->actual = count($this->Results);
        $this->verify();
    }

    /**
     * Data for case check shipping mail.
     *
     * @return array
     */
    public function dataShippingMailProvider()
    {
        return [
            [[], 2],
            [[Shipping::SHIPPING_MAIL_SENT], 1],
            [[Shipping::SHIPPING_MAIL_UNSENT], 1],
            [[Shipping::SHIPPING_MAIL_SENT, Shipping::SHIPPING_MAIL_UNSENT], 2],
        ];
    }

    /**
     * Shippingを対象とする検索のテスト.
     *
     * 複数のShippingをもつOrderに対して, Shippingを対象として検索すると, ヒットしたShippingのみ取得できることを確認する.
     */
    public function testSearchShipping()
    {
        $trackingNumber = StringUtil::random();
        $Shipping = new Shipping();
        $Shipping->copyProperties($this->Customer);
        $Shipping
            ->setOrder($this->Order1)
            ->setTrackingNumber($trackingNumber);

        $this->Order1->addShipping($Shipping);

        $this->entityManager->flush();

        $this->searchData = [
            'order_no' => $this->Order1->getOrderNo(),
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();

        $this->expected = 2;
        $this->actual = count($this->Results[0]->getShippings());
        $this->verify('Shippingは2件取得できるはず');

        $this->entityManager->clear();

        $this->searchData = [
            'order_no' => $this->Order1->getOrderNo(),
            'tracking_number' => $trackingNumber,
        ];

        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();

        $this->expected = 1;
        $this->actual = count($this->Results[0]->getShippings());
        $this->verify('Shippingは1件のみ取得できるはず');
    }
}
