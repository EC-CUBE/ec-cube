<?php

namespace Eccube\ServiceProvider;

use Eccube\EventListener\LogListener;
use Eccube\Log\Logger;
use Eccube\Log\Monolog\Helper\LogHelper;
use Silex\Application;
use Silex\Api\BootableProviderInterface;
use Pimple\ServiceProviderInterface;
use Pimple\Container;

/**
 * Class LogServiceProvider
 *
 * @package Eccube\ServiceProvider
 */
class LogServiceProvider implements BootableProviderInterface, ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->register(new \Silex\Provider\MonologServiceProvider());

        // Log
        $app['eccube.logger'] = function ($app) {
            return new Logger($app);
        };

        // ヘルパー作成
        $app['eccube.monolog.helper'] = function ($app) {
            return new LogHelper($app);
        };

        // ログクラス作成ファクトリー
        $app['eccube.monolog.factory'] = $app->protect(function (array $channelValues) use ($app) {

            $log = new $app['monolog.logger.class']($channelValues['name']);

            // EccubeMonologHelper内でHandlerを設定している
            $log->pushHandler($app['eccube.monolog.helper']->getHandler($channelValues));

            return $log;
        });

        // チャネルに応じてログを作成し、フロント、管理、プラグイン用のログ出力クラスを作成
        $channels = $app['config']['log']['channel'];
        // monologの設定は除外
        unset($channels['monolog']);
        foreach ($channels as $channel => $channelValues) {
            $app['monolog.logger.'.$channel] = function ($app) use ($channelValues) {
                return $app['eccube.monolog.factory']($channelValues);
            };
        }

        // MonologServiceProviderで定義されているmonolog.handlerの置換
        $channelValues = $app['config']['log']['channel']['monolog'];
        $app['monolog.name'] = $channelValues['name'];
        $app['monolog.handler'] = function ($app) use ($channelValues) {
            return $app['eccube.monolog.helper']->getHandler($channelValues);
        };

        $app['eccube.monolog.listener'] = function () use ($app) {
            return new LogListener($app['eccube.logger']);
        };

        $app['listener.requestdump'] = function ($app) {
            return new \Eccube\EventListener\RequestDumpListener($app);
        };
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber($app['listener.requestdump']);
        $app['dispatcher']->addSubscriber($app['eccube.monolog.listener']);
    }
}
