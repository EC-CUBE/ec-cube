<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Sample;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Plugin\Sample\SamplePluginService;

class SamplePluginServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        $app->match('/plugin/sample', '\\Plugin\\Sample\\Controller\\SamplePluginServiceController::index')->bind('plugin_sample');

        $app['eccube.plugin.sample.service'] = $app->share(function () use ($app) {
            return new \Plugin\Sample\Service\SamplePluginService($app);
        });
    }

    public function boot(BaseApplication $app)
    {
    }
}
