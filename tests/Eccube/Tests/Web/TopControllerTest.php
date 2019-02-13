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
}
