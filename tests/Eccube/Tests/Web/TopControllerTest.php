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
use Eccube\Entity\Page;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\PageRepository;

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

    /**
     * TOPページ metaタグのテスト
     */
    public function testMetaTags()
    {
        // description を設定
        $description = 'あのイーハトーヴォのすきとおった風、夏でも底に冷たさをもつ青いそら、うつくしい森で飾られたモリーオ市、郊外のぎらぎらひかる草の波。';
        /** @var PageRepository $pageRepository */
        $pageRepository = $this->entityManager->getRepository(Page::class);
        $page = $pageRepository->getByUrl('homepage');
        $page->setDescription($description);
        $this->entityManager->flush();

        /** @var BaseInfoRepository $baseInfoRepository */
        $baseInfoRepository = $this->entityManager->getRepository(BaseInfo::class);
        $shopName = $baseInfoRepository->get()->getShopName();
        $expected_desc = mb_substr($description, 0, 120, 'utf-8');

        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));

        $this->assertEquals($shopName, $crawler->filter('meta[property="og:site_name"]')->attr('content'));
        $this->assertEquals('website', $crawler->filter('meta[property="og:type"]')->attr('content'));
        $this->assertEquals($expected_desc, $crawler->filter('meta[name="description"]')->attr('content'));
        $this->assertEquals($expected_desc, $crawler->filter('meta[property="og:description"]')->attr('content'));
    }
}
