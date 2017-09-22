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


use Doctrine\Common\Annotations\AnnotationReader;
use Eccube\Di\Di;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

abstract class AbstractScannerTest extends TestCase
{
    /**
     * @var Di
     */
    protected $di;

    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->di = new Di(__DIR__, 'TestServiceProviderCache_'.(new \ReflectionClass($this))->getShortName(), [$this->getAutoWiring()], new AnnotationReader(), true);
        $this->container = new Container();
    }

    abstract protected function getAutoWiring();

    public function tearDown()
    {
        $path = $this->di->getProviderPath();

        if (file_exists($path)) {
            unlink($path);
        }
    }
}