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

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class NewsControllerTest extends AbstractAdminWebTestCase
{
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

    public function testRoutingAdminContentNewsUp()
    {
        $Member = $this->createMember();
        $News1 = $this->createNews($Member, 1);
        $News2 = $this->createNews($Member, 2);

        $redirectUrl = $this->generateUrl('admin_content_news');
        $this->client->request('PUT',
            $this->generateUrl('admin_content_news_up', ['id' => $News1->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testRoutingAdminContentNewsDown()
    {
        $Member = $this->createMember();
        $News1 = $this->createNews($Member, 1);
        $News2 = $this->createNews($Member, 2);

        $redirectUrl = $this->generateUrl('admin_content_news');
        $this->client->request('PUT',
            $this->generateUrl('admin_content_news_down', ['id' => $News2->getId()])
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
            ->setSortNo(100 + $sortNo)
            ->setLinkMethod(false)
            ->setCreator($TestCreator);

        $this->entityManager->persist($TestNews);
        $this->entityManager->flush($TestNews);

        return $TestNews;
    }
}
