<?php

namespace Eccube\DI;

use Doctrine\Common\Annotations\AnnotationReader;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

class DIServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        $app['eccube.di'] = function (Container $app) {
            $di = new DependencyBuilder(
                $app['eccube.di.generator.dir'],
                $app['eccube.di.generator.class'],
                $app['eccube.di.wirings'],
                $app['eccube.di.annotation_reader'],
                $app['eccube.di.debug']
            );

            return $di;
        };

        $app['eccube.di.debug'] = $app['debug'];
        $app['eccube.di.annotation_reader'] = isset($app['annotations']) ? $app['annotations'] : new AnnotationReader();

        $app['eccube.di.generator.dir'] = null;
        $app['eccube.di.generator.class'] = 'ServiceProviderCache';
    }

    public function boot(Application $app)
    {
        $app['eccube.di']->build($app);

        // XXX これを入れないと EntityEventDispatcher が有効になった時テストがコケる
        if (!$app->offsetExists('eccube.event.dispatcher')) {
            $app['eccube.event.dispatcher'] = function () use ($app) {
                return $app['dispatcher'];
            };
        }

        // XXX これ入れないと EntityEventDispatcher が登録されない
        if ($app->offsetExists('eccube.entity.event.dispatcher')) {
            $e = $app['eccube.entity.event.dispatcher'];
        }
    }
}
