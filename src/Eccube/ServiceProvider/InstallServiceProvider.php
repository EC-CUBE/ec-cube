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

use Pimple\Container;
use Silex\Api\BootableProviderInterface;
use Pimple\ServiceProviderInterface;
use Silex\Application;


class InstallServiceProvider  implements ServiceProviderInterface, BootableProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app An Pimple\Container instance
     */
    public function register(Container $app)
    {
        $app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Eccube\Form\Extension\HelpTypeExtension();

            return $extensions;
        });

        $app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Eccube\Form\Type\Install\Step1Type($app);
            $types[] = new \Eccube\Form\Type\Install\Step3Type($app);
            $types[] = new \Eccube\Form\Type\Install\Step4Type($app);
            $types[] = new \Eccube\Form\Type\Install\Step5Type($app);

            return $types;
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
