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

namespace Eccube\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Shipping;
use Eccube\Service\OrderStateMachine;
use Eccube\Tests\EccubeTestCase;

class OrderStateMachineTest extends EccubeTestCase
{
    /** @var OrderStateMachine */
    private $stateMachine;

    public function setUp()
    {
        parent::setUp();
        $this->stateMachine = self::$container->get(OrderStateMachine::class);
    }

    /**
     * @dataProvider canProvider
     *
     * @param $fromId
     * @param $toId
     * @param $expected
     */
    public function testCan($fromId, $toId, $expected)
    {
        $fromStatus = $this->statusOf($fromId);
        $toStatus = $this->statusOf($toId);

        $Order = new Order();
        $Order->setOrderStatus($fromStatus);

        self::assertEquals($expected, $this->stateMachine->can($Order, $toStatus));
    }

    public function canProvider()
    {
        return [
            [OrderStatus::NEW,          OrderStatus::NEW,           false],
            [OrderStatus::NEW,          OrderStatus::PAID,          true],
            [OrderStatus::NEW,          OrderStatus::IN_PROGRESS,   true],
            [OrderStatus::NEW,          OrderStatus::CANCEL,        true],
            [OrderStatus::NEW,          OrderStatus::DELIVERED,     true],
            [OrderStatus::NEW,          OrderStatus::RETURNED,      false],

            [OrderStatus::PAID,         OrderStatus::NEW,           false],
            [OrderStatus::PAID,         OrderStatus::PAID,          false],
            [OrderStatus::PAID,         OrderStatus::IN_PROGRESS,   true],
            [OrderStatus::PAID,         OrderStatus::CANCEL,        true],
            [OrderStatus::PAID,         OrderStatus::DELIVERED,     true],
            [OrderStatus::PAID,         OrderStatus::RETURNED,      false],

            [OrderStatus::IN_PROGRESS,  OrderStatus::NEW,           false],
            [OrderStatus::IN_PROGRESS,  OrderStatus::PAID,          false],
            [OrderStatus::IN_PROGRESS,  OrderStatus::IN_PROGRESS,   false],
            [OrderStatus::IN_PROGRESS,  OrderStatus::CANCEL,        true],
            [OrderStatus::IN_PROGRESS,  OrderStatus::DELIVERED,     true],
            [OrderStatus::IN_PROGRESS,  OrderStatus::RETURNED,      false],

            [OrderStatus::CANCEL,       OrderStatus::NEW,           false],
            [OrderStatus::CANCEL,       OrderStatus::PAID,          false],
            [OrderStatus::CANCEL,       OrderStatus::IN_PROGRESS,   true],
            [OrderStatus::CANCEL,       OrderStatus::CANCEL,        false],
            [OrderStatus::CANCEL,       OrderStatus::DELIVERED,     false],
            [OrderStatus::CANCEL,       OrderStatus::RETURNED,      false],

            [OrderStatus::DELIVERED,    OrderStatus::NEW,           false],
            [OrderStatus::DELIVERED,    OrderStatus::PAID,          false],
            [OrderStatus::DELIVERED,    OrderStatus::IN_PROGRESS,   false],
            [OrderStatus::DELIVERED,    OrderStatus::CANCEL,        false],
            [OrderStatus::DELIVERED,    OrderStatus::DELIVERED,     false],
            [OrderStatus::DELIVERED,    OrderStatus::RETURNED,      true],

            [OrderStatus::RETURNED,     OrderStatus::NEW,           false],
            [OrderStatus::RETURNED,     OrderStatus::PAID,          false],
            [OrderStatus::RETURNED,     OrderStatus::IN_PROGRESS,   false],
            [OrderStatus::RETURNED,     OrderStatus::CANCEL,        false],
            [OrderStatus::RETURNED,     OrderStatus::DELIVERED,     true],
            [OrderStatus::RETURNED,     OrderStatus::RETURNED,      false],
        ];
    }

