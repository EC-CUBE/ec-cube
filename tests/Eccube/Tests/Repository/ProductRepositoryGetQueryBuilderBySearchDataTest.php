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

use Eccube\Entity\Category;
use Eccube\Entity\Master\ProductListMax;
use Eccube\Entity\Master\ProductListOrderBy;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\Master\ProductListOrderByRepository;
use Knp\Component\Pager\Paginator;

/**
 * ProductRepository#getQueryBuilderBySearchData test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ProductRepositoryGetQueryBuilderBySearchDataTest extends AbstractProductRepositoryTestCase
{
    /**
     * @var array
     */
    protected $Results;

    /**
     * @var array
     */
    protected $searchData;

    /**
     * @var ProductListMax
     */
    protected $ProductListMax;

    /**
     * @var ProductListOrderBy
     */
    protected $ProductListOrderBy;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ProductListOrderByRepository
     */
    protected $productListOrderByRepository;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->categoryRepository = $this->entityManager->getRepository(\Eccube\Entity\Category::class);
        $this->productListOrderByRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\ProductListOrderBy::class);
        $this->paginator = self::$container->get('knp_paginator');

        $this->ProductListMax = new ProductListMax();
        $this->ProductListOrderBy = new ProductListOrderBy();
    }

    public function scenario()
    {
        $this->Results = $this->productRepository->getQueryBuilderBySearchData($this->searchData)
            ->getQuery()
            ->getResult();
    }

    public function testCategory()
    {
        $Categories = $this->categoryRepository->findAll();
        $this->searchData = [
            'category_id' => $Categories[0],
        ];
        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCategoryWithOut()
    {
        $Category = new Category();
        $Category
            ->setName('test')
            ->setSortNo(1)
            ->setHierarchy(1)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());
        $this->entityManager->persist($Category);
        $this->entityManager->flush();

        $this->searchData = [
            'category_id' => $Category,
        ];
        $this->scenario();

        $this->expected = 0;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testName()
    {
        $Products = $this->productRepository->findAll();
        $Products[0]->setName('りんご');
        $Products[1]->setName('アイス');
        $Products[1]->setSearchWord('抹茶');
        $Products[2]->setName('お鍋');
        $Products[2]->setSearchWord('立方体');
        $this->entityManager->flush();

        $this->searchData = [
            'name' => 'お鍋　立方体',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testOrderByPrice()
    {
        $Products = $this->productRepository->findAll();
        $Products[0]->setName('りんご');
        foreach ($Products[0]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(100);
        }
        $Products[1]->setName('アイス');
        foreach ($Products[1]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(1000);
        }
        $Products[2]->setName('お鍋');
        foreach ($Products[2]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(10000);
        }
        $this->entityManager->flush();

        $ProductListOrderBy = $this->productListOrderByRepository->find(1);
        $this->searchData = [
            'orderby' => $ProductListOrderBy,
        ];

        $this->scenario();

        $this->expected = ['りんご', 'アイス', 'お鍋'];
        $this->actual = [$this->Results[0]->getName(),
                              $this->Results[1]->getName(),
                              $this->Results[2]->getName(), ];
        $this->verify();
    }

    /**
     * 価格が高い順のソート
     */
    public function testOrderByPriceHigher()
    {
        $Products = $this->productRepository->findAll();
        $Products[0]->setName('りんご');
        foreach ($Products[0]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(100);
        }
        $Products[1]->setName('アイス');
        foreach ($Products[1]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(1000);
        }
        $Products[2]->setName('お鍋');
        foreach ($Products[2]->getProductClasses() as $ProductClass) {
            $ProductClass->setPrice02(10000);
        }
        $this->entityManager->flush();

        $ProductListOrderBy = $this->productListOrderByRepository
            ->find($this->eccubeConfig['eccube_product_order_price_higher']);
        $this->searchData = [
            'orderby' => $ProductListOrderBy,
        ];

        $this->scenario();

        $this->expected = ['お鍋', 'アイス', 'りんご'];
        $this->actual = [
            $this->Results[0]->getName(),
            $this->Results[1]->getName(),
            $this->Results[2]->getName(),
        ];
        $this->verify();
    }

    public function testOrderByNewer()
    {
        $Products = $this->productRepository->findAll();
        $Products[0]->setName('りんご');
        $Products[0]->setCreateDate(new \DateTime('-1 day'));
        $Products[1]->setName('アイス');
        $Products[1]->setCreateDate(new \DateTime('-2 day'));
        $Products[2]->setName('お鍋');
        $Products[2]->setCreateDate(new \DateTime('-3 day'));
        $this->entityManager->flush();

        // 新着順
        $ProductListOrderBy = $this->productListOrderByRepository->find(2);
        $this->searchData = [
            'orderby' => $ProductListOrderBy,
        ];

        $this->scenario();

        $this->expected = ['りんご', 'アイス', 'お鍋'];
        $this->actual = [$this->Results[0]->getName(),
                              $this->Results[1]->getName(),
                              $this->Results[2]->getName(), ];

        $this->verify();
    }

    public function testOrderByNewerSameCreateDate()
    {
        $date = new \DateTime();
        $Products = $this->productRepository->findBy([], ['id' => 'DESC']);
        $Products[0]->setName('りんご');
        $Products[0]->setCreateDate($date);
        $Products[1]->setName('アイス');
        $Products[1]->setCreateDate($date);
        $Products[2]->setName('お鍋');
        $Products[2]->setCreateDate($date);
        $this->entityManager->flush();

        // 新着順
        $ProductListOrderBy = $this->entityManager->find(ProductListOrderBy::class, 2);
        $this->searchData = [
            'orderby' => $ProductListOrderBy,
        ];

        $this->scenario();

        $this->expected = ['りんご', 'アイス', 'お鍋'];
        $this->actual = [$this->Results[0]->getName(),
            $this->Results[1]->getName(),
            $this->Results[2]->getName(), ];

        $this->verify();
    }

    public function testProductImage()
    {
        $this->searchData = [];

        $this->scenario();

        $Products = $this->Results;

        foreach ($Products as $Product) {
            $this->expected = [0, 1, 2];
            $this->actual = [];

            $ProductImages = $Product->getProductImage();
            foreach ($ProductImages as $ProductImage) {
                $this->actual[] = $ProductImage->getSortNo();
            }

            $this->verify();
        }
    }

    public function testPaginationEventByOrderPrice()
    {
        $this->ProductListMax->setId(15);
        $this->ProductListMax->setName('15件');
        $this->ProductListMax->setSortNo(0);

        $this->ProductListOrderBy->setId(1);
        $this->ProductListOrderBy->setName('価格順');
        $this->ProductListOrderBy->setSortNo(0);

        $this->searchData = [
            'mode' => null,
            'category_id' => null,
            'name' => null,
            'pageno' => '1',
            'disp_number' => $this->ProductListMax,
            'orderby' => $this->ProductListOrderBy,
        ];
        $this->scenario();

        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
        $pagination = $this->paginator->paginate(
            $this->Results,
            $this->searchData['pageno'],
            $this->searchData['disp_number']->getId()
        );

        $this->expected = count($pagination);
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testPaginationEventByOrderCreated()
    {
        $this->ProductListMax->setId(15);
        $this->ProductListMax->setName('15件');
        $this->ProductListMax->setSortNo(0);

        $this->ProductListOrderBy->setId(2);
        $this->ProductListOrderBy->setName('新着順');
        $this->ProductListOrderBy->setSortNo(0);

        $this->searchData = [
            'mode' => null,
            'category_id' => null,
            'name' => null,
            'pageno' => '1',
            'disp_number' => $this->ProductListMax,
            'orderby' => $this->ProductListOrderBy,
        ];
        $this->scenario();

        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
        $pagination = $this->paginator->paginate(
            $this->Results,
            $this->searchData['pageno'],
            $this->searchData['disp_number']->getId()
        );

        $this->expected = count($pagination);
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function test300ProductsList()
    {
        $tables = [
            'dtb_product_image',
            'dtb_product_stock',
            'dtb_product_class',
            'dtb_product_category',
            'dtb_product_tag',
            'dtb_product',
        ];
        $this->deleteAllRows($tables);
        $productList = [];
        for ($i = 0; $i <= 5; $i++) {
            $classNo = mt_rand(1, 3);
            $productName = '商品-'.$i;
            $Product = $this->createProduct($productName, $classNo);
            $productList[] = $Product->getName();
        }
        $productList = array_reverse($productList);

        // 商品作成時間同じにする
        $QueryBuilder = $this->entityManager->createQueryBuilder();
        $QueryBuilder->update('Eccube\Entity\Product', 'p');
        $QueryBuilder->set('p.create_date', ':createDate');
        $QueryBuilder->setParameter(':createDate', new \DateTime());
        $QueryBuilder->getQuery()->execute();

        // 新着順
        $ProductListOrderBy = $this->entityManager->find(ProductListOrderBy::class, 2);
        $this->searchData = [
            'name' => '商品-',
            'orderby' => $ProductListOrderBy,
        ];

        $this->scenario();
        $this->expected = [];
        foreach ($productList as $productName) {
            $this->expected[] = $productName;
        }

        $this->actual = [];
        foreach ($this->Results as $row) {
            $this->actual[] = $row->getName();
        }
        $this->verify();
    }
}
