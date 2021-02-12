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

class TopControllerTest extends AbstractWebTestCase
{
    public function testRoutingIndex()
    {
        $this->client->request('GET', $this->generateUrl('homepage'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testCheckFavicon()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));
        $node = $crawler->filter('link[rel=icon]');
        $this->assertEquals('/html/user_data/assets/img/common/favicon.ico', $node->attr('href'));
    }

    public function test_GAスクリプト表示確認()
    {
        // ある時
        // データ更新
        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));
        $node = $crawler->filterXPath('//script[contains(@src, "googletagmanager")]');
        $this->assertEquals('https://www.googletagmanager.com/gtag/js?id=ほげほげ', $node->attr('src'));

        // ない時
        // データ更新
        // Topコンテント取得
        // コンテント確認
    }
}
