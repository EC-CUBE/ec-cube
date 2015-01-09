<?php

namespace Eccube\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class EccubeServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['ecube.customer'] = function () use ($app) {
            return new \Eccube\Framework\Customer();
        };
        $app['ecube.cookie'] = function () use ($app) {
            return new \Eccube\Framework\Cookie();
        };
        $app['ecube.check_error'] = function () use ($app) {
            return new \Eccube\Framework\CheckError();
        };
        $app['ecube.display'] = $app->protect(function ($hasPrevURL = true) use ($app) {
            return new \Eccube\Framework\Display($hasPrevURL);
        });
        $app['ecube.response'] = function () use ($app) {
            return new \Eccube\Framework\Response();
        };
        $app['ecube.query'] = function () use ($app) {
            return \Eccube\Framework\Query::getSingletonInstance();
        };
        $app['ecube.response.action_exit'] = function () use ($app) {
            return \Eccube\Framework\Response::actionExit();
        };
        $app['ecube.site_session'] = function () use ($app) {
            return new \Eccube\Framework\SiteSession();
        };
        $app['ecube.sendmail'] = function () use ($app) {
            return new \Eccube\Framework\Sendmail();
        };
        
        // db
        $app['ecube.db.factory'] = function () use ($app) {
            return new \Eccube\Framework\DB\DB_DBFactory();
        };
        
        // helper
        $app['ecube.helper.db'] = function () use ($app) {
            return new \Eccube\Framework\Helper\DbHelper();
        };
        $app['ecube.helper.db.func'] = $app->protect(function ($name, $parameter = null) use ($app) {
            return call_user_func('\\Eccube\\Framework\\Helper\\DbHelper::'.$name, $parameter);
        });
        $app['ecube.helper.page_layout'] = function () use ($app) {
            return new \Eccube\Framework\Helper\PageLayoutHelper();
        };
        $app['ecube.helper.purchase'] = function () use ($app) {
            return new \Eccube\Framework\Helper\PurchaseHelper();
        };
        $app['ecube.helper.plugin'] = function () use ($app) {
            $plugin_activate_flg = true;
            return \Eccube\Framework\Helper\PluginHelper::getSingletonInstance($plugin_activate_flg);
        };
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
