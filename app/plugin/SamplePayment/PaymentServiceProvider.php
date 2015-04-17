<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SamplePayment;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Plugin\SamplePayment\PaymentService;

class PaymentServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        $app->match('/payment/install', '\\Plugin\\SamplePayment\\Controller\\InstallController::index')->bind('mdl_payment_install');
        $app->match('/shopping/mdl_payment', '\\Plugin\\SamplePayment\\Controller\\PaymentController::index')->bind('mdl_payment');

        $app['eccube.plugin.service.payment'] = $app->share(function () use ($app) {
            return new \Plugin\SamplePayment\Service\PaymentService($app);
        });

        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\SamplePayment\Form\Type\PaymentType();

            return $types;
        }));

        // カード決済会社と通信するモック
        $app['com.card.service'] = $app->share(function() use ($app) {
            return new \Plugin\SamplePayment\Service\CardCompanyService($app);
        });
    }

    public function boot(BaseApplication $app)
    {
    }
}
