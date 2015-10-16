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


namespace Eccube\Tests\Web;

use Eccube\Application;
use Eccube\Tests\Mock\CsrfTokenMock;
use Silex\WebTestCase;

abstract class AbstractWebTestCase extends WebTestCase
{

    protected $client = null;
    protected static $server = null;

    public function setUp()
    {
        parent::setUp();

        if ($this->client == null) {
            if (self::$server == null) {
                self::$server = static::createClient();
            }
            $this->client = self::$server;
        }
    }

    /**
     * @link http://stackoverflow.com/questions/13537545/clear-memory-being-used-by-php
     */
    public function tearDown()
    {
        parent::tearDown();
        $this->app['orm.em']->getConnection()->close();
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }

    public static function tearDownAfterClass()
    {
        self::$server = null;
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = new Application();
        $app->initialize();
        $app->initPluginEventDispatcher();
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        $app['form.csrf_provider'] = $app->share(function () {
            return new CsrfTokenMock();
        });

        $app->boot();

        return $app;
    }
}
