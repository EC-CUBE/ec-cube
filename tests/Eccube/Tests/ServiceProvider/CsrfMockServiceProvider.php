<?php

namespace Eccube\Tests\ServiceProvider;

use Pimple\Container;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Pimple\ServiceProviderInterface;
use Eccube\Tests\Mock\CsrfTokenManagerMock;

/**
 * CsrfMockServiceProvider
 *
 * @author Kentaro Ohkouchi
 */
class CsrfMockServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        $app['csrf.token_manager'] = function () {
            return new CsrfTokenManagerMock();
        };
    }

    public function boot(Application $app)
    {
        // quiet
    }
}
