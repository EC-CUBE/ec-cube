<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Entity;

use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use PHPUnit\Framework\TestCase;

/**
 * ProductClass の在庫数が ProductStock へ委譲されることを検証する.
 */
final class ProductClassStockTest extends TestCase
{
    public function testGetStockReturnsNullWithoutProductStock(): void
    {
        $ProductClass = new ProductClass();

        $this->assertNotInstanceOf(ProductStock::class, $ProductClass->getProductStock());
        $this->assertNull($ProductClass->getStock());
    }

    public function testSetStockCreatesProductStock(): void
    {
        $ProductClass = new ProductClass();
        $ProductClass->setStock('10');

        $ProductStock = $ProductClass->getProductStock();
        $this->assertInstanceOf(ProductStock::class, $ProductStock);
        $this->assertSame($ProductClass, $ProductStock->getProductClass());
        $this->assertSame('10', $ProductStock->getStock());
        $this->assertSame('10', $ProductClass->getStock());
    }

    public function testSetStockUpdatesExistingProductStock(): void
    {
        $ProductStock = new ProductStock();
        $ProductStock->setStock('3');
        $ProductClass = new ProductClass();
        $ProductClass->setProductStock($ProductStock);
        $ProductStock->setProductClass($ProductClass);

        $ProductClass->setStock('7');

        $this->assertSame($ProductStock, $ProductClass->getProductStock());
        $this->assertSame('7', $ProductStock->getStock());
    }

    public function testGetStockReadsProductStock(): void
    {
        $ProductStock = new ProductStock();
        $ProductStock->setStock('5');
        $ProductClass = new ProductClass();
        $ProductClass->setProductStock($ProductStock);

        // ProductStock 側を直接書き換えても ProductClass から同じ値が見える
        $ProductStock->setStock('4');

        $this->assertSame('4', $ProductClass->getStock());
    }

    public function testSetStockNullForUnlimited(): void
    {
        $ProductClass = new ProductClass();
        $ProductClass->setStockUnlimited(true);
        $ProductClass->setStock();

        $this->assertInstanceOf(ProductStock::class, $ProductClass->getProductStock());
        $this->assertNull($ProductClass->getStock());
        $this->assertTrue($ProductClass->getStockFind());
    }

    /**
     * 複製した規格の在庫数を変更しても, 複製元の在庫数は変わらない.
     */
    public function testCloneSeparatesProductStock(): void
    {
        $ProductClass = new ProductClass();
        $ProductClass->setStock('10');
        $ProductClass->getProductStock()->setProductClassId(1);

        $CopyClass = clone $ProductClass;
        $CopyClass->setStock('3');

        $this->assertSame('10', $ProductClass->getStock());
        $this->assertSame('3', $CopyClass->getStock());
        $this->assertNotSame($ProductClass->getProductStock(), $CopyClass->getProductStock());
        $this->assertSame($CopyClass, $CopyClass->getProductStock()->getProductClass());
        $this->assertSame($ProductClass, $ProductClass->getProductStock()->getProductClass());
        $this->assertNull($CopyClass->getProductStock()->getId());
        $this->assertNull($CopyClass->getProductStock()->getProductClassId());
    }

    public function testCloneWithoutProductStock(): void
    {
        $CopyClass = clone new ProductClass();

        $this->assertNotInstanceOf(ProductStock::class, $CopyClass->getProductStock());
    }

    public function testGetStockFind(): void
    {
        $ProductClass = new ProductClass();
        $ProductClass->setStock('0');
        $this->assertFalse($ProductClass->getStockFind());

        $ProductClass->setStock('1');
        $this->assertTrue($ProductClass->getStockFind());
    }
}
