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

namespace Plugin\QueryCustomize\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Plugin\QueryCustomize\Entity\AdminCustomerCustomizer;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

class QueryCustomizeServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        $app['plugin.query_customize.customer_search'] = function (Container $container) {
            return new AdminCustomerCustomizer();
        };
    }

    public function boot(Application $app)
    {
        $app['eccube.queries']
            ->addCustomizer($app['plugin.query_customize.customer_search']);
    }
}
