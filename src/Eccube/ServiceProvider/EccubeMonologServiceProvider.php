<?php

namespace Eccube\ServiceProvider;

use Eccube\Monolog\Handler\EccubeMonologHelper;
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
        $app['monolog.factory'] = $app->protect(function ($channelName, array $values) use ($app) {

            $log = new $app['monolog.logger.class']($channelName);

            $helper = new EccubeMonologHelper($app);
            $log->pushHandler($helper->getHandler($channelName, $values));

            return $log;
        });

        // チャネルに応じてログを作成し、フロント、管理、プラグイン用のログ出力クラスを作成
        $channels = $app['config']['log']['channel'];
        // monologの設定は除外
        unset($channels['monolog']);
        foreach ($channels as $channel => $value) {
            $app[$channel.'.monolog'] = $app->share(function ($app) use ($channel, $value) {
                return $app['monolog.factory']($channel, $value);
            });
        }

        // MonologServiceProviderで定義されているmonolog.handlerの置換
        $app['monolog.handler'] = $app->share(function ($app) {
            $helper = new EccubeMonologHelper($app);
            $value = $app['config']['log']['channel']['monolog'];
            return $helper->getHandler('monolog', $value);
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
