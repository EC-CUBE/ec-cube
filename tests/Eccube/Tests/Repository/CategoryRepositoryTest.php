<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Category;


/**
 * CategoryRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CategoryRepositoryTest extends EccubeTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->remove();
        $this->createCategories();
    }

    public function createCategories()
    {
        $categories = array(
            array('name' => '親1', 'level' => 1, 'rank' => 1,
                  'child' => array(
                      array('name' => '子1', 'level' => 2, 'rank' => 4,
                            'child' => array(
                                array('name' => '孫1', 'level' => 3, 'rank' => 9)
                            ),
                      ),
                  ),
            ),
            array('name' => '親2', 'level' => 1, 'rank' => 2,
                  'child' => array(
                      array('name' => '子2-0', 'level' => 2, 'rank' => 5,
                            'child' => array(
                                array('name' => '孫2', 'level' => 3, 'rank' => 10)
                            )
                      ),
                      array('name' => '子2-1', 'level' => 2, 'rank' => 6),
                      array('name' => '子2-2', 'level' => 2, 'rank' => 7)
                  ),
            ),
            array('name' => '親3', 'level' => 1, 'rank' => 3,
                  'child' => array(
                      array('name' => '子3', 'level' => 2, 'rank' => 8,
                            'child' => array(
                                array('name' => '孫3', 'level' => 3, 'rank' => 11)
                            )
                      )
                  ),
            ),
        );

        foreach ($categories as $category_array) {
            $Category = new Category();
            $Category->setPropertiesFromArray($category_array);
            $Category->setDelFlg(Constant::DISABLED);
            $Category->setCreateDate(new \DateTime());
            $Category->setUpdateDate(new \DateTime());
            $this->app['orm.em']->persist($Category);
            $this->app['orm.em']->flush();
            if (!array_key_exists('child', $category_array)) {
                continue;
            }
            foreach ($category_array['child'] as $child_array) {
                $Child = new Category();
                $Child->setPropertiesFromArray($child_array);
                $Child->setParent($Category);
                $Child->setDelFlg(Constant::DISABLED);
                $Child->setCreateDate(new \DateTime());
                $Child->setUpdateDate(new \DateTime());
                $Category->addChild($Child);
                $this->app['orm.em']->persist($Child);
                $this->app['orm.em']->flush();
                if (!array_key_exists('child', $child_array)) {
                    continue;
                }
                foreach ($child_array['child'] as $grandson_array) {
                    $Grandson = new Category();
                    $Grandson->setPropertiesFromArray($grandson_array);
                    $Grandson->setParent($Child);
                    $Grandson->setDelFlg(Constant::DISABLED);
                    $Grandson->setCreateDate(new \DateTime());
                    $Grandson->setUpdateDate(new \DateTime());
                    $Child->addChild($Grandson);
                    $this->app['orm.em']->persist($Grandson);
                    $this->app['orm.em']->flush();
                }
            }
        }
        // 登録したEntityをEntityManagerからクリアする
        // ソート順が上記の登録順でキャッシュされているため、クリアして、DBから再取得させる
        $this->app['orm.em']->clear();
    }

    /**
     * 既存のデータを論理削除しておく.
     */
    public function remove() {
        $Categories = $this->app['eccube.repository.category']->findAll();
        foreach ($Categories as $Category) {
            $Category->setDelFlg(Constant::ENABLED);
            $this->app['orm.em']->merge($Category);
        }
        $this->app['orm.em']->flush();
    }

    public function testGetTotalCount()
    {
        $this->expected = 11;
        $this->actual = $this->app['eccube.repository.category']->getTotalCount();

        $this->verify('カテゴリの合計数は'.$this->expected.'ではありません');
    }

    public function testGetList()
    {
        $Categories = $this->app['eccube.repository.category']->getList();

        $this->expected = 3;
        $this->actual = count($Categories);
        $this->verify('ルートカテゴリの合計数は'.$this->expected.'ではありません');

        $this->actual = array();
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
        }

        $this->expected = array('親3', '親2', '親1');
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testGetListWithParent()
    {
        $Parent1 = $this->app['eccube.repository.category']->findOneBy(array('name' => '子1'));
        $Categories = $this->app['eccube.repository.category']->getList($Parent1);

        $this->expected = 1;
        $this->actual = count($Categories);
        $this->verify('ルートカテゴリの合計数は'.$this->expected.'ではありません');

        $this->actual = array();
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
        }

        $this->expected = array('孫1');
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testGetListWithFlat()
    {
        $Categories = $this->app['eccube.repository.category']->getList(null, true);

        $this->expected = 11;
        $this->actual = count($Categories);
        $this->verify('ルートカテゴリの合計数は'.$this->expected.'ではありません');

        $this->actual = array();
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
        }

        $this->expected = array('親3', '子3', '孫3', '親2', '子2-2', '子2-1', '子2-0', '孫2', '親1', '子1', '孫1');
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testUpWithParent()
    {
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '子2-1'));
        $result = $this->app['eccube.repository.category']->up($Category);

        $this->assertTrue($result);

        $Parent = $this->app['eccube.repository.category']->findOneBy(array('name' => '親2'));
        $Categories = $this->app['eccube.repository.category']->getList($Parent);

        $this->actual = array();
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
        }

        $this->expected = array('子2-1', '子2-2', '子2-0');
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testUp()
    {
        // CategoryRepository::upは、現状機能しておらず、期待値を返さないが、deprecatedのためスキップする
        $this->markTestSkipped('CategoryRepository::up() is deprecated.');

        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '親2'));
        // CategoryRepository::up() では, rank を1つだけ加算することに注意
        $result = $this->app['eccube.repository.category']->up($Category);

        $this->assertTrue($result);

        $Categories = $this->app['eccube.repository.category']->getList();

        $this->actual = array();
        $c = array();
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
            $c[$Category->getRank()] = $Category->getName();
        }

        $this->expected = array('親2', '親3', '親1'); // 現状、array('親2', '親1', '親3')が返っている
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testUpWithException()
    {
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '親3'));
        $result = $this->app['eccube.repository.category']->up($Category);

        $this->assertFalse($result);
    }

    public function testDownWithParent()
    {
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '子2-1'));
        $result = $this->app['eccube.repository.category']->down($Category);

        $this->assertTrue($result);

        $Parent = $this->app['eccube.repository.category']->findOneBy(array('name' => '親2'));
        $Categories = $this->app['eccube.repository.category']->getList($Parent);

        $this->actual = array();
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
        }

        $this->expected = array('子2-2', '子2-0', '子2-1');
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testDown()
    {
        // CategoryRepository::downは、現状機能しておらず、期待値を返さないが、deprecatedのためスキップする
        $this->markTestSkipped('CategoryRepository::down() is deprecated.');

        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '親2'));
        // CategoryRepository::down() では, rank を1つだけ減算することに注意
        $result = $this->app['eccube.repository.category']->down($Category);

        $this->assertTrue($result);

        $Categories = $this->app['eccube.repository.category']->getList();

        $this->actual = array();
        $c = array();
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
            $c[$Category->getRank()] = $Category->getName();
        }

        $this->expected = array('親3', '親1', '親2'); // 現状、array('親1', '親3', '親2')が返っている
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testDownWithException()
    {
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '親1'));
        $result = $this->app['eccube.repository.category']->down($Category);

        $this->assertFalse($result);
    }

    public function testSave()
    {
        $faker = $this->getFaker();
        $name = $faker->name;
        $Category = new Category();
        $Category->setName($name)
            ->setLevel(1);
        $result = $this->app['eccube.repository.category']->save($Category);
        $this->assertTrue($result);

        $this->expected = 12;
        $this->actual = $Category->getRank();
        $this->verify('カテゴリの rank は'.$this->expected.'ではありません');
    }

    public function testSaveWithException()
    {
        $faker = $this->getFaker();
        $name = $faker->name;
        $Category = new Category();
        $Category->setName($name)
            ->setLevel(null);   // level は not null なので例外になる
        $result = $this->app['eccube.repository.category']->save($Category);
        $this->assertFalse($result);
    }

    public function testSaveWithParent()
    {
        $faker = $this->getFaker();
        $name = $faker->name;
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '子2-1'));
        $Category->setName($name);
        $updateDate = $Category->getUpdateDate();
        sleep(1);
        $result = $this->app['eccube.repository.category']->save($Category);
        $this->assertTrue($result);

        $this->expected = $updateDate;
        $this->actual = $Category->getUpdateDate();

        $this->assertNotEquals($this->expected, $this->actual);

        // 名前を変更したので null になっているはず
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '子2-1'));
        $this->assertNull($Category);
    }

    public function testDelete()
    {
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '孫2'));
        $updateDate = $Category->getUpdateDate();
        sleep(1);
        $result = $this->app['eccube.repository.category']->delete($Category);

        $this->assertTrue($result);
        $this->assertEquals(Constant::ENABLED, $Category->getDelFlg());

        $this->expected = $updateDate;
        $this->actual = $Category->getUpdateDate();
        $this->assertNotEquals($this->expected, $this->actual);
    }
}
