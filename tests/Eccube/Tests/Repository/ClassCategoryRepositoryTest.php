<?php

namespace Eccube\Tests\Repository;

use Eccube\Entity\ProductClass;
use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\ClassCategory;
use Eccube\Entity\ClassName;

/**
 * ClassCategoryRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ClassCategoryRepositoryTest extends EccubeTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->removeClass();

        for ($i = 0; $i < 3; $i++) {
            $ClassName = new ClassName();
            $ClassName
                ->setName('class-'.$i)
                ->setRank($i);
            for ($j = 0; $j < 3; $j++) {
                $ClassCategory = new ClassCategory();
                $ClassCategory
                    ->setName('classcategory-'.$i.'-'.$j)
                    ->setVisible(true)
                    ->setRank($j)
                    ->setClassName($ClassName);
                $ClassName->addClassCategory($ClassCategory);
                $this->app['orm.em']->persist($ClassCategory);
            }
            $this->app['orm.em']->persist($ClassName);
        }
        $this->app['orm.em']->flush();
    }

    public function removeClass()
    {
        $ProductClasses = $this->app['eccube.repository.product_class']->findAll();
        foreach ($ProductClasses as $ProductClass) {
            $this->app['orm.em']->remove($ProductClass);
        }
        $ClassCategories = $this->app['eccube.repository.class_category']->findAll();
        foreach ($ClassCategories as $ClassCategory) {
            $this->app['orm.em']->remove($ClassCategory);
        }
        $this->app['orm.em']->flush();
        $All = $this->app['eccube.repository.class_name']->findAll();
        foreach ($All as $ClassName) {
            $this->app['orm.em']->remove($ClassName);
        }
        $this->app['orm.em']->flush();
    }

    public function testGetList()
    {
        $ClassCategories = $this->app['eccube.repository.class_category']->getList();

        $this->expected = 9;
        $this->actual = count($ClassCategories);
        $this->verify('合計数は'.$this->expected.'ではありません');

        $this->actual = array();
        foreach ($ClassCategories as $ClassCategory) {
            $this->actual[] = $ClassCategory->getRank();
        }
        $this->expected = array(2, 2, 2, 1, 1, 1, 0, 0, 0);
        $this->verify('ソート順が違います');
    }

    public function testGetListWithParams()
    {
        $ClassName = $this->app['eccube.repository.class_name']->findOneBy(
            array('name' => 'class-1')
        );

        $ClassCategories = $this->app['eccube.repository.class_category']->getList($ClassName);

        $this->expected = 3;
        $this->actual = count($ClassCategories);
        $this->verify('合計数は'.$this->expected.'ではありません');

        $this->actual = array();
        foreach ($ClassCategories as $ClassCategory) {
            $this->actual[] = $ClassCategory->getName();
        }
        $this->expected = array('classcategory-1-2', 'classcategory-1-1', 'classcategory-1-0');
        $this->verify('ソート順が違います');
    }

    public function testSave()
    {
        $faker = $this->getFaker();
        $ClassName = $this->app['eccube.repository.class_name']->findOneBy(
            array('name' => 'class-1')
        );

        $ClassCategory = new ClassCategory();
        $ClassCategory
            ->setName($faker->name)
            ->setClassName($ClassName);

        $this->app['eccube.repository.class_category']->save($ClassCategory);

        $this->expected = 3;
        $this->actual = $ClassCategory->getRank();
        $this->verify('rank は'.$this->expected.'ではありません');
    }

    public function testSaveWithRankNull()
    {
        $this->removeClass();    // 一旦全件削除
        $ClassName = new ClassName();
        $ClassName
            ->setName('class-3');
        $this->app['eccube.repository.class_name']->save($ClassName);

        $faker = $this->getFaker();

        $ClassCategory = new ClassCategory();
        $ClassCategory
            ->setName($faker->name)
            ->setClassName($ClassName);

        $this->app['eccube.repository.class_category']->save($ClassCategory);

        $this->expected = 1;
        $this->actual = $ClassCategory->getRank();
        $this->verify('rank は'.$this->expected.'ではありません');
    }

    public function testDelete()
    {
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(
            array('name' => 'classcategory-1-0')
        );
        $ClassCategoryId = $ClassCategory->getId();
        $this->app['eccube.repository.class_category']->delete($ClassCategory);

        self::assertNull($this->app['orm.em']->find(ClassCategory::class, $ClassCategoryId));
    }

    public function testDeleteWithException()
    {
        $Product = $this->createProduct();
        /** @var ProductClass[] $ProductClassess */
        $ProductClassess = $Product->getProductClasses();
        foreach ($ProductClassess as $ProductClass) {
            $ClassCategory1 = $ProductClass->getClassCategory1();
            if ($ClassCategory1 === null) {
                continue;
            }
            try {
                // 外部キー制約違反のため例外が発生するはず.
                $this->app['eccube.repository.class_category']->delete($ClassCategory1);
                $this->fail();
            } catch (\Exception $e) {

            }
        }
    }

    public function testToggleVisibilityToHidden()
    {
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(
            array('name' => 'classcategory-1-0')
        );
        $ClassCategoryId = $ClassCategory->getId();
        $this->app['eccube.repository.class_category']->toggleVisibility($ClassCategory);

        $actual = $this->app['orm.em']->find(ClassCategory::class, $ClassCategoryId);
        self::assertFalse($actual->isVisible());
    }

    public function testToggleVisibilityToVisible()
    {
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(
            array('name' => 'classcategory-1-0')
        );
        $ClassCategory->setVisible(false);
        $this->app['orm.em']->flush($ClassCategory);
        $ClassCategoryId = $ClassCategory->getId();

        $this->app['eccube.repository.class_category']->toggleVisibility($ClassCategory);

        $actual = $this->app['orm.em']->find(ClassCategory::class, $ClassCategoryId);
        self::assertTrue($actual->isVisible());
    }
}
