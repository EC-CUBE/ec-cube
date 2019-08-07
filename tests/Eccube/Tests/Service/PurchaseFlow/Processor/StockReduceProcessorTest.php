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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\StockReduceProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class StockReduceProcessorTest extends EccubeTestCase
{
    private $processor;

    private $customer;

    public function setUp()
    {
        parent::setUp();
        $this->processor = $this->container->get(StockReduceProcessor::class);
        $this->customer = $this->createCustomer();
    }

    public function testPrepare()
    {
        // 在庫10の商品
        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('test', 1)->getProductClasses()->first();
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(10);
        $ProductClass->getProductStock()->setStock(10);

        // 数量3の受注
        $Order = $this->createOrderWithProductClasses($this->customer, [$ProductClass]);
        $OrderItem = $Order->getProductOrderItems()[0];
        $OrderItem->setQuantity(3);

        $this->processor->prepare($Order, new PurchaseContext());

        // 在庫が減っている
        self::assertEquals(7, $ProductClass->getStock());
        self::assertEquals(7, $ProductClass->getProductStock()->getStock());
    }

    public function testRollback()
    {
        // 在庫7の商品
        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('test', 1)->getProductClasses()->first();
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(7);
        $ProductClass->getProductStock()->setStock(7);

        // 数量3の受注
        $Order = $this->createOrderWithProductClasses($this->customer, [$ProductClass]);
        $OrderItem = $Order->getProductOrderItems()[0];
        $OrderItem->setQuantity(3);

        $this->processor->rollback($Order, new PurchaseContext());

        // 在庫が戻っている
        self::assertEquals(10, $ProductClass->getStock());
        self::assertEquals(10, $ProductClass->getProductStock()->getStock());
    }

    public function testRollbackSameProduct()
    {
        $ProductClass = $this->createProduct('test', 1)
            ->getProductClasses()
            ->first();

        // 在庫3の商品
        /* @var ProductClass $ProductClass */
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(3);
        $ProductClass->getProductStock()->setStock(3);

        // 同一商品で複数の明細がある受注を作成
        $Order = $this->createOrderWithProductClasses($this->customer, [$ProductClass, $ProductClass]);

        // 数量の合計が3の受注
        $Order->getOrderItems()[0]->setQuantity(1);
        $Order->getOrderItems()[1]->setQuantity(2);

        $this->processor->rollback($Order, new PurchaseContext());

        // 在庫が戻っている
        self::assertEquals(6, $ProductClass->getStock());
        self::assertEquals(6, $ProductClass->getProductStock()->getStock());
    }

    public function testRollbackMultipleProducts()
    {
        $Product = $this->createProduct('test', 2);
        $ProductClasses = $Product->getProductClasses();

        // 在庫3の商品
        /* @var ProductClass $ProductClass1 */
        $ProductClass1 = $ProductClasses->first();
        $ProductClass1->setStockUnlimited(false);
        $ProductClass1->setStock(3);
        $ProductClass1->getProductStock()->setStock(3);

        // 在庫4の商品
        /* @var ProductClass $ProductClass2 */
        $ProductClass2 = $ProductClasses->next();
        $ProductClass2->setStockUnlimited(false);
        $ProductClass2->setStock(4);
        $ProductClass2->getProductStock()->setStock(4);

        // 異なる商品で複数の明細がある受注を作成
        $Order = $this->createOrderWithProductClasses($this->customer, [$ProductClass1, $ProductClass2]);

        // 数量の合計が3の受注
        $Order->getOrderItems()[0]->setQuantity(1);
        $Order->getOrderItems()[1]->setQuantity(2);

        $this->processor->rollback($Order, new PurchaseContext());

        // 在庫が戻っている
        self::assertEquals(4, $ProductClass1->getStock());
        self::assertEquals(4, $ProductClass1->getProductStock()->getStock());
        self::assertEquals(6, $ProductClass2->getStock());
        self::assertEquals(6, $ProductClass2->getProductStock()->getStock());
    }
}
