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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\ProductClass;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\PurchaseFlow\Processor\PointDiffProcessor;
use Eccube\Service\PurchaseFlow\Processor\PointProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Tests\EccubeTestCase;

class PointDiffProcessorTest extends EccubeTestCase
{
    /** @var PointDiffProcessor */
    private $processor;

    /** @var PointProcessor */
    private $pointProcessor;

    /** @var OrderStatusRepository */
    private $OrderStatusRepository;

    /** @var BaseInfo */
    private $BaseInfo;

    public function setUp()
    {
        parent::setUp();
        $this->processor = $this->container->get(PointDiffProcessor::class);
        $this->pointProcessor = $this->container->get(PointProcessor::class);
        $this->OrderStatusRepository = $this->container->get(OrderStatusRepository::class);
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
    }

    /**
     * @dataProvider usePointOverCustomerPointProvider
     *
     * @param $beforeUsePoint int 編集前の利用ポイント
     * @param $afterUsePoint int 編集後の利用ポイント
     * @param $customerPoint int 保有ポイント
     * @param $isError boolean エラーかどうか
     */
    public function testUsePointOverCustomerPoint($beforeUsePoint, $afterUsePoint, $customerPoint, $isError)
    {
        $Customer = new Customer();
        $Customer->setPoint($customerPoint);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];

        $OrderStatus = $this->OrderStatusRepository->find(OrderStatus::NEW);

        // 編集前の受注
        $BeforeOrder = new Order();
        $BeforeOrder->setOrderStatus($OrderStatus);
        $BeforeOrder->setCustomer($Customer);
        $BeforeOrder->setUsePoint($beforeUsePoint);
        $BeforeOrder->addOrderItem($this->newOrderItem($ProductClass, 1000, 1));

