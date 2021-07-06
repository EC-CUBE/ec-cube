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

namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Entity\ClassCategory;
use Eccube\Entity\ClassName;
use Eccube\Entity\Member;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Repository\DeliveryDurationRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;

/**
 * Class ProductCommon
 */
abstract class AbstractProductCommonTestCase extends AbstractAdminWebTestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @var SaleTypeRepository
     */
    protected $saleTypeRepository;

    /**
     * @var DeliveryDurationRepository
     */
    protected $deliveryDurationRepository;

    /**
     * Set up function
     */
    public function setUp()
    {
        parent::setUp();
        $this->faker = $this->getFaker();
        $this->productStatusRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\ProductStatus::class);
        $this->saleTypeRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\SaleType::class);
        $this->deliveryDurationRepository = $this->entityManager->getRepository(\Eccube\Entity\DeliveryDuration::class);
    }

    /**
     * @param Member $TestCreator
     *
     * @return Product
     */
    protected function createTestProduct(Member $TestCreator = null)
    {
        if (!$TestCreator) {
            $TestCreator = $this->createMember();
        }

        $TestProduct = new Product();
        $ProductStatus = $this->productStatusRepository->find(1);

        $TestProduct->setName($this->faker->word)
            ->setStatus($ProductStatus)
            ->setNote($this->faker->realText(50))
            ->setDescriptionList($this->faker->realText(100))
            ->setDescriptionDetail($this->faker->realText(200))
            ->setFreeArea($this->faker->realText(200))
            ->setCreator($TestCreator);

        $this->entityManager->persist($TestProduct);
        $this->entityManager->flush();

        $ProductClass = new ProductClass();
        $SaleType = $this->saleTypeRepository->find(1);
        $ProductClass->setProduct($TestProduct)
            ->setSaleType($SaleType)
            ->setCode('test code')
            ->setStock(100)
            ->setStockUnlimited(false)
            ->setSaleLimit($this->faker->numberBetween(1, 99))
            ->setPrice01($this->faker->randomNumber(4))
            ->setPrice02($this->faker->randomNumber(4))
            ->setDeliveryFee($this->faker->randomNumber(4))
            ->setCreator($TestCreator)
            ->setVisible(true);

        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        $this->createProductStock($TestCreator, $ProductClass);

        $TestProduct->addProductClass($ProductClass);

        return $TestProduct;
    }

    /**
     * Create class name
     *
     * @param Member|null $Creator
     *
     * @return ClassName
     */
    protected function createClassName(Member $Creator = null)
    {
        if (!$Creator) {
            $Creator = $this->createMember();
        }
        $TestClassName = new ClassName();
        $TestClassName->setName($this->faker->word)
            ->setBackendName($this->faker->word)
            ->setSortNo($this->faker->randomNumber(3))
            ->setCreator($Creator);

        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();

        return $TestClassName;
    }

    /**
     * Create class category
     *
     * @param Member $Creator
     * @param ClassName $TestClassName
     *
     * @return ClassCategory
     */
    protected function createClassCategory(Member $Creator, ClassName &$TestClassName)
    {
        if (!$Creator) {
            $Creator = $this->createMember();
        }
        $TestClassCategory = new ClassCategory();
        $TestClassCategory->setName($this->faker->word)
            ->setSortNo($this->faker->randomNumber(3))
            ->setClassName($TestClassName)
            ->setVisible(true)
            ->setCreator($Creator);

        $this->entityManager->persist($TestClassCategory);
        $this->entityManager->flush();

        $TestClassName->addClassCategory($TestClassCategory);

        return $TestClassCategory;
    }

    /**
     * Create product class
     *
     * @param Member $Creator
     * @param Product $TestProduct
     * @param ClassCategory $TestClassCategory1
     * @param ClassCategory $TestClassCategory2
     *
     * @return ProductClass
     */
    protected function createProductClass(
        Member $Creator,
        Product &$TestProduct,
        ClassCategory $TestClassCategory1,
        ClassCategory $TestClassCategory2
    ) {
        if (!$Creator) {
            $Creator = $this->createMember();
        }
        $DeliveryDurations = $this->deliveryDurationRepository->findAll();
        $ProductClass = new ProductClass();
        $SaleType = $this->saleTypeRepository->find(1);

        $ProductClass->setProduct($TestProduct)
            ->setClassCategory1($TestClassCategory1)
            ->setClassCategory2($TestClassCategory2)
            ->setSaleType($SaleType)
            ->setCode('test')
            ->setStock(100)
            ->setStockUnlimited(false)
            ->setSaleLimit(10)
            ->setPrice01(10000)
            ->setPrice02(5000)
            ->setDeliveryFee(1000)
            ->setDeliveryDuration($DeliveryDurations[$this->faker->numberBetween(0, 8)])
            ->setCreator($Creator)
            ->setVisible(true);

        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        $this->createProductStock($Creator, $ProductClass);

        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        $TestProduct->addProductClass($ProductClass);
        $this->entityManager->persist($TestProduct);
        $this->entityManager->flush();

        return $ProductClass;
    }

    /**
     * Create product stock
     *
     * @param Member $Creator
     * @param ProductClass $TestProductClass
     *
     * @return ProductStock
     */
    protected function createProductStock(Member $Creator, ProductClass &$TestProductClass)
    {
        if (!$Creator) {
            $Creator = $this->createMember();
        }
        $TestProductStock = new ProductStock();
        $TestProductStock->setProductClass($TestProductClass);
        $TestProductStock->setProductClassId($TestProductClass->getId());
        $TestProductStock->setStock($TestProductClass->getStock());
        $TestProductStock->setCreator($Creator);

        $this->entityManager->persist($TestProductStock);
        $this->entityManager->flush();

        $TestProductClass->setProductStock($TestProductStock);

        return $TestProductStock;
    }
}
