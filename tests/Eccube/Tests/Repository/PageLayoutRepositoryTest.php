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

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Entity\Master\DeviceType;

class PageLayoutRepositoryTest extends EccubeTestCase
{
    protected $DeviceType;

    public function setUp()
    {
        parent::setUp();
        $this->DeviceType = $this->app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);
    }

    public function test_findOrCreate_pageIdNullisCreate()
    {
        $expected = null;
        $PageLayout = $this->app['eccube.repository.page_layout']
            ->findOrCreate(null, $this->DeviceType);
        $actual = $PageLayout->getUrl();

        $this->assertSame($actual, $expected);
    }

    public function test_findOrCreate_findTopPage()
    {
        $expected = array(
            'url' => 'homepage',
            'DeviceType' => DeviceType::DEVICE_TYPE_PC,
        );

        $PageLayout = $this->app['eccube.repository.page_layout']
            ->findOrCreate(1, $this->DeviceType);
        $actual = array(
            'url' => $PageLayout->getUrl(),
            'DeviceType' => $PageLayout->getDeviceType()->getId(),
        );

        $this->assertSame($actual, $expected);
    }
}
