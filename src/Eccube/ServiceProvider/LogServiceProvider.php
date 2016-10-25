<?php

namespace Eccube\ServiceProvider;

use Eccube\EventListener\LogListener;
use Eccube\Log\Logger;
use Eccube\Log\Monolog\Helper\LogHelper;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Class LogServiceProvider
 *
 * @package Eccube\ServiceProvider
 */
class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->register(new \Silex\Provider\MonologServiceProvider());

        // Log
        $app['eccube.logger'] = $app->share(function ($app) {
            return new Logger($app);
        });

        // ヘルパー作成
        $app['eccube.monolog.helper'] = $app->share(function ($app) {
            return new LogHelper($app);
        });

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
            $app['monolog.logger.'.$channel] = $app->share(function ($app) use ($channelValues) {
                return $app['eccube.monolog.factory']($channelValues);
            });
        }

        // MonologServiceProviderで定義されているmonolog.handlerの置換
        $channelValues = $app['config']['log']['channel']['monolog'];
        $app['monolog.name'] = $channelValues['name'];
        $app['monolog.handler'] = $app->share(function ($app) use ($channelValues) {
            return $app['eccube.monolog.helper']->getHandler($channelValues);
        });

        $app['eccube.monolog.listener'] = $app->share(function () use ($app) {
            return new LogListener($app['eccube.logger']);
        });

        $app['listener.requestdump'] = $app->share(function ($app) {
            return new \Eccube\EventListener\RequestDumpListener($app);
        });
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber($app['listener.requestdump']);
        $app['dispatcher']->addSubscriber($app['eccube.monolog.listener']);
    }
}
