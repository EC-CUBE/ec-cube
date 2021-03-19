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

class CalendarControllerTest extends AbstractWebTestCase
{
    public function testCalendar()
    {
        $this->client->request('GET', '/block/calendar');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

//    public function testTodayStile()
//    {
//        // TODO スタイルが当たってからテスト実装開始
//        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
//        $node = $crawler->filter('');
//        $this->assertEquals('', $node->attr(''));
//    }
}
