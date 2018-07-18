<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

class InstallServiceProvider implements ServiceProviderInterface, BootableProviderInterface
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
