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

use Doctrine\DBAL\LockMode;
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
     * 在庫をロックして更新した場合, 在庫が 0 をまたがない増減では dtb_product_class を更新しない.
     */
    public function testDoesNotUpdateProductClassWhenLockedAndInStockUnchanged(): void
    {
        $ProductClass = $this->createLimitedProductClass('5');
        $this->updatedProductClassIds = [];

        $this->lockProductStock($ProductClass)->setStock('4');
        $this->entityManager->flush();

        $this->assertSame([], $this->updatedProductClassIds);
        $this->assertTrue($this->fetchInStock($ProductClass));
    }

    public function testUpdatesProductClassWhenLockedAndCrossingZero(): void
    {
        $ProductClass = $this->createLimitedProductClass('1');
        $this->updatedProductClassIds = [];

        $this->lockProductStock($ProductClass)->setStock('0');
        $this->entityManager->flush();

        $this->assertSame([$ProductClass->getId()], $this->updatedProductClassIds);
        $this->assertFalse($this->fetchInStock($ProductClass));
    }

    /**
     * 読み込んだ後に別のトランザクションが在庫を 0 にしても, ロック後に在庫を戻せば in_stock も戻る (#7180).
     *
     * 読み込んだ時点の in_stock (true) と再計算した値が同じでも, DB の値 (false) を書き換える.
     */
    public function testRestoresInStockChangedByAnotherTransactionWhenLocked(): void
    {
        $ProductClass = $this->createLimitedProductClass('1');
        $this->assertTrue($ProductClass->isInStock());

        // 別のトランザクションが最後の 1 個を購入した
        $this->updateDirectly($ProductClass, '0', false);

        // 受注キャンセルで在庫を戻す (ロック後に在庫数を読み直す)
        $ProductStock = $this->lockProductStock($ProductClass);
        $this->assertSame('0', $ProductStock->getStock());
        $ProductStock->setStock('1');
        $this->entityManager->flush();

        $this->assertTrue($this->fetchInStock($ProductClass));
    }

    /**
     * ロックを取らない更新 (管理画面や CSV 取り込み) では, 在庫が 0 をまたがなくても in_stock を書き込む (#7180).
     */
    public function testWritesInStockWhenNotLocked(): void
    {
        $ProductClass = $this->createLimitedProductClass('5');

        // 別のトランザクションが在庫を 0 にした
        $this->updateDirectly($ProductClass, '0', false);

        // 読み込んだ時点の在庫数 (5) から上書きする
        $ProductClass->setStock('4');
        $this->entityManager->flush();

        $this->assertTrue($this->fetchInStock($ProductClass));
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
        // Generator が付けるロック済みの目印を外し, DB から読み込んだ直後と同じ状態にする
        $ProductClass->getProductStock()->setProductClassId(null);

        return $ProductClass;
    }

    /**
     * StockReduceProcessor と同じく, 在庫をロックしてから在庫数を読み直す.
     */
    private function lockProductStock(ProductClass $ProductClass): ProductStock
    {
        $ProductStock = $ProductClass->getProductStock();
        if (!$this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->beginTransaction();
        }
        $this->entityManager->lock($ProductStock, LockMode::PESSIMISTIC_WRITE);
        $this->entityManager->refresh($ProductStock);
        $ProductStock->setProductClassId($ProductClass->getId());

        return $ProductStock;
    }

    /**
     * 別のトランザクションによる更新を模して, エンティティを経由せずに書き換える.
     */
    private function updateDirectly(ProductClass $ProductClass, string $stock, bool $inStock): void
    {
        $conn = $this->entityManager->getConnection();
        $conn->executeStatement('UPDATE dtb_product_stock SET stock = ? WHERE product_class_id = ?', [$stock, $ProductClass->getId()]);
        $conn->executeStatement('UPDATE dtb_product_class SET in_stock = ? WHERE id = ?', [$inStock, $ProductClass->getId()], ['boolean']);
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
