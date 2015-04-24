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

namespace Eccube\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class InstallControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        /* @var $controllers \Silex\ControllerCollection */
        $controllers = $app['controllers_factory'];

        // installer
        $controllers->match('/install/', "\\Eccube\\Controller\\InstallController::index")->bind('install');
        $controllers->match('/install/complete', "\\Eccube\\Controller\\InstallController::complete")->bind('install_complete');

        return $controllers;
    }
}
