<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Repository;

use Eccube\Entity\News;
use Eccube\Repository\NewsRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * NewsRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class NewsRepositoryTest extends EccubeTestCase
{
    protected $Member;

    /** @var NewsRepository */
    protected $newsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->newsRepo = $this->container->get(NewsRepository::class);
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
            $this->entityManager->persist($News);
        }
        $this->entityManager->flush();
    }

    protected function removeNews()
    {
        $All = $this->newsRepo->findAll();
        foreach ($All as $News) {
            $this->entityManager->remove($News);
        }
        $this->entityManager->flush();
    }

    public function testUp()
    {
        /** @var News $News */
        $News = $this->newsRepo->findOneBy(
            ['title' => 'news-1']
        );
        $this->assertNotNull($News);
        $this->assertEquals(1, $News->getSortNo());

        // sortNo up 1 => 2
        $this->newsRepo->up($News);

        $this->expected = 2;
        $this->actual = $News->getSortNo();
        $this->verify('sort_no は '.$this->expected.'ではありません');
    }

    public function testUpWithException()
    {
        $this->expectException(\Exception::class);
        /** @var News $News */
        $News = $this->newsRepo->findOneBy(
            ['title' => 'news-2']
        );

        $this->newsRepo->up($News);
        $this->fail();
    }

    public function testDown()
    {
        /** @var News $News */
        $News = $this->newsRepo->findOneBy(
            ['title' => 'news-1']
        );
        $this->assertNotNull($News);
        $this->assertEquals(1, $News->getSortNo());

        // sortNo down 1 => 0
        $this->newsRepo->down($News);

        $this->expected = 0;
        $this->actual = $News->getSortNo();
        $this->verify('sort_no は '.$this->expected.'ではありません');
    }

    public function testDownWithException()
    {
        $this->expectException(\Exception::class);
        $News = $this->newsRepo->findOneBy(
            ['title' => 'news-0']
        );

        $this->newsRepo->down($News);
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

        $this->newsRepo->save($News);

        $this->expected = 3;
        $this->actual = $News->getSortNo();
        $this->verify('sort_no は'.$this->expected.'ではありません');
    }

    public function testSaveWithSortNoNull()
    {
        $this->removeNews();    // 一旦全件削除
        $faker = $this->getFaker();
        $News = new News();
        $News
            ->setTitle('news-10')
            ->setDescription($faker->realText())
            ->setUrl($faker->url)
            ->setLinkMethod(1);

        $this->newsRepo->save($News);

        $this->expected = 1;
        $this->actual = $News->getSortNo();
        $this->verify('sort_no は'.$this->expected.'ではありません');
    }

    public function testDelete()
    {
        $News = $this->newsRepo->findOneBy(
            ['title' => 'news-0']
        );

        $newsId = $News->getId();
        $this->newsRepo->delete($News);

        self::assertNull($this->newsRepo->find($newsId));
    }
}
