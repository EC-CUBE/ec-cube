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

use Doctrine\ORM\QueryBuilder;
use Eccube\DI\AutoWiring\QueryExtensionAutoWiring;
use Eccube\Doctrine\Query\Queries;
use Eccube\Repository\QueryKey;
use Eccube\Tests\DI\Test\QueryCustomizerClass;

class QueryExtensionScannerTest extends AbstractAutowiringTest
{
    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
        $this->container['eccube.queries'] = function() {
            return new Queries();
        };
    }

    protected function getAutoWiring()
    {
        return new QueryExtensionAutoWiring([__DIR__.'/../Test']);
    }

    public function testQueryExtension()
    {
        $this->di->build($this->container);

        self::assertArrayHasKey(QueryCustomizerClass::class, $this->container);
    }

    public function testCustomize()
    {
        $this->di->build($this->container);

        $qb = $this->createMock(QueryBuilder::class);
        $this->container['eccube.queries']->customize(QueryKey::PRODUCT_SEARCH, $qb, []);

        self::assertTrue($this->container[QueryCustomizerClass::class]->customized);
    }
}
