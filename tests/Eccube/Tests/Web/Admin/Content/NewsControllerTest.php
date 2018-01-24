<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
            $this->generateUrl('admin_content_news_edit', array('id' => $News->getId()))
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
            $this->generateUrl('admin_content_news_delete', array('id' => $News->getId()))
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
            $this->generateUrl('admin_content_news_up', array('id' => $News1->getId()))
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
            $this->generateUrl('admin_content_news_down', array('id' => $News2->getId()))
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
