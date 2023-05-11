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

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Repository\NewsRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class NewsControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->newsRepository = $this->entityManager->getRepository(\Eccube\Entity\News::class);
    }

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
            $this->generateUrl('admin_content_news_edit', ['id' => $News->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentNewsDelete()
    {
        $Member = $this->createMember();
        $News = $this->createNews($Member);

        $this->loginTo($Member);

        $this->client->request('DELETE',
            $this->generateUrl('admin_content_news_delete', ['id' => $News->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_content_news')));
    }

    /**
     * 危険なXSS htmlインジェクションが削除されたことを確認するテスト
     *
     * 下記のものをチェックします。
     *     ・ ID属性の追加
     *     ・ <script> スクリプトインジェクション
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/5372
     */
    public function testPurifyXssInput(): void
    {
        $Member = $this->createMember();
        $News = $this->createNews($Member);

        $formData = [
            '_token' => 'dummy',
            'publish_date' => '2022-09-12T18:03:18',
            'title' => 'test',
            'description' =>  "<div id='dangerous-id' class='safe_to_use_class'>
                <p>新着情報テスト</p>
                <script>alert('XSS Attack')</script>
                <a href='https://www.google.com'>safe html</a>
            </div>",
            'url' => 'https://example.com',
            'link_method' => 1,
            'visible' => 1,
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_content_news_edit', ['id' => $News->getId()]),
            ['admin_news' => $formData]
        );

        $crawler = new Crawler($News->getDescription());

        // <div>タグから危険なid属性が削除されていることを確認する。
        // Find that dangerous id attributes are removed from <div> tags.
        $target = $crawler->filter('#dangerous-id');
        $this->assertEquals(0, $target->count());

        // 安全なclass属性が出力されているかどうかを確認する。
        // Find if classes (which are safe) have been outputted
        $target = $crawler->filter('.safe_to_use_class');
        $this->assertEquals(1, $target->count());

        // 安全なHTMLが存在するかどうかを確認する
        // Find if the safe HTML exists
        $this->assertStringContainsString('<p>新着情報テスト</p>', $target->outerHtml());
        $this->assertStringContainsString('<a href="https://www.google.com">safe html</a>', $target->outerHtml());

        // 安全でないスクリプトが存在しないかどうかを確認する
        // Find if the unsafe script does not exist
        $this->assertStringNotContainsString("<script>alert('XSS Attack')</script>", $target->outerHtml());
    }

    private function createNews($TestCreator, $sortNo = 1)
    {
        $TestNews = new \Eccube\Entity\News();
        $TestNews
            ->setPublishDate(new \DateTime())
            ->setTitle('テストタイトル'.$sortNo)
            ->setDescription('テスト内容'.$sortNo)
            ->setUrl('http://example.com/')
            ->setLinkMethod(false)
            ->setVisible(true)
            ->setCreator($TestCreator);

        $this->entityManager->persist($TestNews);
        $this->entityManager->flush($TestNews);

        return $TestNews;
    }
}
