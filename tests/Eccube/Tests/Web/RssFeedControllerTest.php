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

namespace Eccube\Tests\Web;

class RssFeedControllerTest extends AbstractWebTestCase
{
    /**
     * 商品RSSフィードのテスト
     */
    public function testRoutingRssFeedForProducts()
    {
        $client = $this->client;
        $client->request('GET', $this->generateUrl('rss_feed_for_products'));
        $content = simplexml_load_string($client->getResponse()->getContent());

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(2, $content->channel->item);
        $this->assertEquals('彩のジェラートCUBE', $content->channel->item[1]->title);
    }

    /**
     * 新着情報RSSフィードのテスト
     */
    public function testRoutingRssFeedForNews()
    {
        $client = $this->client;
        $client->request('GET', $this->generateUrl('rss_feed_for_news'));
        $content = simplexml_load_string($client->getResponse()->getContent());

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $content->channel->item);
        $this->assertEquals('サイトオープンいたしました!', $content->channel->item[0]->title);
    }
}
