<?php

namespace Eccube\ServiceProvider;

use Doctrine\Common\Annotations\AnnotationReader;
use Eccube\Di\Di;
use Eccube\Di\ProviderGenerator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

class DiServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        $app['eccube.di'] = function (Container $app) {
            $di = new Di(
                $app['eccube.di.dirs'],
                $app['eccube.di.annotation_reader'],
                $app['eccube.di.generator'],
                $app['eccube.di.debug']
            );

            return $di;
        };

        $app['eccube.di.generator'] = function (Container $app) {
            $generator = new ProviderGenerator(
                $app['eccube.di.generator.dir'],
                $app['eccube.di.generator.class']
            );

            return $generator;
        };

        $app['eccube.di.dirs'] = [];
        $app['eccube.di.debug'] = $app['debug'];
        $app['eccube.di.annotation_reader'] = isset($app['annotations']) ? $app['annotations'] : new AnnotationReader();

        $app['eccube.di.generator.dir'] = null;
        $app['eccube.di.generator.class'] = 'ServiceProviderCache';
    }

    public function boot(Application $app)
    {
        $app['eccube.di']->build($app);
    }
}
