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

namespace Eccube\Tests\Web\Admin\Setting\System;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class SystemControllerTest
 */
class SystemControllerTest extends AbstractAdminWebTestCase
{
    /**
     * testRoutingAdminSettingSystemSystemIndex
     */
    public function testRoutingAdminSettingSystemSystemIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_setting_system_system')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * testInfoAdminSettingSystemSystem
     */
    public function testInfoAdminSettingSystemSystem()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_setting_system_system', ['mode' => 'info'])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
