<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
        $this->client->request('GET', $this->app->url('admin_content_news'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentNewsNew()
    {
        $this->client->request('GET', $this->app->url('admin_content_news_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentNewsEdit()
    {
        // before
        $TestCreator = $this->findMember(2);
        $TestNews = $this->newTestNews($TestCreator);
        $this->insertTestNews($TestNews);

        $test_news_id = $this->getTestNewsId($TestNews);

        // main
        $this->client->request('GET',
            $this->app->url( 'admin_content_news_edit', array('id' => $test_news_id))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // after
        $this->deleteTestNews($TestNews);
    }


    public function testRoutingAdminContentNewsDelete()
    {
        // before
        $TestCreator = $this->findMember(2);
        $TestNews = $this->newTestNews($TestCreator);
        $this->insertTestNews($TestNews);

        $test_news_id = $this->getTestNewsId($TestNews);

        // main
        $redirectUrl = $this->app->url('admin_content_news');
        $this->client->request('DELETE',
            $this->app->url('admin_content_news_delete', array('id' => $test_news_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->deleteTestNews($TestNews);
    }

    public function testRoutingAdminContentNewsUp()
    {
        // before
        $TestCreator = $this->findMember(2);
        $TestNews1 = $this->newTestNews($TestCreator, 1);
        $TestNews2 = $this->newTestNews($TestCreator, 2);
        $this->insertTestNews($TestNews1);
        $this->insertTestNews($TestNews2);

        $test_news_id = $this->getTestNewsId($TestNews1);

        // main
        $redirectUrl = $this->app->url('admin_content_news');
        $this->client->request('PUT',
            $this->app->url('admin_content_news_up', array('id' => $test_news_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->deleteTestNews($TestNews1);
        $this->deleteTestNews($TestNews2);
    }

    public function testRoutingAdminContentNewsDown()
    {
        // before
        $TestCreator = $this->findMember(2);
        $TestNews1 = $this->newTestNews($TestCreator, 1);
        $TestNews2 = $this->newTestNews($TestCreator, 2);
        $this->insertTestNews($TestNews1);
        $this->insertTestNews($TestNews2);

        $test_news_id = $this->getTestNewsId($TestNews2);

        // main
        $redirectUrl = $this->app->url('admin_content_news');
        $this->client->request('PUT',
            $this->app->url('admin_content_news_down', array('id' => $test_news_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->deleteTestNews($TestNews1);
        $this->deleteTestNews($TestNews2);
    }

    private function newTestNews($TestCreator, $rank = 1)
    {
        $TestNews = new \Eccube\Entity\News();
        $TestNews
            ->setDate(new \DateTime())
            ->setTitle('テストタイトル' . $rank)
            ->setComment('テスト内容' . $rank)
            ->setUrl('http://example.com/')
            ->setRank(100 + $rank)
            ->setSelect(0)
            ->setLinkMethod(0)
            ->setDelFlg(0)
            ->setCreator($TestCreator);

        return $TestNews;
    }

    private function findMember($id)
    {
        return $this->createMember();
    }

    private function insertTestNews($TestNews)
    {
        $this->app['orm.em']->persist($TestNews);
        $this->app['orm.em']->flush();
    }

    private function deleteTestNews($TestNews)
    {
        $this->app['orm.em']->remove($TestNews);
        $this->app['orm.em']->flush();
    }

    private function getTestNewsId($TestNews)
    {
        $test_news_id = $this->app['eccube.repository.news']
            ->findOneBy(array(
                'title' => $TestNews->getTitle()
            ))
            ->getId();

        return $test_news_id;
    }
}
