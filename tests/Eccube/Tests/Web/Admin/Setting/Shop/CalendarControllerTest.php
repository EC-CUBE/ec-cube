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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class CalendarControllerTest extends AbstractAdminWebTestCase
{
    public function testCalendar()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_setting_shop_tax')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    // TODO テスト実装
}
