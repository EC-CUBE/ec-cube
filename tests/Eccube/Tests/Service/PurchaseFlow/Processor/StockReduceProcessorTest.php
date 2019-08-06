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

    public function setUp()
    {
        parent::setUp();
        $this->processor = $this->container->get(StockReduceProcessor::class);
    }

    public function testPrepare()
    {
        // 在庫10の商品
        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('test', 1)->getProductClasses()[0];
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(10);
        $ProductClass->getProductStock()->setStock(10);

        // 数量3の受注
        $Customer = $this->createCustomer();
        $Order = $this->createOrderWithProductClasses($Customer, [$ProductClass]);
        $OrderItem = $Order->getProductOrderItems()[0];
        $OrderItem->setQuantity(3);

        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        $this->processor->prepare($Order, new PurchaseContext());

        // 在庫が減っている
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        self::assertEquals(7, $ProductClass->getStock());
    }

    public function testRollback()
    {
        $Product = $this->createProduct('test', 2);
        $ProductClasses = $Product->getProductClasses();

        // 在庫3の商品
        $ProductClass = $ProductClasses->first();
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(3);
        $ProductClass->getProductStock()->setStock(3);

        // 同一商品で複数の明細がある受注を作成
        $Customer = $this->createCustomer();
        $Order = $this->createOrderWithProductClasses($Customer, [$ProductClass, $ProductClass]);

        // 数量の合計が3の受注
        $Order->getOrderItems()[0]->setQuantity(1);
        $Order->getOrderItems()[1]->setQuantity(2);

        $this->processor->rollback($Order, new PurchaseContext());

        // 在庫が戻っている
        self::assertEquals(6, $ProductClass->getStock());
        self::assertEquals(6, $ProductClass->getProductStock()->getStock());
    }
}
