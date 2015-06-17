<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Tests\Repository;

use Eccube\Application;
use Eccube\Entity\Master\DeviceType;

class PageLayoutRepositoryTest extends AbstractRepositoryTestCase
{

/* privateなMethodにしたのでテストは別途考える
    public function test_getNewPageId()
    {
        $app = $this->createApplication();

        $actual = $app['eccube.repository.page_layout']
            ->getNewPageId($app['config']['device_type_pc']);
        $expected = 29;
        $this->assertSame($actual, $expected);

    }
*/

    public function test_findOrCreate_pageIdNullisCreate()
    {
        $app = $this->createApplication();

        $expected = null;
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);
        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate(null, $DeviceType);
        $actual = $PageLayout->getUrl();

        $this->assertSame($actual, $expected);
    }

    public function test_findOrCreate_findTopPage()
    {
        $app = $this->createApplication();

        $expected = array(
            'url' => 'homepage',
            'DeviceType' => DeviceType::DEVICE_TYPE_PC,
        );

        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);
        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate(1, $DeviceType);
        $actual = array(
            'url' => $PageLayout->getUrl(),
            'DeviceType' => $PageLayout->getDeviceType()->getId(),
        );

        $this->assertSame($actual, $expected);
    }

    /*public function test_findOrCreate_findMobileMyPage()
    {
        // 非対応予定のためスキップ
        self::markTestSkipped();

        $app = $this->createApplication();

        $expected = array(
            'url' => 'mypage/index.php',
            'device_type_id' => $app['config']['device_type_mobile'],
        );

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate(6, $app['config']['device_type_mobile']);
        $actual = array(
            'url' => $PageLayout->getUrl(),
            'device_type_id' => $PageLayout->getDeviceTypeId(),
        );

        $this->assertSame($actual, $expected);
    }*/

    public function test_findOrCreate_findSmartphoneProduct()
    {
        // 非対応予定のためスキップ
        self::markTestSkipped();

        $app = $this->createApplication();

        $expected = array(
            'url' => 'products/list.php',
            'DeviceType' => DeviceType::DEVICE_TYPE_SP,
        );

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate(2, DeviceType::DEVICE_TYPE_SP);
        $actual = array(
            'url' => $PageLayout->getUrl(),
            'DeviceType' => $PageLayout->getDeviceType()->getId(),
        );

        $this->assertSame($actual, $expected);
    }

    /* FIXME: CI環境で定数が整っていないのでコケるひとまずコメントアウト
    public function test_getTemplateFile_DefaultTemplateFile_isValid()
    {
        $app = $this->createApplication();

        $actual = $app['eccube.repository.page_layout']
            ->getTemplateFile('mypage/change', 10);

        $expected = array(
            'file_name' =>'change.tpl',
            'tpl_data' => file_get_contents($app['config']['template_realdir'] . 'mypage/change.tpl')
        );

        $this->assertSame($actual, $expected);
    }
    */

    public function tearDown()
    {
        $app = $this->createApplication();
        $app['orm.em']->getConnection()->close();
        parent::tearDown();
    }
}
