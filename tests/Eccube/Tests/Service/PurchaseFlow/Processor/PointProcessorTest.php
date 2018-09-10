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
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\ProductClass;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Service\PurchaseFlow\Processor\AddPointProcessor;
use Eccube\Service\PurchaseFlow\Processor\PointProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Tests\EccubeTestCase;

class PointProcessorTest extends EccubeTestCase
{
    /** @var PointProcessor */
    private $processor;

    /** @var AddPointProcessor */
    private $addPointProcessor;

    /** @var BaseInfo */
    private $BaseInfo;

    public function setUp()
    {
        parent::setUp();
        $this->processor = $this->container->get(PointProcessor::class);
        $this->addPointProcessor = $this->container->get(AddPointProcessor::class);
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
    }

    public function testUsePointA()
    {
        $Customer = new Customer();
        $Customer->setPoint(1000);
        $Order = new Order();
        $Order->setTotal(1000);
        $Order->setCustomer($Customer);
        $Order->setUsePoint(100);
        $this->processor->removeDiscountItem($Order, new PurchaseContext(null, $Customer));
        $this->processor->addDiscountItem($Order, new PurchaseContext(null, $Customer));

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
    public function testUsePointOverCustomerPointShoppingFlow($usePoint, $customerPoint, $isError)
    {
        $Customer = new Customer();
        $Customer->setPoint($customerPoint);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setTotal(1000);
        $Order->setCustomer($Customer);
        $Order->setUsePoint($usePoint);
        $Order->addOrderItem($this->newOrderItem($ProductClass, 1000, 1));

        $context = new PurchaseContext(null, $Customer);
        $context->setFlowType(PurchaseContext::SHOPPING_FLOW);
        $this->processor->removeDiscountItem($Order, $context);
        $result = $this->processor->addDiscountItem($Order, $context);

        if ($isError) {
            self::assertEquals($isError, $result->isWarning());
            self::assertEquals('利用ポイントが所有ポイントを上回っています', $result->getMessage());
        } else {
            self::assertNull($result);
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
        $Order->setTotal(100);
        $Order->setCustomer($Customer);
        $Order->setUsePoint($usePoint);
        $Order->addOrderItem($this->newOrderItem($ProductClass, $price, 1));

        $context = new PurchaseContext(null, $Customer);
        $context->setFlowType(PurchaseContext::ORDER_FLOW);
        $this->processor->removeDiscountItem($Order, $context);
        $result = $this->processor->addDiscountItem($Order, $context);

        if ($isError) {
            self::assertEquals($isError, $result->isError());
            self::assertEquals('利用ポイントがお支払い金額を上回っています', $result->getMessage());
            self::assertEquals($usePoint, $Order->getUsePoint());
        } else {
            self::assertNull($result);
            self::assertEquals($usePoint, $Order->getUsePoint());
        }
    }

    /**
     * @dataProvider usePointOverPriceProvider
     *
     * @param $usePoint int 利用ポイント
     * @param $isError boolean エラーかどうか
     */
    public function testUsePointOverPriceShoppingFlow($usePoint, $isError)
    {
        $price = 100; // 商品の値段

        $Customer = new Customer();
        $Customer->setPoint(10000);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setTotal(100);
        $Order->setCustomer($Customer);
        $Order->setUsePoint($usePoint);
        $Order->addOrderItem($this->newOrderItem($ProductClass, $price, 1));

        $context = new PurchaseContext(null, $Customer);
        $context->setFlowType(PurchaseContext::SHOPPING_FLOW);
        $this->processor->removeDiscountItem($Order, $context);
        $result = $this->processor->addDiscountItem($Order, $context);

        if ($isError) {
            self::assertEquals($isError, $result->isWarning());
            self::assertEquals('利用ポイントがお支払い金額を上回っています', $result->getMessage());
            self::assertEquals($price, $Order->getUsePoint());
        } else {
            self::assertNull($result);
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
        $purchaseFlow->addDiscountProcessor($this->processor);
        $purchaseFlow->addItemHolderPostValidator($this->addPointProcessor);

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

    /**
     * @dataProvider useAddPointExcludeShippingFeeProvider
     *
     * @param $price int 商品の値段
     * @param $deliveryFee int
     * @param $addPoint int 期待する付与ポイント
     */
    public function testAddPointExcludeShippingFee($price, $deliveryFee, $addPoint)
    {
        $Customer = new Customer();
        $Customer->setPoint(1000);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setCustomer($Customer);
        $Order->setUsePoint(0);
        $Order->addOrderItem($this->newOrderItem($ProductClass, $price, 1));
        // add shipping fee
        $DeliveryFeeType = $this->entityManager
            ->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $TaxInclude = $this->entityManager
            ->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxation = $this->entityManager
            ->find(TaxType::class, TaxType::TAXATION);
        $OrderItem = new OrderItem();
        $OrderItem->setProductName($DeliveryFeeType->getName())
            ->setPrice($deliveryFee)
            ->setQuantity(1)
            ->setOrderItemType($DeliveryFeeType)
            ->setOrder($Order)
            ->setTaxDisplayType($TaxInclude)
            ->setTaxType($Taxation);
        $Order->addOrderItem($OrderItem);

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addItemHolderPostValidator($this->addPointProcessor);

        $context = new PurchaseContext(null, $Customer);
        $purchaseFlow->validate($Order, $context);

        self::assertEquals($addPoint, $Order->getAddPoint());
    }

    public function useAddPointExcludeShippingFeeProvider()
    {
        return [
            [200, 200, 2],
            [400, 0, 4],
            [100, 20000, 1],
        ];
    }

    /**
     * ポイント換算レートのテスト
     *
     * @dataProvider pointConversionRateProvider
     *
     * @param $pointConversionRate int 商品の値段
     *
     * @throws \Eccube\Service\PurchaseFlow\PurchaseException
     */
    public function testPointConversionRate($pointConversionRate)
    {
        $productPrice = 1000;
        $usePoint = 10;

        $this->BaseInfo->setPointConversionRate($pointConversionRate);

        $Customer = new Customer();
        $Customer->setPoint(1000);

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setCustomer($Customer);
        $Order->setUsePoint($usePoint);
        $Order->addOrderItem($this->newOrderItem($ProductClass, $productPrice, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addDiscountProcessor($this->processor);
        $purchaseFlow->addPurchaseProcessor($this->processor);

        $context = new PurchaseContext(null, $Customer);
        $purchaseFlow->validate($Order, $context);
        $purchaseFlow->prepare($Order, $context);
        $purchaseFlow->commit($Order, $context);

        /** @var OrderItem $OrderItem */
        $OrderItem = $Order->getOrderItems()->filter(
            function (OrderItem $OrderItem) {
                return $OrderItem->isPoint();
            }
        )->first();

        $discountPrice = $usePoint * $pointConversionRate * -1;
        self::assertEquals($discountPrice, $OrderItem->getPrice());
        self::assertEquals($productPrice + $discountPrice, $Order->getTotal());
    }

    public function pointConversionRateProvider()
    {
        return [
            [1],
            [2],
            [5],
        ];
    }

    /**
     * ポイント付与率のテスト
     *
     * @dataProvider basicPointRateProvider
     *
     * @param $basicPointRate int 商品の値段
     */
    public function testBasicPointRate($basicPointRate)
    {
        $ProductPrice = 1000;

        $this->BaseInfo->setBasicPointRate($basicPointRate);

        $Customer = new Customer();

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setCustomer($Customer);
        $Order->addOrderItem($this->newOrderItem($ProductClass, $ProductPrice, 1));

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addItemHolderPostValidator($this->addPointProcessor);

        $context = new PurchaseContext(null, $Customer);
        $purchaseFlow->validate($Order, $context);

        self::assertEquals($ProductPrice * $basicPointRate / 100, $Order->getAddPoint());
    }

    public function basicPointRateProvider()
    {
        return [
            [1],
            [2],
            [10],
        ];
    }

    private function newOrderItem($ProductClass, $price, $quantity)
    {
        $OrderItem = new OrderItem();
        $OrderItem->setProductClass($ProductClass);
        $OrderItem->setPrice($price);
        $OrderItem->setQuantity($quantity);
        $ProductType = $this->container->get(OrderItemTypeRepository::class)->find(OrderItemType::PRODUCT);
        $OrderItem->setOrderItemType($ProductType);

        return $OrderItem;
    }
}
