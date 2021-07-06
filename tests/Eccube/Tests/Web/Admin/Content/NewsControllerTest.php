<?php

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

namespace Eccube\Tests\Web\Admin\Content;

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

        $this->newsRepository = $this->entityManager->getRepository(\Eccube\Entity\News::class);
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

    private function createNews($TestCreator, $sortNo = 1)
    {
        $TestNews = new \Eccube\Entity\News();
        $TestNews
            ->setPublishDate(new \DateTime())
            ->setTitle('テストタイトル'.$sortNo)
            ->setDescription('テスト内容'.$sortNo)
            ->setUrl('http://example.com/')
            ->setLinkMethod(false)
            ->setVisible(true)
            ->setCreator($TestCreator);

        $this->entityManager->persist($TestNews);
        $this->entityManager->flush($TestNews);

        return $TestNews;
    }
}
