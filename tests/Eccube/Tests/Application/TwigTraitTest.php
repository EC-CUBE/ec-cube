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

use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * TwigTrait test cases.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @requires PHP 5.4
 */
class TwigTraitTest extends EccubeTestCase
{
    public function testRender()
    {
        $app = $this->createApplication();

        $response = $app->render('error.twig');
        $this->assertEquals('Symfony\Component\HttpFoundation\Response', get_class($response));
        $this->assertStringStartsWith('<!doctype html>', $response->getContent());
    }

    public function testRenderKeepResponse()
    {
        $app = $this->createApplication();

        $response = $app->render('error.twig', array(), new Response('', 404));
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testRenderForStream()
    {
        if (php_sapi_name() == 'phpdbg') {
            $this->markTestSkipped('Can not support of ob_*()');
        }
        $app = $this->createApplication();

        $response = $app->render('error.twig', array(), new StreamedResponse());
        $this->assertEquals('Symfony\Component\HttpFoundation\StreamedResponse', get_class($response));

        ob_start();
        $response->send();
        $this->assertStringStartsWith('<!doctype html>', ob_get_clean());
    }

    public function testRenderView()
    {
        $app = $this->createApplication();

        $app->renderView('error.twig');
    }

    public function createApplication()
    {
        $app = new \Eccube\Application();

        // ログの内容をERRORレベルでしか出力しないように設定を上書き
        $app['config'] = $app->share($app->extend('config', function ($config, \Silex\Application $app) {
            $config['log']['log_level'] = 'ERROR';
            $config['log']['action_level'] = 'ERROR';
            $config['log']['passthru_level'] = 'ERROR';

            $channel = $config['log']['channel'];
            foreach (array('monolog', 'front', 'admin') as $key) {
                $channel[$key]['log_level'] = 'ERROR';
                $channel[$key]['action_level'] = 'ERROR';
                $channel[$key]['passthru_level'] = 'ERROR';
            }
            $config['log']['channel'] = $channel;

            return $config;
        }));
        $app->initLogger();

        $app->initialize();
        $app->initializePlugin();
        $app->boot();

        $paths = array();
        $paths[] = $app['config']['template_admin_realdir'];
        $paths[] = $app['config']['template_realdir'];
        $paths[] = $app['config']['template_default_realdir'];
        $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));
        $app['admin'] = true;
        $app['front'] = true;

        return $app;
    }
}
