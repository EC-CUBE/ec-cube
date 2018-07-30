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
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\PointProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Tests\EccubeTestCase;

// TODO: ポイントの割引額への変換レートが変更されているかのテスト追加
// TODO: ポイントの付与レートが変更されているかのテスト追加
class PointProcessorTest extends EccubeTestCase
{
    /** @var PointProcessor */
    private $processor;

    /** @var BaseInfo */
    private $BaseInfo;

    public function setUp()
    {
        parent::setUp();
        $this->processor = $this->container->get(PointProcessor::class);
        $this->BaseInfo = $this->container->get(BaseInfo::class);
    }

    public function testUsePoint()
    {
        $Order = new Order();
        $Order->setCustomer(new Customer());
        $Order->setUsePoint(100);
        $this->processor->process($Order, new PurchaseContext());

        /** @var OrderItem $OrderItem */
        $OrderItem = $Order->getOrderItems()->filter(
            function (OrderItem $OrderItem) {
                return $OrderItem->isPoint();
            }
        )->first();

        self::assertNotNull($OrderItem);
        self::assertEquals(-100, $OrderItem->getPrice());
    }

    /**
     * @dataProvider usePointOverCustomerPointProvider
     *
     * @param $usePoint int 利用ポイント
     * @param $customerPoint int 保有ポイント
     * @param $isError boolean エラーかどうか
     */
    public function testUsePointOverCustomerPoint($usePoint, $customerPoint, $isError)
    {
        $Customer = new Customer();
        $Customer->setPoint($customerPoint);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setCustomer($Customer);
        $Order->setUsePoint($usePoint);
        $Order->addOrderItem($this->newOrderItem($ProductClass, 1000, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addItemHolderValidator($this->processor);
        $result = $purchaseFlow->validate($Order, new PurchaseContext(null, $Customer));

        self::assertEquals($isError, $result->hasError());

        if ($isError) {
            self::assertEquals('利用ポイントが所有ポイントを上回っています.', $result->getErrors()[0]->getMessage());
        }
    }

    public function usePointOverCustomerPointProvider()
    {
        return [
            [0, 0, false],
            [0, 10, false],
            [10, 0, true],
            [10, 9, true],
            [10, 10, false],
            [10, 11, false],
        ];
    }

    /**
     * @dataProvider usePointOverPriceProvider
     *
     * @param $usePoint int 利用ポイント
     * @param $isError boolean エラーかどうか
     */
    public function testUsePointOverPrice($usePoint, $isError)
    {
        $price = 100; // 商品の値段

        $Customer = new Customer();
        $Customer->setPoint(10000);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setCustomer($Customer);
        $Order->setUsePoint($usePoint);
        $Order->addOrderItem($this->newOrderItem($ProductClass, $price, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addItemHolderPreprocessor($this->processor); // Preprocessorでポイント明細を作成してtotalを計算し直す必要がある
        $purchaseFlow->addItemHolderValidator($this->processor);
        $result = $purchaseFlow->validate($Order, new PurchaseContext(null, $Customer));

        self::assertEquals($isError, $result->hasError());

        if ($isError) {
            self::assertEquals('利用ポイントがお支払い金額を上回っています.', $result->getErrors()[0]->getMessage());
            self::assertEquals($price, $Order->getUsePoint());
        } else {
            self::assertEquals($usePoint, $Order->getUsePoint());
        }
    }

    public function usePointOverPriceProvider()
    {
        return [
            [0, false],
            [99, false],
            [100, false],
            [101, true],
        ];
    }

    /**
     * @throws \Eccube\Service\PurchaseFlow\PurchaseException
     */
    public function testReduceCustomerPoint()
    {
        $Customer = new Customer();
        $Customer->setPoint(100);

        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setCustomer($Customer);
        $Order->setUsePoint(10);
        $Order->addOrderItem($this->newOrderItem($ProductClass, 100, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addPurchaseProcessor($this->processor);

        $context = new PurchaseContext(null, $Customer);
        $purchaseFlow->validate($Order, $context);
        $purchaseFlow->prepare($Order, $context);
        $purchaseFlow->commit($Order, $context);

        self::assertEquals(90, $Customer->getPoint());
    }

    /**
     * @dataProvider useAddPointProvider
     *
     * @param $price int 商品の値段
     * @param $usePoint int 利用ポイント
     * @param $addPoint int 期待する付与ポイント
     */
    public function testAddPoint($price, $usePoint, $addPoint)
    {
        $Customer = new Customer();
        $Customer->setPoint(1000);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setCustomer($Customer);
        $Order->setUsePoint($usePoint);
        $Order->addOrderItem($this->newOrderItem($ProductClass, $price, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addItemHolderPreprocessor($this->processor);

        $context = new PurchaseContext(null, $Customer);
        $purchaseFlow->validate($Order, $context);

        self::assertEquals($addPoint, $Order->getAddPoint());
    }

    public function useAddPointProvider()
    {
        return [
            [200, 0, 2],
            [200, 100, 1],
            [200, 200, 0],
            [1000, 0, 10],
            [1000, 100, 9],
            [1000, 200, 8],
        ];
    }

    private function newOrderItem($ProductClass, $price, $quantity)
    {
        $OrderItem = new OrderItem();
        $OrderItem->setProductClass($ProductClass);
        $OrderItem->setPrice($price);
        $OrderItem->setPriceIncTax($price);
        $OrderItem->setQuantity($quantity);

        return $OrderItem;
    }
}
