<?php

namespace Eccube\ServiceProvider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

/**
 * @see https://github.com/jbinfo/MobileDetectServiceProvider/pull/4
 */
class MobileDetectServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['mobile_detect'] = function($app) {
            return new \Mobile_Detect();
        };

        $app['mobile_detect.device_type'] = function ($app) {
            if ($app['mobile_detect']->isMobile()) {
                return \Eccube\Entity\Master\DeviceType::DEVICE_TYPE_SP;
            } else {
                return \Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC;
            }
        };
    }
}
