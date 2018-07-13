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

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Entity\News;
use Eccube\Repository\NewsRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class NewsControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->newsRepository = $this->container->get(NewsRepository::class);
    }

    public function testRoutingAdminContentNews()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_news'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentNewsNew()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_news_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentNewsEdit()
    {
        $Member = $this->createMember();
        $News = $this->createNews($Member);

        $this->client->request('GET',
            $this->generateUrl('admin_content_news_edit', ['id' => $News->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentNewsDelete()
    {
        $Member = $this->createMember();
        $News = $this->createNews($Member);

        $this->loginTo($Member);

        $redirectUrl = $this->generateUrl('admin_content_news');

        $this->client->request('DELETE',
            $this->generateUrl('admin_content_news_delete', ['id' => $News->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testMoveSortNo()
    {
        /** @var News[] $News */
        $News = $this->newsRepository->findBy([], ['sort_no' => 'DESC']);

        $this->expected = [];
        foreach ($News as $New) {
            $this->expected[$New->getId()] = $New->getSortNo();
        }

        // swap sort_no
        reset($this->expected);
        $firstKey = key($this->expected);
        end($this->expected);
        $lastKey = key($this->expected);

        $tmp = $this->expected[$firstKey];
        $this->expected[$firstKey] = $this->expected[$lastKey];
        $this->expected[$lastKey] = $tmp;

        $this->client->request('POST',
            $this->generateUrl('admin_content_news_sort_no_move'),
            $this->expected,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $News = $this->newsRepository->findBy([], ['sort_no' => 'DESC']);
        $this->actual = [];
        foreach ($News as $New) {
            $this->actual[$New->getId()] = $New->getSortNo();
        }
        sort($this->expected);
        sort($this->actual);

        $this->verify();
    }

    private function createNews($TestCreator, $sortNo = 1)
    {
        $TestNews = new \Eccube\Entity\News();
        $TestNews
            ->setPublishDate(new \DateTime())
            ->setTitle('テストタイトル'.$sortNo)
            ->setDescription('テスト内容'.$sortNo)
            ->setUrl('http://example.com/')
            ->setSortNo(100 + $sortNo)
            ->setLinkMethod(false)
            ->setCreator($TestCreator);

        $this->entityManager->persist($TestNews);
        $this->entityManager->flush($TestNews);

        return $TestNews;
    }
}
