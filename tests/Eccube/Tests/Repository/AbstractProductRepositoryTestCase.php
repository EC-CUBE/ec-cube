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

use Eccube\Entity\CustomerFavoriteProduct;
use Eccube\Entity\Product;
use Eccube\Entity\ProductTag;
use Eccube\Entity\Tag;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TagRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * ProductRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
abstract class AbstractProductRepositoryTestCase extends EccubeTestCase
{
    protected ?ProductRepository $productRepository = null;

    protected ?TagRepository $tagRepository = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = $this->entityManager->getRepository(Product::class);
        $this->tagRepository = $this->entityManager->getRepository(Tag::class);
        $tables = [
            'dtb_product_image',
            'dtb_product_stock',
            'dtb_product_class',
            'dtb_product_category',
            'dtb_product',
        ];
        $this->deleteAllRows($tables);
        $this->createProducts(3, [
            'nameTemplate' => fn (int $i): string => '商品-'.$i,
            'withCategoriesAndTags' => true,
        ]);
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

    /**
     * 商品にタグをつける
     */
    protected function setProductTags(Product $Product, array $tagIds)
    {
        $ProductTags = $Product->getProductTag();
        foreach ($ProductTags as $ProductTag) {
            $Product->removeProductTag($ProductTag);
            $this->entityManager->remove($ProductTag);
        }

        $Tags = $this->tagRepository->findBy(['id' => $tagIds]);
        foreach ($Tags as $Tag) {
            $ProductTag = new ProductTag();
            $ProductTag
                ->setProduct($Product)
                ->setTag($Tag);
            $Product->addProductTag($ProductTag);
            $this->entityManager->persist($ProductTag);
        }
        $this->entityManager->flush();
    }
}
