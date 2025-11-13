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
use PHPUnit\Framework\Attributes\DataProvider;

class OrderStateMachineTest extends EccubeTestCase
{
    private ?OrderStateMachine $stateMachine = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = static::getContainer()->get(OrderStateMachine::class);
    }

    /**
     * @param $fromId
     * @param $toId
     * @param $expected
     */
    #[DataProvider(methodName: 'canProvider')]
    public function testCan($fromId, $toId, $expected)
    {
        $fromStatus = $this->statusOf($fromId);
        $toStatus = $this->statusOf($toId);

        $Order = new Order();
        $Order->setOrderStatus($fromStatus);

        $this->assertEquals($expected, $this->stateMachine->can($Order, $toStatus));
    }

    public static function canProvider(): \Iterator
    {
        yield [OrderStatus::NEW,          OrderStatus::NEW,           false];
        yield [OrderStatus::NEW,          OrderStatus::PAID,          true];
        yield [OrderStatus::NEW,          OrderStatus::IN_PROGRESS,   true];
        yield [OrderStatus::NEW,          OrderStatus::CANCEL,        true];
        yield [OrderStatus::NEW,          OrderStatus::DELIVERED,     true];
        yield [OrderStatus::NEW,          OrderStatus::RETURNED,      false];
        yield [OrderStatus::PAID,         OrderStatus::NEW,           false];
        yield [OrderStatus::PAID,         OrderStatus::PAID,          false];
        yield [OrderStatus::PAID,         OrderStatus::IN_PROGRESS,   true];
        yield [OrderStatus::PAID,         OrderStatus::CANCEL,        true];
        yield [OrderStatus::PAID,         OrderStatus::DELIVERED,     true];
        yield [OrderStatus::PAID,         OrderStatus::RETURNED,      false];
        yield [OrderStatus::IN_PROGRESS,  OrderStatus::NEW,           false];
        yield [OrderStatus::IN_PROGRESS,  OrderStatus::PAID,          false];
        yield [OrderStatus::IN_PROGRESS,  OrderStatus::IN_PROGRESS,   false];
        yield [OrderStatus::IN_PROGRESS,  OrderStatus::CANCEL,        true];
        yield [OrderStatus::IN_PROGRESS,  OrderStatus::DELIVERED,     true];
        yield [OrderStatus::IN_PROGRESS,  OrderStatus::RETURNED,      false];
        yield [OrderStatus::CANCEL,       OrderStatus::NEW,           false];
        yield [OrderStatus::CANCEL,       OrderStatus::PAID,          false];
        yield [OrderStatus::CANCEL,       OrderStatus::IN_PROGRESS,   true];
        yield [OrderStatus::CANCEL,       OrderStatus::CANCEL,        false];
        yield [OrderStatus::CANCEL,       OrderStatus::DELIVERED,     false];
        yield [OrderStatus::CANCEL,       OrderStatus::RETURNED,      false];
        yield [OrderStatus::DELIVERED,    OrderStatus::NEW,           false];
        yield [OrderStatus::DELIVERED,    OrderStatus::PAID,          false];
        yield [OrderStatus::DELIVERED,    OrderStatus::IN_PROGRESS,   false];
        yield [OrderStatus::DELIVERED,    OrderStatus::CANCEL,        false];
        yield [OrderStatus::DELIVERED,    OrderStatus::DELIVERED,     false];
        yield [OrderStatus::DELIVERED,    OrderStatus::RETURNED,      true];
        yield [OrderStatus::RETURNED,     OrderStatus::NEW,           false];
        yield [OrderStatus::RETURNED,     OrderStatus::PAID,          false];
        yield [OrderStatus::RETURNED,     OrderStatus::IN_PROGRESS,   false];
        yield [OrderStatus::RETURNED,     OrderStatus::CANCEL,        false];
        yield [OrderStatus::RETURNED,     OrderStatus::DELIVERED,     true];
        yield [OrderStatus::RETURNED,     OrderStatus::RETURNED,      false];
    }

    public function testTransitionPay()
    {
        $Order = $this->createOrder($this->createCustomer());
        $Order->setOrderStatus($this->statusOf(OrderStatus::NEW));
        $Order->setPaymentDate(null);

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::PAID));

        $this->assertInstanceOf(\DateTime::class, $Order->getPaymentDate(), '入金済みになれば入金日が設定される');
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
        $ProductClass1->setStock('10');

        $ProductClass2 = $ProductClasses[1];
        $ProductClass2->getProductStock()->setStock(20);
        $ProductClass2->setStock('20');

        $this->entityManager->flush();

        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint('1000');

        $Order = $this->createOrderWithProductClasses($Customer, $ProductClasses)
            ->setOrderStatus($this->statusOf(OrderStatus::NEW));

        /*
         * 受注の利用ポイント設定
         * 100pt
         */
        $Order->setUsePoint('100');

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

        $this->assertSame('1100', $Customer->getPoint(), '受注取り消しなら会員の保有ポイントが戻る');

        $this->assertSame('15', $ProductClass1->getStock(), '受注取り消しなら在庫が戻る');
        $this->assertSame('30', $ProductClass2->getStock(), '受注取り消しなら在庫が戻る');
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
        $ProductClass1->setStock('10');

        $ProductClass2 = $ProductClasses[1];
        $ProductClass2->getProductStock()->setStock(20);
        $ProductClass2->setStock('20');

        $this->entityManager->flush();

        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint('1000');

        $Order = $this->createOrderWithProductClasses($Customer, $ProductClasses)
            ->setOrderStatus($this->statusOf(OrderStatus::CANCEL));

        /*
         * 受注の利用ポイント設定
         * 100pt
         */
        $Order->setUsePoint('100');

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

        $this->assertSame('900', $Customer->getPoint(), '対応中に戻るなら会員の保有ポイントが減る');

        $this->assertSame('5', $ProductClass1->getStock(), '対応中に戻るなら在庫が減る');
        $this->assertSame('10', $ProductClass2->getStock(), '対応中に戻るなら在庫が減る');
    }

    public function testTransitionShip()
    {
        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint('1000');

        $Order = $this->createOrder($Customer)
            ->setOrderStatus($this->statusOf(OrderStatus::IN_PROGRESS));
        $Order->getShippings()->forAll(function ($id, Shipping $Shipping) {
            $Shipping->setShippingDate(new \DateTime());
        });

        /*
         * 受注の加算ポイント設定
         * 100pt
         */
        $Order->setAddPoint('100');

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::DELIVERED));

        $this->assertSame('1100', $Customer->getPoint(), '発送済みになれば加算ポイントが会員に付与されているはず');
    }

    public function testTransitionReturn()
    {
        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint('1000');

        $Order = $this->createOrder($Customer)
            ->setOrderStatus($this->statusOf(OrderStatus::DELIVERED));

        /*
         * 受注のポイント設定
         * 利用ポイント - 10pt
         * 加算ポイント - 100pt
         */
        $Order
            ->setUsePoint('10')
            ->setAddPoint('100');

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::RETURNED));

        // 1000 + 10 - 100 = 910
        $this->assertSame('910', $Customer->getPoint(), '返品になれば利用ポイント分が戻され、加算ポイント分は引かれるはず');
    }

    public function testTransitionCancelReturn()
    {
        /*
         * 会員の保有ポイント設定
         * 1000pt
         */
        $Customer = $this->createCustomer()
            ->setPoint('1000');

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
            ->setUsePoint('10')
            ->setAddPoint('100');

        $this->stateMachine->apply($Order, $this->statusOf(OrderStatus::DELIVERED));

        // 1000 - 10 + 100 = 1090
        $this->assertSame('1090', $Customer->getPoint(), '返品キャンセルになれば利用ポイント分が減らされ、加算ポイント分が増えるはず');
    }

    private function getProductOrderItem(Order $Order, ProductClass $ProductClass): OrderItem
    {
        return (new ArrayCollection($Order->getProductOrderItems()))->filter(fn (OrderItem $item) => $item->getProductClass()->getId() == $ProductClass->getId())->first();
    }

    private function statusOf(int $statusId): OrderStatus
    {
        return $this->entityManager->find(OrderStatus::class, $statusId);
    }
}
