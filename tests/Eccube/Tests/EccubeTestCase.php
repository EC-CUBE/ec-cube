<?php

namespace Eccube\Tests;

use Eccube\Application;

abstract class EccubeTestCase extends \PHPUnit_Framework_TestCase
{

    public static function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        $app->initialize();
        $app->initPluginEventDispatcher();
        $app['session.test'] = true;
        $app['exception_handler']->disable();
        $app->boot();
        return $app;
    }

}
