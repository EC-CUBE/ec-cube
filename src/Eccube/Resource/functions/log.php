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

if (!function_exists('log_emergency')) {
    function log_emergency($message, array $context = [])
    {
        $app = \Eccube\Application::getInstance();
        if (isset($app['eccube.logger'])) {
            $app['eccube.logger']->emergency($message, $context);
        }
    }
}

if (!function_exists('log_alert')) {
    function log_alert($message, array $context = [])
    {
        $app = \Eccube\Application::getInstance();
        if (isset($app['eccube.logger'])) {
            $app['eccube.logger']->alert($message, $context);
        }
    }
}

if (!function_exists('log_critical')) {
    function log_critical($message, array $context = [])
    {
        $app = \Eccube\Application::getInstance();
        if (isset($app['eccube.logger'])) {
            $app['eccube.logger']->critical($message, $context);
        }
    }
}

if (!function_exists('log_error')) {
    function log_error($message, array $context = [])
    {
        $app = \Eccube\Application::getInstance();
        if (isset($app['eccube.logger'])) {
            $app['eccube.logger']->error($message, $context);
        }
    }
}

if (!function_exists('log_warning')) {
    function log_warning($message, array $context = [])
    {
        $app = \Eccube\Application::getInstance();
        if (isset($app['eccube.logger'])) {
            $app['eccube.logger']->warning($message, $context);
        }
    }
}

if (!function_exists('log_notice')) {
    function log_notice($message, array $context = [])
    {
        $app = \Eccube\Application::getInstance();
        if (isset($app['eccube.logger'])) {
            $app['eccube.logger']->notice($message, $context);
        }
    }
}

if (!function_exists('log_info')) {
    function log_info($message, array $context = [])
    {
        $app = \Eccube\Application::getInstance();
        if (isset($app['eccube.logger'])) {
            $app['eccube.logger']->info($message, $context);
        }
    }
}

if (!function_exists('log_debug')) {
    function log_debug($message, array $context = [])
    {
        $app = \Eccube\Application::getInstance();
        if (isset($app['eccube.logger'])) {
            $app['eccube.logger']->debug($message, $context);
        }
    }
}
