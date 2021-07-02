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

class BlockControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('block_search_product')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * SNSボタンの表示テスト
     */
    public function testSocialButtons()
    {
        // TOPページはSNSボタンが表示される
        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));
        $node = $crawler->filter('.ec-socialButtons');
        $this->assertNotEmpty($node);

        // 商品詳細ページはSNSボタンが表示される
        $crawler = $this->client->request('GET', $this->generateUrl('product_detail', ['id' => '1']));
        $node = $crawler->filter('.ec-socialButtons');
        $this->assertNotEmpty($node);

        // マイページはSNSボタンが表示されない
        $crawler = $this->client->request('GET', $this->generateUrl('mypage_login'));
        $node = $crawler->filter('.ec-socialButtons');
        $this->assertEmpty($node);

        // カートページはSNSボタンが表示されない
        $crawler = $this->client->request('GET', $this->generateUrl('cart'));
        $node = $crawler->filter('.ec-socialButtons');
        $this->assertEmpty($node);

        // 会員登録ページはSNSボタンが表示されない
        $crawler = $this->client->request('GET', $this->generateUrl('entry'));
        $node = $crawler->filter('.ec-socialButtons');
        $this->assertEmpty($node);
    }
}
