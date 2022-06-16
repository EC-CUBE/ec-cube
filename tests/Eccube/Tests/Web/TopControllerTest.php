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
     * 危険なXSS htmlインジェクションが削除されたことを確認するテスト

     * 下記のものをチェックします。
     *     ・ ID属性の追加
     *     ・ <script> スクリプトインジェクション
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/5372
     * @return void
     */
    public function testFeaturedNewsXSSAttackPrevention()
    {
        // Create a new news item for the homepage with a XSS attack (via <script> AND id attribute injection)
        $Member = $this->createMember();
        $sortNo = 1;
        $TestNews = new \Eccube\Entity\News();
        $TestNews
            ->setPublishDate(new \DateTime())
            ->setTitle('テストタイトル' . $sortNo)
            ->setDescription(
                "<div id='test-news-id' class='safe_to_use_class'>
                    <p>新着情報テスト＃１</p>
                    <script>alert('XSS Attack')</script>
                    <a href='https://www.google.com'>safe html</a>
                </div>"
            )
            ->setUrl('http://example.com/')
            ->setLinkMethod(false)
            ->setVisible(true)
            ->setCreator($Member);
        $this->entityManager->persist($TestNews);
        $this->entityManager->flush($TestNews);

        // 1つの新着情報を保存した後にホームページにアクセスする。
        // Request Homepage after saving a single news item
        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // <div>タグから危険なid属性が削除されていることを確認する。
        // Find that dangerous id attributes are removed from <div> tags.
        $testNewsArea_notFoundTest = $crawler->filter('#test-news-id');
        $this->assertEquals(0, $testNewsArea_notFoundTest->count());

        // 安全なclass属性が出力されているかどうかを確認する。
        // Find if classes (which are safe) have been outputted
        $testNewsArea = $crawler->filter('.safe_to_use_class');
        $this->assertEquals(1, $testNewsArea->count());

        // 安全なHTMLが存在するかどうかを確認する
        // Find if the safe HTML exists
        $this->assertStringContainsString('<p>新着情報テスト＃１</p>', $testNewsArea->outerHtml());
        $this->assertStringContainsString('<a href="https://www.google.com">safe html</a>', $testNewsArea->outerHtml());

        // 安全でないスクリプトが存在しないかどうかを確認する
        // Find if the unsafe script does not exist
        $this->assertStringNotContainsString("<script>alert('XSS Attack')</script>", $testNewsArea->outerHtml());
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
