<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\ServiceProvider;

use Eccube\Tests\Fixture\Generator as FixtureGenerator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

/**
 * FixtureServiceProvider
 *
 * @author Kentaro Ohkouchi
 */
class FixtureServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        $app['eccube.fixture.generator'] = function ($app) {
            return new FixtureGenerator($app);
        };

        $app['eccube.fixture.generator.locale'] = $app->protect(function ($locale) use ($app) {
            return new FixtureGenerator($app, $locale);
        });
    }

    public function boot(Application $app)
    {
        // quiet
    }
}
