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

use Eccube\DI\AutoWiring\RepositoryDefinition;
use Eccube\Repository\ProductRepository;
use Eccube\Tests\DI\Test\Repository\TestRepository;
use PHPUnit\Framework\TestCase;

class RepositoryDefinitionTest extends TestCase
{
    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    public function testGetEntityName_Ecucube_Entity()
    {
        $this->markTestIncomplete('Eccube\DI は使用しないかも');
        $refClass = new \ReflectionClass(ProductRepository::class);
        $def = new RepositoryDefinition(ProductRepository::class, $refClass, null);
        self::assertEquals("Eccube\\Entity\\Product", $def->getEntityName());
    }

    public function testGetEntityName_Test()
    {
        $this->markTestIncomplete('Eccube\DI は使用しないかも');
        $refClass = new \ReflectionClass(TestRepository::class);
        $def = new RepositoryDefinition(TestRepository::class, $refClass, null);
        self::assertEquals("Eccube\\Tests\\DI\\Test\\Entity\\Test", $def->getEntityName());
    }
}