    public function testTransitionPay()
    {
        $Order = $this->createOrder($this->createCustomer());
        $Order->setOrderStatus($this->statusOf(OrderStatus::NEW));
        $Order->setPaymentDate(null);

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::PAID));

        self::assertNotNull($Order->getPaymentDate(), '入金済みになれば入金日が設定される');
    }

    public function testTransitionCancel()
    {
        /** @var ProductClass[] $ProductClasses */
        $ProductClasses = $this->createProduct('test', 2)->getProductClasses()->toArray();

        /*
         * 在庫を設定
         * ProductClass1 - 10
         * ProductClass2 - 20
         */
        $ProductClass1 = $ProductClasses[0];
        $ProductClass1->getProductStock()->setStock(10);
        $ProductClass1->setStock(10);

        $ProductClass2 = $ProductClasses[1];
        $ProductClass2->getProductStock()->setStock(20);
        $ProductClass2->setStock(20);

        $this->entityManager->flush();

        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint(1000);

        $Order = $this->createOrderWithProductClasses($Customer, $ProductClasses)
            ->setOrderStatus($this->statusOf(OrderStatus::NEW));

        /*
         * 受注の利用ポイント設定
         * 100pt
         */
        $Order->setUsePoint(100);

        /*
         * 受注明細の数量設定
         * OrderItem1 - 5
         * OrderItem2 - 10
         */
        $OrderItem1 = $this->getProductOrderItem($Order, $ProductClass1);
        $OrderItem1->setQuantity(5);
        $OrderItem2 = $this->getProductOrderItem($Order, $ProductClass2);
        $OrderItem2->setQuantity(10);

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::CANCEL));

        self::assertEquals(1100, $Customer->getPoint(), '受注取り消しなら会員の保有ポイントが戻る');

        self::assertEquals(15, $ProductClass1->getStock(), '受注取り消しなら在庫が戻る');
        self::assertEquals(30, $ProductClass2->getStock(), '受注取り消しなら在庫が戻る');
    }

    public function testTransitionBackToInProgress()
    {
        /** @var ProductClass[] $ProductClasses */
        $ProductClasses = $this->createProduct('test', 2)->getProductClasses()->toArray();

        /*
         * 在庫を設定
         * ProductClass1 - 10
         * ProductClass2 - 20
         */
        $ProductClass1 = $ProductClasses[0];
        $ProductClass1->getProductStock()->setStock(10);
        $ProductClass1->setStock(10);

        $ProductClass2 = $ProductClasses[1];
        $ProductClass2->getProductStock()->setStock(20);
        $ProductClass2->setStock(20);

        $this->entityManager->flush();

        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint(1000);

        $Order = $this->createOrderWithProductClasses($Customer, $ProductClasses)
            ->setOrderStatus($this->statusOf(OrderStatus::CANCEL));

        /*
         * 受注の利用ポイント設定
         * 100pt
         */
        $Order->setUsePoint(100);

        /*
         * 受注明細の数量設定
         * OrderItem1 - 5
         * OrderItem2 - 10
         */
        $OrderItem1 = $this->getProductOrderItem($Order, $ProductClass1);
        $OrderItem1->setQuantity(5);
        $OrderItem2 = $this->getProductOrderItem($Order, $ProductClass2);
        $OrderItem2->setQuantity(10);

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::IN_PROGRESS));

        self::assertEquals(900, $Customer->getPoint(), '対応中に戻るなら会員の保有ポイントが減る');

        self::assertEquals(5, $ProductClass1->getStock(), '対応中に戻るなら在庫が減る');
        self::assertEquals(10, $ProductClass2->getStock(), '対応中に戻るなら在庫が減る');
    }

    public function testTransitionShip()
    {
        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint(1000);

        $Order = $this->createOrder($Customer)
            ->setOrderStatus($this->statusOf(OrderStatus::IN_PROGRESS));
        $Order->getShippings()->forAll(function ($id, Shipping $Shipping) {
            $Shipping->setShippingDate(new \DateTime());
        });

        /*
         * 受注の加算ポイント設定
         * 100pt
         */
        $Order->setAddPoint(100);

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::DELIVERED));

        self::assertEquals(1100, $Customer->getPoint(), '発送済みになれば加算ポイントが会員に付与されているはず');
    }

    public function testTransitionReturn()
    {
        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint(1000);

        $Order = $this->createOrder($Customer)
            ->setOrderStatus($this->statusOf(OrderStatus::DELIVERED));

        /*
         * 受注のポイント設定
         * 利用ポイント - 10pt
         * 加算ポイント - 100pt
         */
        $Order
            ->setUsePoint(10)
            ->setAddPoint(100);

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::RETURNED));

        self::assertEquals(1000 + 10 - 100, $Customer->getPoint(), '返品になれば利用ポイント分が戻され、加算ポイント分は引かれるはず');
    }

    public function testTransitionCancelReturn()
    {
        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint(1000);

        $Order = $this->createOrder($Customer)
            ->setOrderStatus($this->statusOf(OrderStatus::RETURNED));
        $Order->getShippings()->forAll(function ($id, Shipping $Shipping) {
            $Shipping->setShippingDate(new \DateTime());
        });

        /*
         * 受注のポイント設定
         * 利用ポイント - 10pt
         * 加算ポイント - 100pt
         */
        $Order
            ->setUsePoint(10)
            ->setAddPoint(100);

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::DELIVERED));

        self::assertEquals(1000 - 10 + 100, $Customer->getPoint(), '返品キャンセルになれば利用ポイント分が減らされ、加算ポイント分が増えるはず');
    }

    /**
     * @param Order $Order
     * @param ProductClass $ProductClass
     *
     * @return OrderItem
     */
    private function getProductOrderItem(Order $Order, ProductClass $ProductClass)
    {
        return (new ArrayCollection($Order->getProductOrderItems()))->filter(function (OrderItem $item) use ($ProductClass) {
            return $item->getProductClass()->getId() == $ProductClass->getId();
        })->first();
    }

    /**
     * @param int $statusId
     *
     * @return OrderStatus
     */
    private function statusOf($statusId)
    {
        return $this->entityManager->find(OrderStatus::class, $statusId);
    }
}
