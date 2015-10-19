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
//         self::markTestSkipped();

        $this->client->request('GET', $this->app->url('admin_content_news'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentNewsNew()
    {
//         self::markTestSkipped();

        $this->client->request('GET', $this->app->url('admin_content_news_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentNewsEdit()
    {
//         self::markTestSkipped();

        $this->client->request('GET',
            $this->app->url(
                'admin_content_news_edit',
                array('id' => 1)
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }


    public function testRoutingAdminContentNewsDelete()
    {
//         self::markTestSkipped();

        $redirectUrl = $this->app->url('admin_content_news');

        $this->client->request('DELETE',
            $this->app->url('admin_content_news_delete', array('id' => 1))
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }

    public function testRoutingAdminContentNewsUp()
    {
//         self::markTestSkipped();

        // before
        $TestCreator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestNews = $this->newTestNews($TestCreator);
        $this->app['orm.em']->persist($TestNews);
        $this->app['orm.em']->flush();

        $test_news_id = $this->app['eccube.repository.news']
            ->findOneBy(array(
                'title' => $TestNews->getTitle()
            ))
            ->getId();

        // main
        $redirectUrl = $this->app->url('admin_content_news');
        $this->client->request('PUT',
            $this->app->url('admin_content_news_up', array('id' => $test_news_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->app['orm.em']->remove($TestNews);
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminContentNewsDown()
    {
//         self::markTestSkipped();

        // before
        $TestCreator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestNews = $this->newTestNews($TestCreator);
        $this->app['orm.em']->persist($TestNews);
        $this->app['orm.em']->flush();

        $test_news_id = $this->app['eccube.repository.news']
            ->findOneBy(array(
                'title' => $TestNews->getTitle()
            ))
            ->getId();

        // main
        $redirectUrl = $this->app->url('admin_content_news');
        $this->client->request('PUT',
            $this->app->url('admin_content_news_down', array('id' => $test_news_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->app['orm.em']->remove($TestNews);
        $this->app['orm.em']->flush();
    }

    private function newTestNews($TestCreator)
    {
        $TestNews = new \Eccube\Entity\News();
        $TestNews
            ->setDate(new \DateTime())
            ->setTitle('テストタイトル')
            ->setComment('テスト内容')
            ->setUrl('http://example.com/')
            ->setRank(100)
            ->setSelect(0)
            ->setLinkMethod(0)
            ->setDelFlg(false)
            ->setCreator($TestCreator);

        return $TestNews;
    }
}
