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

use Eccube\Entity\CustomerFavoriteProduct;
use Eccube\Tests\EccubeTestCase;
use Eccube\Repository\ProductRepository;

/**
 * ProductRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
abstract class AbstractProductRepositoryTestCase extends EccubeTestCase
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->productRepository = $this->container->get(ProductRepository::class);

        $tables = [
            'dtb_product_image',
            'dtb_product_stock',
            'dtb_product_class',
            'dtb_product_category',
            'dtb_product',
        ];
        $this->deleteAllRows($tables);
        for ($i = 0; $i < 3; $i++) {
            $this->createProduct('商品-'.$i);
        }
    }

    /**
     * Create favorites products for testing
     *
     * @param $Customer
     */
    protected function createFavorites($Customer)
    {
        $Products = $this->productRepository->findAll();
        foreach ($Products as $Product) {
            $Fav = new CustomerFavoriteProduct();
            $Fav->setProduct($Product)
                ->setCustomer($Customer);
            $this->entityManager->persist($Fav);
        }
        $this->entityManager->flush();
    }
}
