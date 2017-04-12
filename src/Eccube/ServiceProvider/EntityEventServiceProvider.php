<?php


namespace Eccube\ServiceProvider;


use Eccube\Entity\Event\EntityEventDispatcher;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EntityEventServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $container)
    {
        $container['eccube.entity.event.dispatcher'] = function($container) {
            return new EntityEventDispatcher($container['orm.em']);
        };
    }
}