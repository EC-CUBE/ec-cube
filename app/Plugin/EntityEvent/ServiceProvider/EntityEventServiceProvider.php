<?php

namespace Plugin\EntityEvent\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Plugin\EntityEvent\Entity\BaseInfoListener;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

class EntityEventServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        $app['plugin.entity_event.base_info_listener'] = function (Container $container) {
            return new BaseInfoListener();
        };
    }

    public function boot(Application $app)
    {
        $app['eccube.entity.event.dispatcher']
            ->addEventListener($app['plugin.entity_event.base_info_listener']);
    }
}
