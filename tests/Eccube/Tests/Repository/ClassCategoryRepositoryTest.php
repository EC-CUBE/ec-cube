<?php

namespace Eccube\Tests\Repository;

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
    protected $Member;

    public function setUp()
    {
        parent::setUp();
        $this->removeClass();
        $this->Member = $this->app['eccube.repository.member']->find(2);

        for ($i = 0; $i < 3; $i++) {
            $ClassName = new ClassName();
            $ClassName
                ->setName('class-'.$i)
                ->setCreator($this->Member)
                ->setDelFlg(0)
                ->setRank($i);
            for ($j = 0; $j < 3; $j++) {
                $ClassCategory = new ClassCategory();
                $ClassCategory
                    ->setName('classcategory-'.$i.'-'.$j)
                    ->setCreator($this->Member)
                    ->setDelFlg(0)
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

    public function testUp()
    {
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(
            array('name' => 'classcategory-1-1')
        );
        $this->assertNotNull($ClassCategory);
        $this->assertEquals(1, $ClassCategory->getRank());

        // rank up 1 => 2
        $result = $this->app['eccube.repository.class_category']->up($ClassCategory);

        $this->assertTrue($result);
        $this->expected = 2;
        $this->actual = $ClassCategory->getRank();
        $this->verify('rank は '.$this->expected.'ではありません');
    }

    public function testUpWithException()
    {
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(
            array('name' => 'classcategory-1-2')
        );

        $result = $this->app['eccube.repository.class_category']->up($ClassCategory);

        $this->assertFalse($result);
    }

    public function testDown()
    {
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(
            array('name' => 'classcategory-1-1')
        );
        $this->assertNotNull($ClassCategory);
        $this->assertEquals(1, $ClassCategory->getRank());

        // rank down 1 => 0
        $result = $this->app['eccube.repository.class_category']->down($ClassCategory);

        $this->assertTrue($result);
        $this->expected = 0;
        $this->actual = $ClassCategory->getRank();
        $this->verify('rank は '.$this->expected.'ではありません');
    }

    public function testDownWithException()
    {
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(
            array('name' => 'classcategory-1-0')
        );

        $result = $this->app['eccube.repository.class_category']->down($ClassCategory);

        $this->assertFalse($result);
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
            ->setClassName($ClassName)
            ->setCreator($this->Member);

        $result = $this->app['eccube.repository.class_category']->save($ClassCategory);
        $this->assertTrue($result);

        $this->expected = 3;
        $this->actual = $ClassCategory->getRank();
        $this->verify('rank は'.$this->expected.'ではありません');
    }

    public function testSaveWithRankNull()
    {
        $this->removeClass();    // 一旦全件削除
        $ClassName = new ClassName();
        $ClassName
            ->setName('class-3')
            ->setCreator($this->Member);
        $result = $this->app['eccube.repository.class_name']->save($ClassName);
        $this->assertTrue($result);

        $faker = $this->getFaker();

        $ClassCategory = new ClassCategory();
        $ClassCategory
            ->setName($faker->name)
            ->setClassName($ClassName)
            ->setCreator($this->Member);

        $result = $this->app['eccube.repository.class_category']->save($ClassCategory);
        $this->assertTrue($result, '保存に成功したかどうか');

        $this->expected = 1;
        $this->actual = $ClassCategory->getRank();
        $this->verify('rank は'.$this->expected.'ではありません');
    }

    public function testSaveWithException()
    {
        $ClassCategory = new ClassCategory(); // 空の要素なので例外となる

        // TODO name, rank のテストをする
        // https://github.com/EC-CUBE/ec-cube/issues/913
        $result = $this->app['eccube.repository.class_category']->save($ClassCategory);
        $this->assertFalse($result, '保存に成功したかどうか');
    }

    public function testDelete()
    {
        $ClassCategory = $this->app['eccube.repository.class_category']->findOneBy(
            array('name' => 'classcategory-1-0')
        );
        $result = $this->app['eccube.repository.class_category']->delete($ClassCategory);

        $this->assertTrue($result);
        $this->assertEquals(Constant::ENABLED, $ClassCategory->getDelFlg());
        $this->assertTrue(0 === $ClassCategory->getRank());
    }

    public function testDeleteWithException()
    {
        $ClassCategory = new ClassCategory(); // 存在しないので例外となる

        $result = $this->app['eccube.repository.class_category']->delete($ClassCategory);
        $this->assertFalse($result, '削除に失敗するはず');

    }
}
