<?php

namespace Eccube\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PaymentServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['payment.method'] = $app->protect(function ($clazz, $form) use ($app) {
            $PaymentMethod = new $clazz();
            $PaymentMethod->setApplication($app);
            $PaymentMethod->setFormType($form);

            return $PaymentMethod;
        });

        $app['payment.method.request'] = $app->protect(function ($clazz, $form, $request) use ($app) {
            $PaymentMethod = new $clazz();
            $PaymentMethod->setApplication($app);
            $PaymentMethod->setFormType($form);
            $PaymentMethod->setRequest($request);

            return $PaymentMethod;
        });

        $app['eccube.service.payment'] = $app->protect(function ($clazz) use ($app) {
            $Service = new $clazz($app['request_stack']);

            return $Service;
        });
    }
}
