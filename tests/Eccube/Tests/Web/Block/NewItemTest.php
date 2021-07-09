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
    public function testNewItemBlock()
    {
        // 新着商品が表示されている
        $crawler = $this->client->request('GET', $this->generateUrl('homepage'));
        $node = $crawler->filter('.ec-newItemRole__listItemTitle');
        $this->assertTrue(count($node) > 0);
    }
}
