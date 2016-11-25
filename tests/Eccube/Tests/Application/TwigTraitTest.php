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
    public function setUp()
    {
        parent::setUp();

        $paths = array();
        $paths[] = $this->app['config']['template_admin_realdir'];
        $paths[] = $this->app['config']['template_realdir'];
        $paths[] = $this->app['config']['template_default_realdir'];
        $this->app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));
        $app['admin'] = true;
        $app['front'] = true;
    }

    public function testRender()
    {
        $app = $this->app;

        $parameters = array('error_title' => 'error', 'error_message' => 'error');
        $response = $app->render('error.twig', $parameters);
        $this->assertEquals('Symfony\Component\HttpFoundation\Response', get_class($response));
        $this->assertStringStartsWith('<!doctype html>', $response->getContent());
    }

    public function testRenderKeepResponse()
    {
        $app = $this->app;

        $parameters = array('error_title' => 'error', 'error_message' => 'error');
        $response = $app->render('error.twig', $parameters, new Response('', 404));
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testRenderForStream()
    {
        if (php_sapi_name() == 'phpdbg') {
            $this->markTestSkipped('Can not support of ob_*()');
        }
        $app = $this->app;

        $parameters = array('error_title' => 'error', 'error_message' => 'error');
        $response = $app->render('error.twig', $parameters, new StreamedResponse());
        $this->assertEquals('Symfony\Component\HttpFoundation\StreamedResponse', get_class($response));

        ob_start();
        $response->send();
        $this->assertStringStartsWith('<!doctype html>', ob_get_clean());
    }

    public function testRenderView()
    {
        $app = $this->app;

        $parameters = array('error_title' => 'error', 'error_message' => 'error');
        $app->renderView('error.twig', $parameters);
    }
}
