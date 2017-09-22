<?php

namespace Eccube\ServiceProvider;

use Doctrine\Common\Annotations\AnnotationReader;
use Eccube\Di\Di;
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
                $app['eccube.di.generator.dir'],
                $app['eccube.di.generator.class'],
                $app['eccube.di.scanners'],
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
    }
}
