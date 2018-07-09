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
        // 在庫7の商品
        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('test', 1)->getProductClasses()[0];
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock(7);
        $ProductClass->getProductStock()->setStock(7);

        // 数量3の受注
        $Customer = $this->createCustomer();
        $Order = $this->createOrderWithProductClasses($Customer, [$ProductClass]);
        $OrderItem = $Order->getProductOrderItems()[0];
        $OrderItem->setQuantity(3);

        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        $this->processor->rollback($Order, new PurchaseContext());

        // 在庫が戻っている
        $ProductClass = $this->entityManager->find(ProductClass::class, $ProductClass->getId());
        self::assertEquals(10, $ProductClass->getStock());
    }
}
