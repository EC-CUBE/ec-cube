<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Bundle\RateLimiterBundle\DependencyInjection;

use Eccube\Bundle\RateLimiterBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class RateLimiterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);
    }

    public function getAlias()
    {
        return 'eccube_rate_limiter';
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return parent::getConfiguration($config, $container);
    }
}
