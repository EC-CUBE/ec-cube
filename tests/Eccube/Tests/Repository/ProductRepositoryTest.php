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

namespace Eccube\Tests\Repository;

class ProductRepositoryTest extends AbstractProductRepositoryTestCase
{
    public function testFindWithSortedClassCategories()
    {
        $Product = $this->createProduct(null, 3);
        $Result = $this->productRepository->findWithSortedClassCategories($Product->getId());

        // visible = falseも取得するため, 合計4件.
        self::assertCount(4, $Result->getProductClasses());

        $this->entityManager->clear();

        $Result = $this->productRepository->findWithSortedClassCategories($Product->getId());

        // visible = trueのみ取得する, 合計3件.
        self::assertCount(3, $Result->getProductClasses());
    }

    public function testGetQueryBuilderBySearchDataForAdminId2147483648()
    {
        $Product = $this->createProduct(null, 1);
        $Product->setName('2147483648');

        $this->productRepository->save($Product);
        $this->entityManager->flush();

        $qb = $this->productRepository->getQueryBuilderBySearchDataForAdmin(['id' => '2147483648']);
        $result = $qb->getQuery()->getResult();

        self::assertEquals($Product, $result[0]);
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

        // Enable Doctrine query logger to count queries
        $logger = new \Doctrine\DBAL\Logging\DebugStack();
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger($logger);

        $this->entityManager->clear();

        // Fetch the product with all relations
        $Result = $this->productRepository->findWithSortedClassCategories($Product->getId());

        // Verify product is loaded
        self::assertNotNull($Result);
        self::assertSame('商品-多規格', $Result->getName());

        // Clear the query log for the next test
        $queriesBeforeCalc = count($logger->queries);

        // Trigger _calc() which accesses ProductStock and TaxRule
        $Result->getStockMin();
        $Result->getStockMax();
        $Result->getPrice02Min();
        $Result->getPrice02Max();

        $queriesAfterCalc = count($logger->queries);

        // Assert that no additional queries were executed (N+1 problem is solved)
        // If ProductStock and TaxRule are not eagerly loaded, this would cause 200+ additional queries
        self::assertSame(
            $queriesBeforeCalc,
            $queriesAfterCalc,
            'N+1 problem detected: Additional queries were executed during _calc(). ProductStock and TaxRule should be eagerly loaded.'
        );

        // Disable logger
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
    }
}
