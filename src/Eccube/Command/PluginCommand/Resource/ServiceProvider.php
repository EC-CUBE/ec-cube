<?php

/*
 * This file is part of the [code]
 *
 * Copyright (C) [year] [author]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\[code]\ServiceProvider;

use Eccube\Application;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Plugin\[code]\Form\Type\[code]ConfigType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class [code]ServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // プラグイン用設定画面
        $app->match('/'.$app['config']['admin_route'].'/plugin/[code]/config', 'Plugin\[code]\Controller\ConfigController::index')->bind('plugin_[code]_config');

        // 独自コントローラ
        $app->match('/plugin/[lower_code]/hello', 'Plugin\[code]\Controller\[code]Controller::index')->bind('plugin_[code]_hello');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new [code]ConfigType();

            return $types;
        }));

        // Repository

        // Service

        // メッセージ登録
        // $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
        // $app['translator']->addResource('yaml', $file, $app['locale']);

        // load config
        // プラグイン独自の定数はconfig.ymlの「const」パラメータに対して定義し、$app['[lower_code]config']['定数名']で利用可能
        // if (isset($app['config']['[code]']['const'])) {
        //     $config = $app['config'];
        //     $app['[lower_code]config'] = $app->share(function () use ($config) {
        //         return $config['[code]']['const'];
        //     });
        // }

        // ログファイル設定
        $app['monolog.logger.[lower_code]'] = $app->share(function ($app) {

            $logger = new $app['monolog.logger.class']('[lower_code]');

            $filename = $app['config']['root_dir'].'/app/log/[lower_code].log';
            $RotateHandler = new RotatingFileHandler($filename, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                '[lower_code]_{date}',
                'Y-m-d'
            );

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::ERROR),
                    0,
                    true,
                    true,
                    Logger::INFO
                )
            );

            return $logger;
        });

    }

    public function boot(BaseApplication $app)
    {
    }

}
