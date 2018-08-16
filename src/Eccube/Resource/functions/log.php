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

function log_emergency($message, array $context = [])
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['eccube.logger'])) {
        $app['eccube.logger']->emergency($message, $context);
    }
}

function log_alert($message, array $context = [])
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['eccube.logger'])) {
        $app['eccube.logger']->alert($message, $context);
    }
}

function log_critical($message, array $context = [])
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['eccube.logger'])) {
        $app['eccube.logger']->critical($message, $context);
    }
}

function log_error($message, array $context = [])
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['eccube.logger'])) {
        $app['eccube.logger']->error($message, $context);
    }
}

function log_warning($message, array $context = [])
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['eccube.logger'])) {
        $app['eccube.logger']->warning($message, $context);
    }
}

function log_notice($message, array $context = [])
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['eccube.logger'])) {
        $app['eccube.logger']->notice($message, $context);
    }
}

function log_info($message, array $context = [])
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['eccube.logger'])) {
        $app['eccube.logger']->info($message, $context);
    }
}

function log_debug($message, array $context = [])
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['eccube.logger'])) {
        $app['eccube.logger']->debug($message, $context);
    }
}

/**
 * プラグイン用ログ出力関数
 *
 * @param $channel 設定されたchannel名
 *
 * @return \Symfony\Bridge\Monolog\Logger
 */
function logs($channel)
{
    $app = \Eccube\Application::getInstance();

    $container = $app->getParentContainer();

    return $container->get('monolog.logger.'.$channel);
}
