<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Tests\DI\AutoWiring;

use Eccube\DI\AutoWiring\ComponentAutoWiring;
use Eccube\Tests\DI\Test\ComponentClass;
use Eccube\Tests\DI\Test\ComponentInjectClass;
use Eccube\Tests\DI\Test\IdentifiedComponentClass;
use Eccube\Tests\DI\Test\NoComponentClass;

class AutoWiring extends AbstractAutowiringTest
{

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    protected function getAutoWiring()
    {
        return new ComponentAutoWiring([__DIR__.'/../Test']);
    }

    public function testComponentWithoutId()
    {
        $this->di->build($this->container);

        // コンテナに登録されていることを確認
        self::assertArrayHasKey(ComponentClass::class, $this->container);
        self::assertArrayNotHasKey(NoComponentClass::class, $this->container);
    }

    public function testComponentWithId()
    {
        $this->di->build($this->container);

        self::assertArrayHasKey('__Eccube_Tests_Di_Test_IdentifiedComponentClass__', $this->container);
        self::assertArrayNotHasKey(IdentifiedComponentClass::class, $this->container);
    }

    public function testComponentWithDependencies()
    {
        $this->di->build($this->container);

        // インジェクションされていることを確認
        $instance = $this->container[ComponentInjectClass::class];
        self::assertInstanceOf(ComponentInjectClass::class, $instance);
        self::assertInstanceOf(ComponentClass::class, $instance->component);
    }

}
