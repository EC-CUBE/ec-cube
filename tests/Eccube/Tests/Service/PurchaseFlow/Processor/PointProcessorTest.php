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
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\PointProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Tests\EccubeTestCase;

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

    public function testAddPoint()
    {
        $Cart = new Cart();
        $ProductClasses = $this->createProduct('テスト商品', 3)->getProductClasses();

        $Cart->addCartItem($this->newCartItem($ProductClasses[0], 1000, 1));
        $Cart->addCartItem($this->newCartItem($ProductClasses[1], 100, 2));
        $Cart->addCartItem($this->newCartItem($ProductClasses[2], 10, 3));

        $this->processor->process($Cart, new PurchaseContext());
        self::assertEquals(12, $Cart->getAddPoint());
    }

    public function testUsePoint()
    {
        $Order = new Order();
        $Order->setUsePoint(100);
        $this->processor->process($Order, new PurchaseContext());

        /** @var OrderItem $OrderItem */
        $OrderItem = $Order->getOrderItems()->filter(
            function (OrderItem $OrderItem) {
                return $OrderItem->getProductName() == 'ポイント値引';
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
        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setUsePoint($usePoint);
        $Order->addOrderItem($this->newOrderItem($ProductClass, 1000, 1));

        $Customer = new Customer();
        $Customer->setPoint($customerPoint);

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
     * @param $isError エラーかどうか
     */
    public function testUsePointOverPrice($usePoint, $isError)
    {
        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setUsePoint($usePoint);
        $Order->addOrderItem($this->newOrderItem($ProductClass, 100, 1));

        $Customer = new Customer();
        $Customer->setPoint(10000);

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addItemHolderValidator($this->processor);
        $result = $purchaseFlow->validate($Order, new PurchaseContext(null, $Customer));

        self::assertEquals($isError, $result->hasError());

        if ($isError) {
            self::assertEquals('利用ポイントがお支払い金額を上回っています.', $result->getErrors()[0]->getMessage());
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
        $this->markTestIncomplete('prepare');

        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()[0];
        $Order = new Order();
        $Order->setUsePoint(0);
        $Order->addOrderItem($this->newOrderItem($ProductClass, 100, 1));
        $Order->setAddPoint(10);

        $Customer = new Customer();
        $Customer->setPoint(100);

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->addItemHolderValidator($this->processor);

        $context = new PurchaseContext(null, $Customer);
        $purchaseFlow->validate($Order, $context);
        $purchaseFlow->purchase($Order, $context);

        self::assertEquals(90, $Customer->getPoint());
    }

    private function newCartItem(ProductClass $ProductClass, $price, $quantity)
    {
        $cartItem = new CartItem();
        $cartItem->setProductClass($ProductClass);
        $cartItem->setPrice($price);
        $cartItem->setQuantity($quantity);

        return $cartItem;
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
