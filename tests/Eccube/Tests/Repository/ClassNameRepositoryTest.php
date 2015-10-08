<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\ClassName;


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
        parent::setUp();
        $this->removeClass();
        $this->Member = $this->app['eccube.repository.member']->find(2);

        for ($i = 0; $i < 3; $i++) {
            $ClassName = new ClassName();
            $ClassName
                ->setName('class-'.$i)
                ->setCreator($this->Member)
                ->setDelFlg(0)
                ->setRank($i)
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
            $this->actual[] = $ClassName->getRank();
        }
        $this->expected = array(2, 1, 0);
        $this->verify('ソート順が違います');
    }

    public function testUp()
    {
        $ClassName = $this->app['eccube.repository.class_name']->findOneBy(
            array('name' => 'class-1')
        );
        $this->assertNotNull($ClassName);
        $this->assertEquals(1, $ClassName->getRank());

        // rank up 1 => 2
        $result = $this->app['eccube.repository.class_name']->up($ClassName);

        $this->assertTrue($result);
        $this->expected = 2;
        $this->actual = $ClassName->getRank();
        $this->verify('rank は '.$this->expected.'ではありません');
    }

    public function testUpWithException()
    {
        $ClassName = $this->app['eccube.repository.class_name']->findOneBy(
            array('name' => 'class-2')
        );

        $result = $this->app['eccube.repository.class_name']->up($ClassName);

        $this->assertFalse($result);
    }

    public function testDown()
    {
        $ClassName = $this->app['eccube.repository.class_name']->findOneBy(
            array('name' => 'class-1')
        );
        $this->assertNotNull($ClassName);
        $this->assertEquals(1, $ClassName->getRank());

        // rank down 1 => 0
        $result = $this->app['eccube.repository.class_name']->down($ClassName);

        $this->assertTrue($result);
        $this->expected = 0;
        $this->actual = $ClassName->getRank();
        $this->verify('rank は '.$this->expected.'ではありません');
    }

    public function testDownWithException()
    {
        $ClassName = $this->app['eccube.repository.class_name']->findOneBy(
            array('name' => 'class-0')
        );

        $result = $this->app['eccube.repository.class_name']->down($ClassName);

        $this->assertFalse($result);
    }

    public function testSave()
    {
        $faker = $this->getFaker();
        $ClassName = new ClassName();
        $ClassName
            ->setName($faker->name)
            ->setCreator($this->Member);

        $result = $this->app['eccube.repository.class_name']->save($ClassName);
        $this->assertTrue($result);

        $this->expected = 3;
        $this->actual = $ClassName->getRank();
        $this->verify('rank は'.$this->expected.'ではありません');
    }

    public function testSaveWithRankNull()
    {
        $this->removeClass();    // 一旦全件削除
        $faker = $this->getFaker();
        $ClassName = new ClassName();
        $ClassName
            ->setName($faker->name)
            ->setCreator($this->Member);

        $result = $this->app['eccube.repository.class_name']->save($ClassName);
        $this->assertTrue($result);

        $this->expected = 1;
        $this->actual = $ClassName->getRank();
        $this->verify('rank は'.$this->expected.'ではありません');
    }

    public function testSaveWithException()
    {
        $ClassName = new ClassName(); // 空の要素なので例外となる

        // TODO name, rank のテストをする
        // https://github.com/EC-CUBE/ec-cube/issues/913
        $result = $this->app['eccube.repository.class_name']->save($ClassName);
        $this->assertFalse($result);
    }

    public function testDelete()
    {
        $ClassName = $this->app['eccube.repository.class_name']->findOneBy(
            array('name' => 'class-0')
        );

        $updateDate = $ClassName->getUpdateDate();
        sleep(1);
        $result = $this->app['eccube.repository.class_name']->delete($ClassName);

        $this->assertTrue($result);
        $this->assertEquals(Constant::ENABLED, $ClassName->getDelFlg());
        $this->assertTrue(0 === $ClassName->getRank());

        $this->expected = $updateDate;
        $this->actual = $ClassName->getUpdateDate();
        $this->assertNotEquals($this->expected, $this->actual);
    }

    public function testDeleteWithException()
    {
        $ClassName = new ClassName();     // 存在しないので例外が発生する
        $result = $this->app['eccube.repository.class_name']->delete($ClassName);

        $this->assertFalse($result, '削除に失敗するはず');
    }
}
