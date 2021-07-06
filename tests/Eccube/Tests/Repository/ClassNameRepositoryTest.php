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
use Eccube\Entity\Member;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * ClassNameRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ClassNameRepositoryTest extends EccubeTestCase
{
    /**
     * @var  Member
     */
    protected $Member;

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
        $this->Member = $this->entityManager->getRepository(\Eccube\Entity\Member::class)->find(2);

        for ($i = 0; $i < 3; $i++) {
            $ClassName = new ClassName();
            $ClassName
                ->setName('class-'.$i)
                ->setBackendName('class-'.$i)
                ->setCreator($this->Member)
                ->setSortNo($i)
                ;
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
        $ClassNames = $this->classNameRepository->getList();

        $this->expected = 3;
        $this->actual = count($ClassNames);
        $this->verify('合計数は'.$this->expected.'ではありません');

        $this->actual = [];
        foreach ($ClassNames as $ClassName) {
            $this->actual[] = $ClassName->getSortNo();
        }
        $this->expected = [2, 1, 0];
        $this->verify('ソート順が違います');
    }

    public function testSave()
    {
        $faker = $this->getFaker();
        $ClassName = new ClassName();
        $ClassName
            ->setName($faker->name)
            ->setBackendName($faker->name)
            ->setCreator($this->Member);

        $this->classNameRepository->save($ClassName);

        $this->expected = 3;
        $this->actual = $ClassName->getSortNo();
        $this->verify('sort_no は'.$this->expected.'ではありません');
    }

    public function testSaveWithSortNoNull()
    {
        $this->removeClass();    // 一旦全件削除
        $faker = $this->getFaker();
        $ClassName = new ClassName();
        $ClassName
            ->setName($faker->name)
            ->setBackendName($faker->name)
            ->setCreator($this->Member);

        $this->classNameRepository->save($ClassName);

        $this->expected = 1;
        $this->actual = $ClassName->getSortNo();
        $this->verify('sort_no は'.$this->expected.'ではありません');
    }

    public function testDelete()
    {
        $ClassName = $this->classNameRepository->findOneBy(
            ['backend_name' => 'class-0']
        );
        $ClassNameId = $ClassName->getId();
        $this->classNameRepository->delete($ClassName);

        self::assertNull($this->entityManager->find(ClassName::class, $ClassNameId));
    }

    public function testDeleteWithException()
    {
        $ClassName = new ClassName();
        $ClassName->setName('sample');
        $ClassName->setBackendName('sample');
        $ClassName->setSortNo(100);
        $ClassCateogory = new ClassCategory();
        $ClassCateogory->setClassName($ClassName);
        $ClassCateogory->setName('sample');
        $ClassCateogory->setSortNo(100);
        $ClassCateogory->setVisible(true);

        $this->entityManager->persist($ClassName);
        $this->entityManager->persist($ClassCateogory);
        $this->entityManager->flush([$ClassName, $ClassCateogory]);

        try {
            $this->classNameRepository->delete($ClassName);
            $this->fail();
        } catch (\Exception $e) {
            $this->addToAssertionCount(1);
        }
    }
}
