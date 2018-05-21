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

use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Entity\Master\ProductStatus;

/**
 * ProductRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ProductRepositoryTest extends AbstractProductRepositoryTestCase
{
    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->productStatusRepository = $this->container->get(ProductStatusRepository::class);
    }

    public function testGetFavoriteProductQueryBuilderByCustomer()
    {
        $this->markTestSkipped(get_class($this).' getFavoriteProductQueryBuilderByCustomer is deprecated since 3.1');
        $Customer = $this->createCustomer();
        $this->entityManager->persist($Customer);

        $this->createFavorites($Customer);

        // 3件中, 1件は非表示にしておく
        $ProductStatus = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
        $Products = $this->productRepository->findAll();
        $Products[0]->setStatus($ProductStatus);
        $this->entityManager->flush();

        $qb = $this->productRepository->getFavoriteProductQueryBuilderByCustomer($Customer);
        $Favorites = $qb
            ->getQuery()
            ->getResult();

        $this->expected = 2;
        $this->actual = count($Favorites);
        $this->verify('お気に入りの件数は'.$this->expected.'件');
    }
}
