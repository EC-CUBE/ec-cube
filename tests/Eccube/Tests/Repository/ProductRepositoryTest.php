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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Product;

final class ProductRepositoryTest extends AbstractProductRepositoryTestCase
{
    public function testFindWithSortedClassCategories()
    {
        $Product = $this->createProduct(null, 3);
        $Result = $this->productRepository->findWithSortedClassCategories($Product->getId());
        $this->assertInstanceOf(Product::class, $Result);

        // visible = falseも取得するため, 合計4件.
        $this->assertCount(4, $Result->getProductClasses());

        $this->entityManager->clear();

        $Result = $this->productRepository->findWithSortedClassCategories($Product->getId());
        $this->assertInstanceOf(Product::class, $Result);

        // visible = trueのみ取得する, 合計3件.
        $this->assertCount(3, $Result->getProductClasses());
    }

    public function testGetQueryBuilderBySearchDataForAdminId2147483648()
    {
        $Product = $this->createProduct(null, 1);
        $Product->setName('2147483648');

        $this->productRepository->save($Product);
        $this->entityManager->flush();

        $qb = $this->productRepository->getQueryBuilderBySearchDataForAdmin(['id' => '2147483648']);
        $result = $qb->getQuery()->getResult();

        $this->assertEquals($Product, $result[0]);
    }

    /**
     * Test findWithSortedClassCategories with many product classes (N+1 problem test)
     *
     * This test ensures that ProductStock and TaxRule are eagerly loaded
     * to prevent N+1 queries when Product::_calc() is called.
     */
    public function testFindWithSortedClassCategoriesWithManyProductClasses()
    {
        // Create a product with 100 product classes to simulate N+1 problem scenario
        $Product = $this->createProduct('商品-多規格', 100);

        $this->entityManager->clear();

        // Fetch the product with all relations
        $Result = $this->productRepository->findWithSortedClassCategories($Product->getId());

        // Verify product is loaded
        $this->assertInstanceOf(Product::class, $Result);
        $this->assertSame('商品-多規格', $Result->getName());

        // DBAL 4 では DebugStack / SQLLogger が廃止されたため, DoctrineBundle 標準の
        // doctrine.debug_data_holder (debug middleware が記録するクエリ) でクエリ数を計測する.
        // 自前の doctrine.middleware を追加すると dama のトランザクション分離が壊れるため使わない.
        // クラス名のエイリアスが標準では存在しないため、文字列サービスIDのまま使用
        $serviceId = 'doctrine.debug_data_holder';
        $debugDataHolder = static::getContainer()->get($serviceId);
        $data = $debugDataHolder->getData()['default'] ?? [];
        $queriesBeforeCalc = is_array($data) ? count($data) : count($data->getQueries());

        // Trigger _calc() which accesses ProductStock and TaxRule
        $Result->getStockMin();
        $Result->getStockMax();
        $Result->getPrice02Min();
        $Result->getPrice02Max();

        $data = $debugDataHolder->getData()['default'] ?? [];
        $queriesAfterCalc = is_array($data) ? count($data) : count($data->getQueries());

        // Assert that no additional queries were executed (N+1 problem is solved)
        // If ProductStock and TaxRule are not eagerly loaded, this would cause 200+ additional queries
        $this->assertSame($queriesBeforeCalc, $queriesAfterCalc, 'N+1 problem detected: Additional queries were executed during _calc(). ProductStock and TaxRule should be eagerly loaded.');
    }
}
