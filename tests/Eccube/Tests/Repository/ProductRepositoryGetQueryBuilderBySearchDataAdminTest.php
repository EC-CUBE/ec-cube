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

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\Category;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\ProductStock;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\CategoryRepository;

/**
 * ProductRepository#getQueryBuilderBySearchDataAdmin test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ProductRepositoryGetQueryBuilderBySearchDataAdminTest extends AbstractProductRepositoryTestCase
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
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->productStatusRepository = $this->container->get(ProductStatusRepository::class);
        $this->categoryRepository = $this->container->get(CategoryRepository::class);
    }

    public function scenario()
    {
        $this->Results = $this->productRepository->getQueryBuilderBySearchDataForAdmin($this->searchData)
            ->getQuery()
            ->getResult();
    }

    public function testId()
    {
        $Product = $this->productRepository->findOneBy(['name' => '商品-2']);
        $id = $Product->getId();

        $this->searchData = [
            'id' => $id,
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCode()
    {
        $Products = $this->productRepository->findAll();
        $Products[0]->setName('りんご');
        foreach ($Products[0]->getProductClasses() as $ProductClass) {
            $ProductClass->setCode('dessert-1');
        }
        $Products[1]->setName('アイス');
        foreach ($Products[1]->getProductClasses() as $ProductClass) {
            $ProductClass->setCode('dessert-2');
        }
        $Products[2]->setName('お鍋');
        foreach ($Products[2]->getProductClasses() as $ProductClass) {
            $ProductClass->setCode('onabe-1');
        }
        $this->entityManager->flush();

        $this->searchData = [
            'id' => 'dessert-',
        ];
        $this->scenario();

        $this->expected = 2;
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
            'id' => 'お',
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStatus()
    {
        $Product = $this->productRepository->findOneBy(['name' => '商品-1']);
        $ProductStatus = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
        $Product->setStatus($ProductStatus);
        $this->entityManager->flush();

        $Status = new ArrayCollection([$ProductStatus]);
        $this->searchData = [
            'status' => $Status,
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testLinkStatus()
    {
        $Product = $this->productRepository->findOneBy(['name' => '商品-1']);
        $ProductStatus = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
        $Product->setStatus($ProductStatus);
        $this->entityManager->flush();

        $this->searchData = [
            'link_status' => ProductStatus::DISPLAY_HIDE,
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStockStatus()
    {
        $faker = $this->getFaker();
        // 全商品の在庫を 1 以上にしておく
        $Products = $this->productRepository->findAll();
        foreach ($Products as $Product) {
            foreach ($Product->getProductClasses() as $ProductClass) {
                $ProductClass
                    ->setStockUnlimited(false)
                    ->setStock($faker->numberBetween(1, 999));
            }
        }
        $this->entityManager->flush();

        // 1商品だけ 0 に設定する
        $Product = $this->productRepository->findOneBy(['name' => '商品-1']);
        foreach ($Product->getProductClasses() as $ProductClass) {
            $ProductClass
                ->setStockUnlimited(false)
                ->setStock(0);
        }
        $this->entityManager->flush();

        $this->searchData = [
            'stock' => [ProductStock::OUT_OF_STOCK],
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testStockStatusWithUnlimited()
    {
        $faker = $this->getFaker();
        // 全商品の在庫をなしにする
        $Products = $this->productRepository->findAll();
        foreach ($Products as $Product) {
            foreach ($Product->getProductClasses() as $ProductClass) {
                $ProductClass
                    ->setStockUnlimited(false)
                    ->setStock(0);
            }
        }
        $this->entityManager->flush();

        // 1商品だけ無制限に設定する
        $Product = $this->productRepository->findOneBy(['name' => '商品-1']);
        foreach ($Product->getProductClasses() as $ProductClass) {
            $ProductClass
                ->setStockUnlimited(true)
                ->setStock(0);
        }
        $this->entityManager->flush();

        $this->searchData = [
            'stock' => [ProductStock::IN_STOCK],
        ];
        $this->scenario();

        $this->expected = 1;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCreateDateStart()
    {
        $this->searchData = [
            'create_date_start' => new \DateTime('- 1 days'),
        ];

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testCreateDateEnd()
    {
        $this->searchData = [
            'create_date_end' => new \DateTime('+ 1 days'),
        ];

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateStart()
    {
        $this->searchData = [
            'update_date_start' => new \DateTime('- 1 days'),
        ];

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
    }

    public function testUpdateDateEnd()
    {
        $this->searchData = [
            'update_date_end' => new \DateTime('+ 1 days'),
        ];

        $this->scenario();

        $this->expected = 3;
        $this->actual = count($this->Results);
        $this->verify();
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
}