        // 編集後の受注
        $AfterOrder = new Order();
        $AfterOrder->setOrderStatus($OrderStatus);
        $AfterOrder->setCustomer($Customer);
        $AfterOrder->setUsePoint($afterUsePoint);
        $AfterOrder->addOrderItem($this->newOrderItem($ProductClass, 1000, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addItemHolderValidator($this->processor);
        $result = $purchaseFlow->validate($AfterOrder, new PurchaseContext($BeforeOrder, $Customer));

        self::assertEquals($isError, $result->hasError());

        if ($isError) {
            self::assertEquals('利用ポイントが所有ポイントを上回っています。', $result->getErrors()[0]->getMessage());
        }
    }

    public function usePointOverCustomerPointProvider()
    {
        return [
            [0, 0, 0, false],
            [0, 0, 10, false],
            [0, 10, 0, true],
            [0, 10, 9, true],
            [0, 10, 10, false],
            [0, 10, 11, false],
            [10, 0, 0, false],
            [10, 10, 0, false],
            [10, 11, 0, true],
            [10, 20, 0, true],
            [10, 20, 9, true],
            [10, 20, 10, false],
            [10, 20, 11, false],
        ];
    }

    /**
     * @dataProvider usePointOverPriceProvider
     *
     * @param $beforeUsePoint int 編集前の利用ポイント
     * @param $afterUsePoint int 編集後の利用ポイント
     * @param $isError boolean エラーかどうか
     */
    public function testUsePointOverPrice($beforeUsePoint, $afterUsePoint, $isError)
    {
        $price = 100; // 商品の値段

        $Customer = new Customer();
        $Customer->setPoint(10000);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];

        $OrderStatus = $this->OrderStatusRepository->find(OrderStatus::NEW);

        // 編集前の受注
        $BeforeOrder = new Order();
        $BeforeOrder->setOrderStatus($OrderStatus);
        $BeforeOrder->setCustomer($Customer);
        $BeforeOrder->setUsePoint($beforeUsePoint);
        $BeforeOrder->addOrderItem($this->newOrderItem($ProductClass, $price, 1));

        // 編集後の受注
        $AfterOrder = new Order();
        $AfterOrder->setOrderStatus($OrderStatus);
        $AfterOrder->setCustomer($Customer);
        $AfterOrder->setUsePoint($afterUsePoint);
        $AfterOrder->addOrderItem($this->newOrderItem($ProductClass, $price, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addDiscountProcessor($this->pointProcessor); // Preprocessorでポイント明細を作成してtotalを計算し直す必要がある
        $purchaseFlow->addItemHolderValidator($this->processor);
        $result = $purchaseFlow->validate($AfterOrder, new PurchaseContext($BeforeOrder, $Customer));

        self::assertEquals($isError, $result->hasError());

        if ($isError) {
            $errors = $result->getErrors();
            $error = array_shift($errors); // PointDiffProcessorがsuccess, PointProcessorがerrorを返すので.
            self::assertEquals('利用ポイントがお支払い金額を上回っています。', $error->getMessage());
        }
    }

    public function usePointOverPriceProvider()
    {
        return [
            [0, 0, false],
            [0, 99, false],
            [0, 100, false],
            [0, 101, true],
            [50, 0, false],
            [50, 99, false],
            [50, 100, false],
            [50, 101, true],
        ];
    }

    /**
     * @dataProvider useReduceCustomerPointProvider
     *
     * @param $beforeUsePoint int 編集前の利用ポイント
     * @param $afterUsePoint int 編集後の利用ポイント
     * @param $userUsePoint int 期待する会員のポイント
     *
     * @throws \Eccube\Service\PurchaseFlow\PurchaseException
     */
    public function testReduceCustomerPoint($beforeUsePoint, $afterUsePoint, $userUsePoint)
    {
        $Customer = new Customer();
        $Customer->setPoint(100);

        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];

        $OrderStatus = $this->OrderStatusRepository->find(OrderStatus::NEW);

        // 編集前の受注
        $BeforeOrder = new Order();
        $BeforeOrder->setOrderStatus($OrderStatus);
        $BeforeOrder->setCustomer($Customer);
        $BeforeOrder->setUsePoint($beforeUsePoint);
        $BeforeOrder->addOrderItem($this->newOrderItem($ProductClass, 100, 1));

        // 編集後の受注
        $AfterOrder = new Order();
        $AfterOrder->setOrderStatus($OrderStatus);
        $AfterOrder->setCustomer($Customer);
        $AfterOrder->setUsePoint($afterUsePoint);
        $AfterOrder->addOrderItem($this->newOrderItem($ProductClass, 100, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addPurchaseProcessor($this->processor);

        $context = new PurchaseContext($BeforeOrder, $Customer);
        $purchaseFlow->validate($AfterOrder, $context);
        $purchaseFlow->prepare($AfterOrder, $context);
        $purchaseFlow->commit($AfterOrder, $context);

        self::assertEquals($userUsePoint, $Customer->getPoint());
    }

    public function useReduceCustomerPointProvider()
    {
        return [
            [0, 0, 100],
            [0, 10, 90],
            [50, 40, 110],
            [50, 50, 100],
            [50, 60, 90],
        ];
    }

    /**
     * @dataProvider usePointEachOrderStatusProvider
     *
     * @param $orderStatusId int 受注ステータス
     * @param $isChange boolean 変更されたかどうか
     *
     * @throws \Eccube\Service\PurchaseFlow\PurchaseException
     */
    public function testUsePointEachOrderStatus($orderStatusId, $isChange)
    {
        $Customer = new Customer();
        $Customer->setPoint(100);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];

        $OrderStatus = $this->OrderStatusRepository->find($orderStatusId);

        // 編集前の受注
        $BeforeOrder = new Order();
        $BeforeOrder->setOrderStatus($OrderStatus);
        $BeforeOrder->setCustomer($Customer);
        $BeforeOrder->setUsePoint(10);
        $BeforeOrder->addOrderItem($this->newOrderItem($ProductClass, 1000, 1));

        // 編集後の受注
        $AfterOrder = new Order();
        $AfterOrder->setOrderStatus($OrderStatus);
        $AfterOrder->setCustomer($Customer);
        $AfterOrder->setUsePoint(20);
        $AfterOrder->addOrderItem($this->newOrderItem($ProductClass, 1000, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addPurchaseProcessor($this->processor);

        $context = new PurchaseContext($BeforeOrder, $Customer);

        $purchaseFlow->validate($AfterOrder, $context);
        $purchaseFlow->prepare($AfterOrder, $context);
        $purchaseFlow->commit($AfterOrder, $context);

        if ($isChange) {
            self::assertEquals(90, $Customer->getPoint());
        } else {
            self::assertEquals(100, $Customer->getPoint());
        }
    }

    public function usePointEachOrderStatusProvider()
    {
        return [
            [OrderStatus::NEW, true],
            [OrderStatus::PAID, true],
            [OrderStatus::IN_PROGRESS, true],
            [OrderStatus::CANCEL, false],
            [OrderStatus::DELIVERED, true],
            [OrderStatus::RETURNED, false],
        ];
    }

    private function newOrderItem($ProductClass, $price, $quantity)
    {
        $OrderItem = new OrderItem();
        $OrderItem->setProductClass($ProductClass);
        $OrderItem->setPrice($price);
        $OrderItem->setQuantity($quantity);

        return $OrderItem;
    }
}
