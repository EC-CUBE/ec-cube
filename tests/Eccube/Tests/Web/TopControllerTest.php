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


namespace Eccube\Tests\Web;
use Symfony\Component\DomCrawler\Crawler;

class TopControllerTest extends AbstractWebTestCase
{

    public function testRoutingIndex()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('homepage'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * testTopContent
     */
    public function testTopContent()
    {
        $client = $this->client;
        $client->restart();
        $crawler = $client->request('GET', $this->app['url_generator']->generate('homepage'));
        dump($this->client->getResponse()->getContent());
        $html = $crawler->html();
        //test product list
        $this->assertContains('商品一覧へ', $html);

        //test dummy link
        $href = $crawler->filter('.img_right a')->attr('href');
        $this->expected = '#';
        $this->actual = $href;
        $this->verify();

        //test delivery free display
        /* @var $BaseInfo \Eccube\Entity\BaseInfo */
        $BaseInfo = $this->app['eccube.repository.base_info']->find(1);
        $BaseInfo->setDeliveryFreeAmount(100);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush($BaseInfo);
        $crawler = $client->request('GET', $this->app['url_generator']->generate('homepage'));
        $html = $crawler->html();
        $this->assertContains('100円以上の購入', $html);

        //if null set 0円
        $BaseInfo->setDeliveryFreeAmount(0);
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush($BaseInfo);
        $crawler = $client->request('GET', $this->app['url_generator']->generate('homepage'));
        $html = $crawler->html();
        $this->assertContains('0円以上の購入', $html);
    }
}
