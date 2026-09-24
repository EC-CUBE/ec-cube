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

namespace Eccube\Tests\Doctrine\EventSubscriber;

use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Tests\EccubeTestCase;

/**
 * ProductClass::in_stock が flush 時に在庫数から再計算されることを検証する.
 */
final class ProductClassInStockSubscriberTest extends EccubeTestCase
{
    /** @var list<int|null>|null flush 中に UPDATE された ProductClass の ID */
    private ?array $updatedProductClassIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager->getEventManager()->addEventListener(Events::postUpdate, $this);
    }

    protected function tearDown(): void
    {
        $this->entityManager->getEventManager()->removeEventListener(Events::postUpdate, $this);

        parent::tearDown();
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof ProductClass) {
            $this->updatedProductClassIds[] = $entity->getId();
        }
    }

    public function testInsertWithStock(): void
    {
        $ProductClass = $this->createLimitedProductClass('5');

        $this->assertTrue($this->fetchInStock($ProductClass));
    }

    public function testInsertWithoutStock(): void
    {
        $ProductClass = $this->createLimitedProductClass('0');

        $this->assertFalse($this->fetchInStock($ProductClass));
    }

    public function testInsertUnlimited(): void
    {
        $ProductClass = $this->createLimitedProductClass(null);
        $ProductClass->setStockUnlimited(true);
        $this->entityManager->flush();

        $this->assertTrue($this->fetchInStock($ProductClass));
    }

    public function testBecomesOutOfStock(): void
    {
        $ProductClass = $this->createLimitedProductClass('1');

        $ProductClass->getProductStock()->setStock('0');
        $this->entityManager->flush();

        $this->assertFalse($this->fetchInStock($ProductClass));
    }

    public function testBecomesInStock(): void
    {
        $ProductClass = $this->createLimitedProductClass('0');

        $ProductClass->getProductStock()->setStock('3');
        $this->entityManager->flush();

        $this->assertTrue($this->fetchInStock($ProductClass));
    }

    /**
     * 在庫が 0 をまたがない増減では dtb_product_class を更新しない.
     */
    public function testDoesNotUpdateProductClassWhenInStockUnchanged(): void
    {
        $ProductClass = $this->createLimitedProductClass('5');
        $this->updatedProductClassIds = [];

        $ProductClass->getProductStock()->setStock('4');
        $this->entityManager->flush();

        $this->assertSame([], $this->updatedProductClassIds);
        $this->assertTrue($this->fetchInStock($ProductClass));
    }

    public function testUpdatesProductClassWhenCrossingZero(): void
    {
        $ProductClass = $this->createLimitedProductClass('1');
        $this->updatedProductClassIds = [];

        $ProductClass->getProductStock()->setStock('0');
        $this->entityManager->flush();

        $this->assertSame([$ProductClass->getId()], $this->updatedProductClassIds);
    }

    public function testStockUnlimitedChange(): void
    {
        $ProductClass = $this->createLimitedProductClass('0');

        $ProductClass->setStockUnlimited(true);
        $this->entityManager->flush();
        $this->assertTrue($this->fetchInStock($ProductClass));

        $ProductClass->setStockUnlimited(false);
        $this->entityManager->flush();
        $this->assertFalse($this->fetchInStock($ProductClass));
    }

    /**
     * inverse side (ProductClass::$ProductStock) を付け替えていない場合も, ProductStock 側から計算する.
     */
    public function testUsesOwningSideProductStock(): void
    {
        $ProductClass = $this->createLimitedProductClass('0');
        $OldProductStock = $ProductClass->getProductStock();
        $this->entityManager->remove($OldProductStock);

        $NewProductStock = new ProductStock();
        $NewProductStock->setStock('8');
        $NewProductStock->setProductClass($ProductClass);
        $this->entityManager->persist($NewProductStock);
        $this->entityManager->flush();

        $this->assertTrue($this->fetchInStock($ProductClass));
    }

    /**
     * in_stock を直接書き換えても, 在庫数から再計算した値で保存される.
     */
    public function testCorrectsDirectlyModifiedInStock(): void
    {
        $ProductClass = $this->createLimitedProductClass('0');

        $ProductClass->setInStock(true);
        $this->entityManager->flush();

        $this->assertFalse($this->fetchInStock($ProductClass));
    }

    private function createLimitedProductClass(?string $stock): ProductClass
    {
        $Product = $this->createProduct('in-stock-test', 0);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStockUnlimited(false);
        $ProductClass->setStock($stock);
        $this->entityManager->flush();

        return $ProductClass;
    }

    private function fetchInStock(ProductClass $ProductClass): bool
    {
        $value = $this->entityManager->getConnection()->fetchOne(
            'SELECT in_stock FROM dtb_product_class WHERE id = ?',
            [$ProductClass->getId()]
        );

        return in_array($value, [true, 1, '1', 't', 'true'], true);
    }
}
