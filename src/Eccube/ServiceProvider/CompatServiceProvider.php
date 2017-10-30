<?php

namespace Eccube\ServiceProvider;

use Eccube\Service\CartService;
use Eccube\Service\CsvExportService;
use Eccube\Service\MailService;
use Eccube\Service\OrderHelper;
use Eccube\Service\OrderService;
use Eccube\Service\PaymentService;
use Eccube\Service\PluginService;
use Eccube\Service\ShoppingService;
use Eccube\Service\SystemService;
use Eccube\Service\TaxRuleService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CompatServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        $app['eccube.service.cart'] = function () use ($app) {
            return $app[CartService::class];
        };
        $app['eccube.service.order'] = function () use ($app) {
            return $app[OrderService::class];
        };
        $app['eccube.service.tax_rule'] = function () use ($app) {
            return $app[TaxRuleService::class];
        };
        $app['eccube.service.plugin'] = function () use ($app) {
            return $app[PluginService::class];
        };
        $app['eccube.service.mail'] = function () use ($app) {
            return $app[MailService::class];
        };
        $app['eccube.helper.order'] = function ($app) {
            return $app[OrderHelper::class];
        };
        $app['eccube.service.csv.export'] = function () use ($app) {
            return $app[CsvExportService::class];
        };
        $app['eccube.service.shopping'] = function () use ($app) {
            return $app[ShoppingService::class];
        };
        $app['eccube.service.payment'] = function () use ($app) {
            return $app[PaymentService::class];
        };
        $app['eccube.service.system'] = function () use ($app) {
            return $app[SystemService::class];
        };

    }
}
