<?php

namespace Eccube\ServiceProvider;


use Eccube\EventListener\ForwardOnlyListener;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ForwardOnlyServiceProvider implements EventListenerProviderInterface, ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['eccube.listener.forward_only'] = function (Container $app) {
            return new ForwardOnlyListener();
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['eccube.listener.forward_only']);
    }
}