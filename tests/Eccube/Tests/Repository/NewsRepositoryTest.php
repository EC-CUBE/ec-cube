<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\News;


/**
 * NewsRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class NewsRepositoryTest extends EccubeTestCase
{
    protected $Member;

    public function setUp()
    {
        parent::setUp();
        $this->removeNews();
        $this->Member = $this->app['eccube.repository.member']->find(2);

        $faker = $this->getFaker();
        for ($i = 0; $i < 3; $i++) {
            $News = new News();
            $News
                ->setTitle('news-'.$i)
                ->setComment($faker->text())
                ->setUrl($faker->url)
                ->setCreator($this->Member)
                ->setSelect(1)
                ->setLinkMethod(1)
                ->setDelFlg(0)
                ->setRank($i)
                ;
            $this->app['orm.em']->persist($News);
        }
        $this->app['orm.em']->flush();
    }

    public function removeNews()
    {
        $All = $this->app['eccube.repository.news']->findAll();
        foreach ($All as $News) {
            $this->app['orm.em']->remove($News);
        }
        $this->app['orm.em']->flush();
    }

    public function testUp()
    {
        $News = $this->app['eccube.repository.news']->findOneBy(
            array('title' => 'news-1')
        );
        $this->assertNotNull($News);
        $this->assertEquals(1, $News->getRank());

        // rank up 1 => 2
        $result = $this->app['eccube.repository.news']->up($News);

        $this->assertTrue($result);
        $this->expected = 2;
        $this->actual = $News->getRank();
        $this->verify('rank は '.$this->expected.'ではありません');
    }

    public function testUpWithException()
    {
        $News = $this->app['eccube.repository.news']->findOneBy(
            array('title' => 'news-2')
        );

        $result = $this->app['eccube.repository.news']->up($News);

        $this->assertFalse($result);
    }

    public function testDown()
    {
        $News = $this->app['eccube.repository.news']->findOneBy(
            array('title' => 'news-1')
        );
        $this->assertNotNull($News);
        $this->assertEquals(1, $News->getRank());

        // rank down 1 => 0
        $result = $this->app['eccube.repository.news']->down($News);

        $this->assertTrue($result);
        $this->expected = 0;
        $this->actual = $News->getRank();
        $this->verify('rank は '.$this->expected.'ではありません');
    }

    public function testDownWithException()
    {
        $News = $this->app['eccube.repository.news']->findOneBy(
            array('title' => 'news-0')
        );

        $result = $this->app['eccube.repository.news']->down($News);

        $this->assertFalse($result);
    }

    public function testSave()
    {
        $faker = $this->getFaker();
        $News = new News();
        $News
            ->setTitle('news-10')
            ->setComment($faker->text())
            ->setUrl($faker->url)
            ->setCreator($this->Member)
            ->setSelect(1)
            ->setLinkMethod(1);

        $result = $this->app['eccube.repository.news']->save($News);
        $this->assertTrue($result);

        $this->expected = 3;
        $this->actual = $News->getRank();
        $this->verify('rank は'.$this->expected.'ではありません');
    }

    public function testSaveWithRankNull()
    {
        $this->removeNews();    // 一旦全件削除
        $faker = $this->getFaker();
        $News = new News();
        $News
            ->setTitle('news-10')
            ->setComment($faker->text())
            ->setUrl($faker->url)
            ->setCreator($this->Member)
            ->setSelect(1)
            ->setLinkMethod(1);

        $result = $this->app['eccube.repository.news']->save($News);
        $this->assertTrue($result);

        $this->expected = 1;
        $this->actual = $News->getRank();
        $this->verify('rank は'.$this->expected.'ではありません');
    }

    public function testSaveWithException()
    {
        $faker = $this->getFaker();
        $News = new News();
        $News
            ->setTitle('news-10')
            ->setComment($faker->text())
            ->setUrl($faker->url)
            ->setCreator($this->Member)
            ->setSelect(null)   // select は not null なので例外になる
            ->setLinkMethod(1);

        $result = $this->app['eccube.repository.news']->save($News);
        $this->assertFalse($result);
    }


    public function testDelete()
    {
        $News = $this->app['eccube.repository.news']->findOneBy(
            array('title' => 'news-0')
        );

        $updateDate = $News->getUpdateDate();
        sleep(1);
        $result = $this->app['eccube.repository.news']->delete($News);

        $this->assertTrue($result);
        $this->assertEquals(Constant::ENABLED, $News->getDelFlg());
        $this->assertTrue(0 === $News->getRank());

        $this->expected = $updateDate;
        $this->actual = $News->getUpdateDate();
        $this->assertNotEquals($this->expected, $this->actual);
    }

    public function testDeleteWithException()
    {
        $News = new News();     // 存在しないので例外が発生する
        $result = $this->app['eccube.repository.news']->delete($News);

        $this->assertFalse($result, '削除に失敗するはず');
    }
}
