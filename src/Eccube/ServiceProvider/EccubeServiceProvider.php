<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
