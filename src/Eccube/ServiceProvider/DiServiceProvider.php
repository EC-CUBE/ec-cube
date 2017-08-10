<?php

namespace Eccube\ServiceProvider;

use Eccube\Di\Di;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DiServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['eccube.di'] = function (Container $app) {
            $di = new Di(
                $app['eccube.di.dirs'],
                $app['annotations'],
                $app['eccube.di.cache_dir'],
                $app['eccube.di.debug']
            );

            return $di;
        };

        $app['eccube.di.dirs'] = null;
        $app['eccube.di.cache_dir'] = null;
        $app['eccube.di.debug'] = $app['debug'];
    }
}
