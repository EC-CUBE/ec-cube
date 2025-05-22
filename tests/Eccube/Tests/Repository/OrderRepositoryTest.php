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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Entity\Shipping;
use Eccube\Repository\OrderRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * OrderRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderRepositoryTest extends EccubeTestCase
{
    /** @var Customer */
    protected $Customer;
    /** @var Order */
    protected $Order;

    /** @var OrderRepository */
    protected $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->entityManager->getRepository(\Eccube\Entity\Order::class);

        $this->createProduct();
        $this->Customer = $this->createCustomer();
        $this->entityManager->persist($this->Customer);
        $this->entityManager->flush();
        $this->Order = $this->createOrder($this->Customer);
    }

    public function testChangeStatusWithPayment()
    {
        $orderId = $this->Order->getId();
        $Status = $this->entityManager->find(OrderStatus::class, OrderStatus::PAID);

        $this->orderRepository->changeStatus($orderId, $Status);

        $this->assertNotNull($this->Order->getPaymentDate());
        $this->expected = 6;
        $this->actual = $this->Order->getOrderStatus()->getId();
        $this->verify();
    }

    public function testChangeStatusWithOther()
    {
        $orderId = $this->Order->getId();
        $Status = $this->entityManager->find(OrderStatus::class, OrderStatus::NEW);

        $this->orderRepository->changeStatus($orderId, $Status);

        $this->assertNull($this->Order->getPaymentDate());
    }

    public function testGetQueryBuilderByCustomer()
    {
        $Customer2 = $this->createCustomer();
        $this->createOrder($this->Customer);
        $this->createOrder($Customer2);

        $qb = $this->orderRepository->getQueryBuilderByCustomer($this->Customer);
        $Orders = $qb->getQuery()->getResult();

        $this->expected = 2;
        $this->actual = count($Orders);
        $this->verify();
    }

    public function testGetShippings()
    {
        $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $this->Order->getShippings());
        $this->assertEquals(1, $this->Order->getShippings()->count());
    }

    public function testUpdateOrderSummary()
    {
        $Customer = $this->createCustomer();
        $this->orderRepository->updateOrderSummary($Customer);

        self::assertNull($Customer->getFirstBuyDate());
        self::assertNull($Customer->getLastBuyDate());
        self::assertSame(0, $Customer->getBuyTimes());
        self::assertSame(0, $Customer->getBuyTotal());

        $Order1 = $this->createOrder($Customer);
        $Order1->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();

        $this->orderRepository->updateOrderSummary($Customer);
        self::assertSame($Order1->getOrderDate(), $Customer->getFirstBuyDate());
        self::assertSame($Order1->getOrderDate(), $Customer->getLastBuyDate());
        self::assertEquals(1, $Customer->getBuyTimes());
        self::assertEquals($Order1->getTotal(), $Customer->getBuyTotal());

        $Order2 = $this->createOrder($Customer);
        $Order2->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();

        $this->orderRepository->updateOrderSummary($Customer);
        self::assertSame($Order1->getOrderDate(), $Customer->getFirstBuyDate());
        self::assertSame($Order2->getOrderDate(), $Customer->getLastBuyDate());
        self::assertEquals(2, $Customer->getBuyTimes());
        self::assertEquals($Order1->getTotal() + $Order2->getTotal(), $Customer->getBuyTotal());
    }

    public function testGetQueryBuilderBySearchDataForAdminMulti2147483648()
    {
        $Order = $this->createOrder($this->createCustomer('2147483648@example.com'));
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->orderRepository->save($Order);
        $this->entityManager->flush();

        $actual = $this->orderRepository->getQueryBuilderBySearchDataForAdmin(['multi' => '2147483648'])
            ->getQuery()
            ->getResult();

        self::assertEquals($Order, $actual[0]);
    }

    /**
     * @dataProvider dataGetQueryBuilderBySearchDataForAdmin_nameProvider
     */
    public function testGetQueryBuilderBySearchDataForAdminName(string $formName, string $searchWord, int $expected)
    {
        $this->Order
            ->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW))
            ->setName01('姓')
            ->setName02('名')
            ->setKana01('セイ')
            ->setKana02('メイ')
            ->setCompanyName('株式会社　会社名'); // 全角スペース
        $this->orderRepository->save($this->Order);
        $this->entityManager->flush();

        $actual = $this->orderRepository->getQueryBuilderBySearchDataForAdmin([$formName => $searchWord])
            ->getQuery()
            ->getResult();

        self::assertCount($expected, $actual);
    }

    public function dataGetQueryBuilderBySearchDataForAdmin_nameProvider()
    {
        return [
            ['multi', '姓', 1],
            ['multi', '名', 1],
            ['multi', '姓名', 1],
            ['multi', '姓 名', 1],
            ['multi', '姓　名', 1],
            ['multi', 'セイ', 1],
            ['multi', 'メイ', 1],
            ['multi', 'セイメイ', 1],
            ['multi', 'セイ メイ', 1],
            ['multi', 'セイ　メイ', 1],
            ['multi', '株式会社', 1],
            ['multi', '会社名', 1],
            ['multi', '株式会社会社名', 0],
            ['multi', '株式会社 会社名', 0], // 半角スペース
            ['multi', '株式会社　会社名', 1], // 全角スペース
            ['multi', '石', 0],
            ['multi', 'キューブ', 0],
            ['multi', '姓 球部', 0],
            ['multi', 'セイ 名', 0],
            ['multi', '姓　メイ', 0],
            ['name', '姓', 1],
            ['name', '名', 1],
            ['name', '姓名', 1],
            ['name', '姓 名', 1],
            ['name', '姓　名', 1],
            ['name', 'セイ', 0],
            ['name', '株式会社　会社名', 0],
            ['kana', 'セイ', 1],
            ['kana', 'メイ', 1],
            ['kana', 'セイメイ', 1],
            ['kana', 'セイ メイ', 1],
            ['kana', 'セイ　メイ', 1],
            ['kana', '姓', 0],
            ['kana', '株式会社　会社名', 0],
            ['company_name', '株式会社', 1],
            ['company_name', '会社名', 1],
            ['company_name', '株式会社会社名', 0],
            ['company_name', '株式会社 会社名', 0], // 半角スペース
            ['company_name', '株式会社　会社名', 1], // 全角スペース
            ['company_name', '姓', 0],
            ['company_name', 'セイ', 0],
        ];
    }

    /**
     * AND 条件についてテストします。
     *
     * すべて一致する検索条件を、1項目ずつ一致しない値に置き換えて確認します。
     *
     * @dataProvider dataGetQueryBuilderBySearchDataForAdmin_testAndCondition
     */
    public function testGetQueryBuilderBySearchDataForAdmin_testAndCondition(array $searchWord, int $expected)
    {
        // 基本の検索条件に一致するデータを作成します
        $this->Order
            ->setOrderStatus($this->entityManager->getReference(OrderStatus::class, OrderStatus::NEW))
            ->setName01('姓')
            ->setName02('名')
            ->setKana01('セイ')
            ->setKana02('メイ')
            ->setCompanyName('会社名')
            ->setEmail('alice@example.com')
            ->setPhoneNumber('00000000000')
            ->setPayment($this->entityManager->getReference(Payment::class, 1))
            ->setOrderDate(new \DateTime('2022-01-01T10:00:00Z'))
            ->setPaymentDate(new \DateTime('2022-02-01T10:00:00Z'))
            ->setPaymentTotal('1000');
        $Shipping = $this->Order->getShippings()[0];
        $Shipping
            ->setTrackingNumber('12345')
            ->setMailSendDate(null)
            ->setShippingDeliveryDate(new \DateTime('2022-03-01T10:00:00Z'));
        $OrderItem = $this->Order->getOrderItems()[0];
        $OrderItem
            ->setProductName('商品名');
        $this->orderRepository->save($this->Order);
        $this->entityManager->flush();

        // 基本の検索条件 (すべて一致する条件)
        $baseSearchData = [
            'multi' => '姓',
            'status' => [OrderStatus::NEW],
            'name' => '姓',
            'kana' =>'セイ',
            'company_name' => '会社名',
            'email' => 'alice@example.com',
            'phone_number' => '00000000000',
            'order_no' => $this->Order->getOrderNo(),
            'tracking_number' => '12345',
            'shipping_mail' => [Shipping::SHIPPING_MAIL_UNSENT],
            'payment' => [1],
            'order_datetime_start' => new \DateTime('2022-01-01T10:00:00Z'),
            'order_datetime_end' => new \DateTime('2022-01-01T10:00:01Z'),
            'payment_datetime_start' => new \DateTime('2022-02-01T10:00:00Z'),
            'payment_datetime_end' => new \DateTime('2022-02-01T10:00:01Z'),
            'update_datetime_start' => 'PT0S',
            'update_datetime_end' => 'PT1S',
            'shipping_delivery_datetime_start' => new \DateTime('2022-03-01T10:00:00Z'),
            'shipping_delivery_datetime_end' => new \DateTime('2022-03-01T10:00:01Z'),
            'payment_total_start' => '1000',
            'payment_total_end' => '1000',
            'buy_product_name' => '商品名',
        ];

        $searchData = array_merge($baseSearchData, $searchWord);

        // dataProvider 内で直接指定することが難しい値を変換します
        if (isset($searchData['payment'])) {
            $searchData['payment'] = \array_map(function ($item) {
                return $this->entityManager->getReference(Payment::class, $item);
            }, $searchData['payment']);
        }
        if (isset($searchData['update_datetime_start'])) {
            $searchData['update_datetime_start'] = $this->Order->getUpdateDate()
                ->add(new \DateInterval($searchData['update_datetime_start']))
                ->format('Y-m-d H:i:s');
        }
        if (isset($searchData['update_datetime_end'])) {
            $searchData['update_datetime_end'] = $this->Order->getUpdateDate()
                ->add(new \DateInterval($searchData['update_datetime_end']))
                ->format('Y-m-d H:i:s');
        }

        $actual = $this->orderRepository->getQueryBuilderBySearchDataForAdmin($searchData)
            ->getQuery()
            ->getResult();

        self::assertCount($expected, $actual);
    }

    public function dataGetQueryBuilderBySearchDataForAdmin_testAndCondition()
    {
        return [
            // 基本の検索条件で検索結果が返ってくること
            [[], 1],

            // 1 項目ずつ一致しない条件に置き換えると検索結果が返ってこないこと
            [['status' => [OrderStatus::CANCEL]], 0],
            [['multi' => '一致しないキーワード'], 0],
            [['name' =>  '一致しないキーワード'], 0],
            [['kana' =>  '一致しないキーワード'], 0],
            [['company_name' =>  '一致しないキーワード'], 0],
            [['email' =>  '一致しないキーワード'], 0],
            [['phone_number' =>  '11111111111'], 0],
            [['order_no' =>  '一致しないキーワード'], 0],
            [['tracking_number' =>  '一致しないキーワード'], 0],
            [['shipping_mail' =>  [Shipping::SHIPPING_MAIL_SENT]], 0],
            [['payment' => [2]], 0],
            [['order_datetime_start' => new \DateTime('2022-01-01T10:00:01Z')], 0],
            [['order_datetime_end' => new \DateTime('2022-01-01T10:00:00Z')], 0],
            [['payment_datetime_start' => new \DateTime('2022-02-01T10:00:01Z')], 0],
            [['payment_datetime_end' => new \DateTime('2022-02-01T10:00:00Z')], 0],
            [['update_datetime_start' => 'PT1S'], 0],
            [['update_datetime_end' => 'PT0S'], 0],
            [['shipping_delivery_datetime_start' => new \DateTime('2022-03-01T10:00:01Z')], 0],
            [['shipping_delivery_datetime_end' => new \DateTime('2022-03-01T10:00:00Z')], 0],
            [['payment_total_start' => '1001'], 0],
            [['payment_total_end' => '999'], 0],
            [['buy_product_name' => '一致しないキーワード'], 0],
        ];
    }
}
