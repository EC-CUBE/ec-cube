<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eccube\Tests\Application;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Silex\Provider\MonologServiceProvider;

/**
 * MonologTrait test cases.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @requires PHP 5.4
 */
class MonologTraitTest extends TestCase
{
    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    public function testLog()
    {
        $this->markTestIncomplete('Eccube\Application に依存しないようにする');
        $app = $this->createApplication();

        $app->log('Foo');
        $app->log('Bar', array(), Logger::DEBUG);
        $this->assertTrue($app['monolog.handler']->hasInfo('Foo'));
        $this->assertTrue($app['monolog.handler']->hasDebug('Bar'));
    }

    public function createApplication()
    {
        $app = new \Eccube\Application(['eccube.autoloader' => $GLOBALS['eccube.autoloader']]);
        $app->register(new MonologServiceProvider(), array(
            'monolog.handler' => function () use ($app) {
                return new TestHandler($app['monolog.level']);
            },
        ));

        return $app;
    }
}
