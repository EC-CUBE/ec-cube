<?php

namespace Eccube\ServiceProvider;

use Eccube\Monolog\Helper\EccubeMonologHelper;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Class EccubeMonologServiceProvider
 *
 * @package Eccube\ServiceProvider
 */
class EccubeMonologServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->register(new \Silex\Provider\MonologServiceProvider());

        // ログクラス作成ファクトリー
        $app['monolog.factory'] = $app->protect(function ($channelName, array $channelValues) use ($app) {

            $log = new $app['monolog.logger.class']($channelName);

            $helper = new EccubeMonologHelper($app);
            // EccubeMonologHelper内でHandlerを設定している
            $log->pushHandler($helper->getHandler($channelValues));

            return $log;
        });

        // チャネルに応じてログを作成し、フロント、管理、プラグイン用のログ出力クラスを作成
        $channels = $app['config']['log']['channel'];
        // monologの設定は除外
        unset($channels['monolog']);
        foreach ($channels as $channel => $channelValues) {
            $app['monolog.logger.'.$channel] = $app->share(function ($app) use ($channel, $channelValues) {
                return $app['monolog.factory']($channel, $channelValues);
            });
        }

        // MonologServiceProviderで定義されているmonolog.handlerの置換
        $app['monolog.handler'] = $app->share(function ($app) {
            $helper = new EccubeMonologHelper($app);
            $channelValues = $app['config']['log']['channel']['monolog'];

            return $helper->getHandler($channelValues);
        });

        $app['listener.requestdump'] = $app->share(function ($app) {
            return new \Eccube\EventListener\RequestDumpListener($app);
        });
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber($app['listener.requestdump']);
    }
}
