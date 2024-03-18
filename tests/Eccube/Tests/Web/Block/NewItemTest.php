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

namespace Eccube\Tests\Web\Block;

use Eccube\Tests\Web\AbstractWebTestCase;

class NewItemTest extends AbstractWebTestCase
{
    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp(): void
    {
        parent::setUp();

        // レイアウト管理に自動取得の新着商品を追加
        $sql = "
            insert into dtb_block_position (
                section,
                block_id,
                layout_id,
                block_row,
                discriminator_type)
            values(
                7,
                18,
                1,
                3,
                'blockposition'
            );";

        $this->entityManager->getConnection()->exec($sql);
    }
    public function testNewItemBlock()
    {
        // 新着商品が表示されている
        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));
        $node = $crawler->filter('.ec-newItemRole__listItemTitle');
        $this->assertTrue(count($node) > 0);
    }

    public function testAutoNewItemBlock()
    {
        // 自動取得の新着商品が表示されている
        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));
        $node = $crawler->filter('.__getAutoNewItemBlock');
        $this->assertTrue(count($node) > 0);
    }
}
