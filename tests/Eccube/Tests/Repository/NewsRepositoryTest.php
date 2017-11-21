<?php

namespace Eccube\Tests\Repository;

use Eccube\Entity\News;
use Eccube\Tests\EccubeTestCase;


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

        $faker = $this->getFaker();
        for ($i = 0; $i < 3; $i++) {
            $News = new News();
            $News
                ->setTitle('news-'.$i)
                ->setDescription($faker->realText())
                ->setUrl($faker->url)
                ->setLinkMethod(1)
                ->setSortNo($i)
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
        $this->assertEquals(1, $News->getSortNo());

        // rank up 1 => 2
        $this->app['eccube.repository.news']->up($News);

        $this->expected = 2;
        $this->actual = $News->getSortNo();
        $this->verify('sort_no は '.$this->expected.'ではありません');
    }

    public function testUpWithException()
    {
        $News = $this->app['eccube.repository.news']->findOneBy(
            array('title' => 'news-2')
        );

        try {
            $this->app['eccube.repository.news']->up($News);
            $this->fail();
        } catch (\Exception $e) {

        }
    }

    public function testDown()
    {
        $News = $this->app['eccube.repository.news']->findOneBy(
            array('title' => 'news-1')
        );
        $this->assertNotNull($News);
        $this->assertEquals(1, $News->getSortNo());

        // rank down 1 => 0
        $this->app['eccube.repository.news']->down($News);

        $this->expected = 0;
        $this->actual = $News->getSortNo();
        $this->verify('sort_no は '.$this->expected.'ではありません');
    }

    public function testDownWithException()
    {
        $News = $this->app['eccube.repository.news']->findOneBy(
            array('title' => 'news-0')
        );

        try {
            $this->app['eccube.repository.news']->down($News);
        } catch (\Exception $e) {

        }
    }

    public function testSave()
    {
        $faker = $this->getFaker();
        $News = new News();
        $News
            ->setTitle('news-10')
            ->setDescription($faker->realText())
            ->setUrl($faker->url)
            ->setLinkMethod(1);

        $this->app['eccube.repository.news']->save($News);

        $this->expected = 3;
        $this->actual = $News->getSortNo();
        $this->verify('sort_no は'.$this->expected.'ではありません');
    }

    public function testSaveWithRankNull()
    {
        $this->removeNews();    // 一旦全件削除
        $faker = $this->getFaker();
        $News = new News();
        $News
            ->setTitle('news-10')
            ->setDescription($faker->realText())
            ->setUrl($faker->url)
            ->setLinkMethod(1);

        $this->app['eccube.repository.news']->save($News);

        $this->expected = 1;
        $this->actual = $News->getSortNo();
        $this->verify('sort_no は'.$this->expected.'ではありません');
    }

    public function testDelete()
    {
        $News = $this->app['eccube.repository.news']->findOneBy(
            array('title' => 'news-0')
        );

        $newsId = $News->getId();
        $this->app['eccube.repository.news']->delete($News);

        self::assertNull($this->app['eccube.repository.news']->find($newsId));
    }
}
