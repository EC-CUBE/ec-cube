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
}
