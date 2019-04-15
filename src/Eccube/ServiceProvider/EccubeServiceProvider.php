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

namespace Eccube\ServiceProvider;

use Eccube\Application;
use Eccube\Common\EccubeConfig;

class EccubeServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['orm.em'] = $app->share(function () use ($app) {
            return $app->getParentContainer()->get('doctrine')->getManager();
        });

        $app['config'] = $app->share(function () use ($app) {
            if ($app->getParentContainer()->has(EccubeConfig::class)) {
                return $app->getParentContainer()->get(EccubeConfig::class);
            }

            return [];
        });

        $app['monolog.logger'] = $app->share(function () use ($app) {
            return $app->getParentContainer()->get('logger');
        });
        $app['monolog'] = $app->share(function () use ($app) {
            return $app['monolog.logger'];
        });
        $app['eccube.logger'] = $app->share(function () use ($app) {
            return $app->getParentContainer()->get('eccube.logger');
        });

        $app['session'] = $app->share(function () use ($app) {
            return $app->getParentContainer()->get('session');
        });

        $app['form.factory'] = $app->share(function () use ($app) {
            return $app->getParentContainer()->get('form.factory');
        });

        $app['security'] = $app->share(function () use ($app) {
            return $app->getParentContainer()->get('security.token_storage');
        });

        $app['user'] = $app->share(function () use ($app) {
            return $app['security']->getToken()->getUser();
        });

        $app['dispatcher'] = $app->share(function () use ($app) {
            return $app->getParentContainer()->get('event_dispatcher');
        });

        $app['translator'] = $app->share(function () use ($app) {
            return $app->getParentContainer()->get('translator');
        });

        $app['eccube.event.dispatcher'] = $app->share(function () use ($app) {
            return $app['dispatcher'];
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
