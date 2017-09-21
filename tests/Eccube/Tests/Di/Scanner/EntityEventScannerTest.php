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

namespace Eccube\Tests\Di\Scanner;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Eccube\Di\Scanner\EntityEventScanner;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Event\EntityEventDispatcher;
use Eccube\Tests\Di\Test\PostUpdateEventClass;
use Eccube\Tests\Di\Test\PreUpdateEventClass;

class EntityEventScannerTest extends AbstractScannerTest
{
    public function setUp()
    {
        parent::setUp();
        $this->container['eccube.entity.event.dispatcher'] = function() {
            $eventManager = $this->createMock(EventManager::class);
            $em = $this->createMock(EntityManager::class);
            $em->method('getEventManager')->willReturn($eventManager);
            return new EntityEventDispatcher($em);
        };
    }

    protected function getAutoWiring()
    {
        return new EntityEventScanner([__DIR__.'/../Test']);
    }

    public function testEntityEvent()
    {
        $this->di->build($this->container);

        self::assertArrayHasKey(PreUpdateEventClass::class, $this->container);
        self::assertArrayHasKey(PostUpdateEventClass::class, $this->container);
    }

    public function testEventDispatch()
    {
        $this->di->build($this->container);

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getEntity')->willReturn(new BaseInfo());

        /**
         * @var EntityEventDispatcher
         */
        $eventDispatcher = $this->container['eccube.entity.event.dispatcher'];
        $eventDispatcher->preUpdate($eventArgs);

        self::assertTrue($this->container[PreUpdateEventClass::class]->executed);
    }
}
