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

use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\Master\OrderStatusRepository;

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
        // GAスクリプト表示がある時
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->get();
        $BaseInfo->setGaId('UA-12345678-1');
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));
        $node = $crawler->filterXPath('//script[contains(@src, "googletagmanager")]');
        $this->assertEquals('https://www.googletagmanager.com/gtag/js?id=UA-12345678-1', $node->attr('src'));

        // GAスクリプト表示がない時
        $BaseInfo->setGaId('');
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));
        $node = $crawler->filterXPath('//script[contains(@src, "googletagmanager")]');
        $this->assertEmpty($node);
    }
}
