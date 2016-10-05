<?php

namespace Eccube\ServiceProvider;

use Eccube\Processor\RequestProcessor;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\UidProcessor;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * MonologServiceProvider for EC-CUBE.
 *
 * @author Kentaro Ohkouchi
 */
class EccubeMonologServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->register(new \Silex\Provider\MonologServiceProvider());
        $app['monolog.handler'] = function () use ($app) {
            $levels = Logger::getLevels();
            if ($app['debug']) {
                $level = Logger::DEBUG;
            } else {
                $level = $app['config']['log']['log_level'];
            }

            $RotateHandler = new RotatingFileHandler($app['monolog.logfile'], $app['config']['log']['max_files'], $level);
            $RotateHandler->setFilenameFormat(
                $app['config']['log']['prefix'].'{date}'.$app['config']['log']['suffix'],
                $app['config']['log']['format']
            );

            $format = "[%datetime%] %channel%.%level_name% [%token%] [%uid%] [%class%:%function%] - %message% %context% %extra% [%url%, %ip%, %referrer%]\n";
           // $format = "[%datetime%] %channel%.%level_name% [%token%] [%uid%] [%class%:%function%] - %message% %extra% [%url%, %ip%, %referrer%]\n";
            //$format = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            //$format = "[%datetime%] %msec% %level_name% %file%:%line% %message% %extra%\n";
            //$format = "[%datetime%] [%token%] %level_name% %file%:%line% %message% %extra%\n";

            $RotateHandler->setFormatter(new LineFormatter($format, 'Y-m-d H:i:s,u', true, true));

            $FingerCrossedHandler = new FingersCrossedHandler(
                $RotateHandler,
                new ErrorLevelActivationStrategy($levels[$app['config']['log']['action_level']])
            );

            $FingerCrossedHandler->pushProcessor(function ($record) use ($app) {
                return $record;
            });

            $FingerCrossedHandler->pushProcessor(function ($record) {
                // 出力ログからファイル名を削除し、lineを最終項目にセットしなおす
//                unset($record['extra']['file']);
//                $line = $record['extra']['line'];
//                unset($record['extra']['line']);
//                $record['extra']['line'] = $line;

                return $record;
            });

            $ip = new IntrospectionProcessor();
            // $FingerCrossedHandler->pushProcessor($ip);
            $web = new RequestProcessor();
            // $FingerCrossedHandler->pushProcessor($web);

            $uid = new UidProcessor(8);

            $FingerCrossedHandler->pushProcessor(function ($record) use ($app, $uid, $ip, $web) {
                $sessionId = substr(sha1($app['session']->getId()), 0, 8);
                $record['token'] = $sessionId;
                $record['uid'] = $uid->getUid();


                $record['url'] = $web->serverData['REQUEST_URI'];
                $record['ip'] = $web->serverData['REMOTE_ADDR'];
                $record['referrer'] = $web->serverData['HTTP_REFERER'];

                //$record['class'] = $ip->

                return $record;
            });


            return $FingerCrossedHandler;
        };
        $app['listener.requestdump'] = $app->share(function ($app) {
            return new \Eccube\EventListener\RequestDumpListener($app);
        });
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber($app['listener.requestdump']);
    }
}
