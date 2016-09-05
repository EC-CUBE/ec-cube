<?php

namespace Eccube\Tests\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Eccube\Tests\Fixture\Generator as FixtureGenerator;

/**
 * FixtureServiceProvider
 *
 * @author Kentaro Ohkouchi
 */
class FixtureServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['eccube.fixture.generator'] = $app->share(function () use ($app) {
            return new FixtureGenerator($app);
        });
    }

    public function boot(Application $app)
    {
        // quiet
    }
}
