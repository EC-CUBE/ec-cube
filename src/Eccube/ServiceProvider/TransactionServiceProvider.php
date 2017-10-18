<?php

namespace Eccube\ServiceProvider;


use Eccube\EventListener\TransactionListener;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TransactionServiceProvider implements EventListenerProviderInterface, ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['eccube.listener.transaction.enabled'] = true;

        $app['eccube.listener.transaction'] = function (Container $app) {
            $listener = new TransactionListener(
                $app['orm.em'],
                $app['logger'],
                $app['eccube.listener.transaction.enabled']
            );

            return $listener;
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['eccube.listener.transaction']);
    }
}