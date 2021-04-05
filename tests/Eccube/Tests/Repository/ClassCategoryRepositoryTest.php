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

use Eccube\Entity\ClassCategory;
use Eccube\Entity\ClassName;
use Eccube\Entity\ProductClass;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * ClassCategoryRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ClassCategoryRepositoryTest extends EccubeTestCase
{
    /**
     * @var  ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var  ClassCategoryRepository
     */
    protected $classCategoryRepository;

    /**
     * @var  ClassNameRepository
     */
    protected $classNameRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->productClassRepository = $this->entityManager->getRepository(\Eccube\Entity\ProductClass::class);
        $this->classCategoryRepository = $this->entityManager->getRepository(\Eccube\Entity\ClassCategory::class);
        $this->classNameRepository = $this->entityManager->getRepository(\Eccube\Entity\ClassName::class);
        $this->removeClass();

        for ($i = 0; $i < 3; $i++) {
            $ClassName = new ClassName();
            $ClassName
                ->setName('class-'.$i)
                ->setBackendName('class-'.$i)
                ->setSortNo($i);
            for ($j = 0; $j < 3; $j++) {
                $ClassCategory = new ClassCategory();
                $ClassCategory
                    ->setName('classcategory-'.$i.'-'.$j)
                    ->setVisible(true)
                    ->setSortNo($j)
                    ->setClassName($ClassName);
                $ClassName->addClassCategory($ClassCategory);
                $this->entityManager->persist($ClassCategory);
            }
            $this->entityManager->persist($ClassName);
        }
        $this->entityManager->flush();
    }

    public function removeClass()
    {
        $ProductClasses = $this->productClassRepository->findAll();
        foreach ($ProductClasses as $ProductClass) {
            $this->entityManager->remove($ProductClass);
        }
        $ClassCategories = $this->classCategoryRepository->findAll();
        foreach ($ClassCategories as $ClassCategory) {
            $this->entityManager->remove($ClassCategory);
        }
        $this->entityManager->flush();
        $All = $this->classNameRepository->findAll();
        foreach ($All as $ClassName) {
            $this->entityManager->remove($ClassName);
        }
        $this->entityManager->flush();
    }

    public function testGetList()
    {
        $ClassCategories = $this->classCategoryRepository->getList();

        $this->expected = 9;
        $this->actual = count($ClassCategories);
        $this->verify('合計数は'.$this->expected.'ではありません');

        $this->actual = [];
        foreach ($ClassCategories as $ClassCategory) {
            $this->actual[] = $ClassCategory->getSortNo();
        }
        $this->expected = [2, 2, 2, 1, 1, 1, 0, 0, 0];
        $this->verify('ソート順が違います');
    }

    public function testGetListWithParams()
    {
        $ClassName = $this->classNameRepository->findOneBy(
            ['backend_name' => 'class-1']
        );

        $ClassCategories = $this->classCategoryRepository->getList($ClassName);

        $this->expected = 3;
        $this->actual = count($ClassCategories);
        $this->verify('合計数は'.$this->expected.'ではありません');

        $this->actual = [];
        foreach ($ClassCategories as $ClassCategory) {
            $this->actual[] = $ClassCategory->getName();
        }
        $this->expected = ['classcategory-1-2', 'classcategory-1-1', 'classcategory-1-0'];
        $this->verify('ソート順が違います');
    }

    public function testSave()
    {
        $faker = $this->getFaker();
        $ClassName = $this->classNameRepository->findOneBy(
            ['backend_name' => 'class-1']
        );

        $ClassCategory = new ClassCategory();
        $ClassCategory
            ->setName($faker->name)
            ->setClassName($ClassName);

        $this->classCategoryRepository->save($ClassCategory);

        $this->expected = 3;
        $this->actual = $ClassCategory->getSortNo();
        $this->verify('sort_no は'.$this->expected.'ではありません');
    }

    public function testSaveWithSortNoNull()
    {
        $this->removeClass();    // 一旦全件削除
        $ClassName = new ClassName();
        $ClassName
            ->setName('class-3')
            ->setBackendName('class-3');
        $this->classNameRepository->save($ClassName);

        $faker = $this->getFaker();

        $ClassCategory = new ClassCategory();
        $ClassCategory
            ->setName($faker->name)
            ->setClassName($ClassName);

        $this->classCategoryRepository->save($ClassCategory);

        $this->expected = 1;
        $this->actual = $ClassCategory->getSortNo();
        $this->verify('sort_no は'.$this->expected.'ではありません');
    }

    public function testDelete()
    {
        $ClassCategory = $this->classCategoryRepository->findOneBy(
            ['name' => 'classcategory-1-0']
        );
        $ClassCategoryId = $ClassCategory->getId();
        $this->classCategoryRepository->delete($ClassCategory);

        self::assertNull($this->entityManager->find(ClassCategory::class, $ClassCategoryId));
    }

    public function testDeleteWithException()
    {
        $Product = $this->createProduct();
        /** @var ProductClass[] $ProductClasses */
        $ProductClasses = $Product->getProductClasses();
        foreach ($ProductClasses as $ProductClass) {
            $ClassCategory1 = $ProductClass->getClassCategory1();
            if ($ClassCategory1 === null) {
                continue;
            }
            try {
                // 外部キー制約違反のため例外が発生するはず.
                $this->classCategoryRepository->delete($ClassCategory1);
                $this->fail();
            } catch (\Exception $e) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function testToggleVisibilityToHidden()
    {
        $ClassCategory = $this->classCategoryRepository->findOneBy(
            ['name' => 'classcategory-1-0']
        );
        $ClassCategoryId = $ClassCategory->getId();
        $this->classCategoryRepository->toggleVisibility($ClassCategory);

        $actual = $this->entityManager->find(ClassCategory::class, $ClassCategoryId);
        self::assertFalse($actual->isVisible());
    }

    public function testToggleVisibilityToVisible()
    {
        $ClassCategory = $this->classCategoryRepository->findOneBy(
            ['name' => 'classcategory-1-0']
        );
        $ClassCategory->setVisible(false);
        $this->entityManager->flush($ClassCategory);
        $ClassCategoryId = $ClassCategory->getId();

        $this->classCategoryRepository->toggleVisibility($ClassCategory);

        $actual = $this->entityManager->find(ClassCategory::class, $ClassCategoryId);
        self::assertTrue($actual->isVisible());
    }
}
