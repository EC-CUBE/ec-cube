<?php

namespace Eccube\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

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
        $app['monolog.handler'] = function() use ($app) {
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
            $RotateHandler->setFormatter(new LineFormatter(null, null, true));
            $FingerCrossedHandler = new FingersCrossedHandler(
                $RotateHandler,
                new ErrorLevelActivationStrategy($levels[$app['config']['log']['action_level']])
            );
            return $FingerCrossedHandler;
        };
        $app['listener.requestdump'] = $app->share(function($app) {
            return new \Eccube\EventListener\RequestDumpListener($app);
        });
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber($app['listener.requestdump']);
    }
}
