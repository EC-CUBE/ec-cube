<?php
/*
* This file is part of MobileDetectServiceProvider.
*
* (c) Lhassan Baazzi <baazzilhassan@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace Eccube\ServiceProvider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
/**
 * @see https://github.com/jbinfo/MobileDetectServiceProvider/pull/4
 */
class MobileDetectServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['mobile_detect'] = function($c) {
            return new \Mobile_Detect();
        };
    }
}
