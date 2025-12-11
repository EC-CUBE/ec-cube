<?php

declare(strict_types=1);

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

use Eccube\Entity\News;
use Eccube\Repository\NewsRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * NewsRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
final class NewsRepositoryTest extends EccubeTestCase
{
    protected ?NewsRepository $newsRepo = null;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->newsRepo = $this->entityManager->getRepository(News::class);
        $this->removeNews();

        $faker = $this->getFaker();
        for ($i = 0; $i < 3; $i++) {
            $News = new News();
            $News
                ->setTitle('news-'.$i)
                ->setDescription($faker->realText())
                ->setUrl($faker->url)
                ->setLinkMethod(true)
                ->setVisible(true)
                ->setPublishDate(new \DateTime());
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

    public function testSave()
    {
        $dateTime = new \DateTime();
        $faker = $this->getFaker();
        $url = $faker->url;
        $title = $faker->text();
        $News = new News();
        $News
            ->setPublishDate($dateTime)
            ->setTitle($title)
            ->setDescription($faker->realText())
            ->setUrl($url)
            ->setVisible(true)
            ->setLinkMethod(true);

        $this->newsRepo->save($News);

        // verify
        /** @var News $new */
        $new = $this->newsRepo->findOneBy(['title' => $title, 'url' => $url]);
        $this->actual = $new->getPublishDate();
        $this->expected = $dateTime;
        $this->verify();
    }

    public function testDelete()
    {
        $News = $this->newsRepo->findOneBy(
            ['title' => 'news-0']
        );
        $this->assertInstanceOf(News::class, $News);

        $newsId = $News->getId();
        $this->newsRepo->delete($News);

        $this->assertNotInstanceOf(News::class, $this->newsRepo->find($newsId));
    }

    public function testGetList()
    {
        $arrNews = $this->newsRepo->getList();
        $this->actual = count($arrNews);
        $this->expected = 3;

        $this->verify();
    }
}
