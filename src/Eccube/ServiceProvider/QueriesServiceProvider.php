<?php


namespace Eccube\ServiceProvider;


use Eccube\Entity\Event\EntityEventDispatcher;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueriesServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $app)
    {
        $app['eccube.queries'] = function () {
            return new \Eccube\Doctrine\Query\Queries();
        };
    }
}
