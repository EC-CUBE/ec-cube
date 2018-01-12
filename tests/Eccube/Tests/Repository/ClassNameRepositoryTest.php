<?php

namespace Eccube\Tests\Repository;

use Eccube\Entity\ClassCategory;
use Eccube\Entity\ClassName;
use Eccube\Tests\EccubeTestCase;


/**
 * ClassNameRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ClassNameRepositoryTest extends EccubeTestCase
{
    protected $Member;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
        $this->removeClass();
        $this->Member = $this->app['eccube.repository.member']->find(2);

        for ($i = 0; $i < 3; $i++) {
            $ClassName = new ClassName();
            $ClassName
                ->setName('class-'.$i)
                ->setCreator($this->Member)
                ->setSortNo($i)
                ;
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
        $ClassNames = $this->app['eccube.repository.class_name']->getList();

        $this->expected = 3;
        $this->actual = count($ClassNames);
        $this->verify('合計数は'.$this->expected.'ではありません');

        $this->actual = array();
        foreach ($ClassNames as $ClassName) {
            $this->actual[] = $ClassName->getSortNo();
        }
        $this->expected = array(2, 1, 0);
        $this->verify('ソート順が違います');
    }

    public function testSave()
    {
        $faker = $this->getFaker();
        $ClassName = new ClassName();
        $ClassName
            ->setName($faker->name)
            ->setCreator($this->Member);

        $this->app['eccube.repository.class_name']->save($ClassName);

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
            ->setCreator($this->Member);

        $this->app['eccube.repository.class_name']->save($ClassName);

        $this->expected = 1;
        $this->actual = $ClassName->getSortNo();
        $this->verify('sort_no は'.$this->expected.'ではありません');
    }

    public function testDelete()
    {
        $ClassName = $this->app['eccube.repository.class_name']->findOneBy(
            array('name' => 'class-0')
        );
        $ClassNameId = $ClassName->getId();
        $this->app['eccube.repository.class_name']->delete($ClassName);

        self::assertNull($this->app['orm.em']->find(ClassName::class, $ClassNameId));
    }

    public function testDeleteWithException()
    {
        $ClassName = new ClassName();
        $ClassName->setName('sample');
        $ClassName->setSortNo(100);
        $ClassCateogory = new ClassCategory();
        $ClassCateogory->setClassName($ClassName);
        $ClassCateogory->setName('sample');
        $ClassCateogory->setSortNo(100);
        $ClassCateogory->setVisible(true);

        $em = $this->app['orm.em'];
        $em->persist($ClassName);
        $em->persist($ClassCateogory);
        $em->flush([$ClassName, $ClassCateogory]);

        try {
            $this->app['eccube.repository.class_name']->delete($ClassName);
            $this->fail();
        } catch (\Exception $e) {
            
        }
    }
}
