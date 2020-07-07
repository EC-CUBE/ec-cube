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

namespace Plugin\MigrationSample;

use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PluginManager.
 */
class PluginManager extends AbstractPluginManager
{
    const VERSION = '1.0.0';

    /**
     * Install the plugin.
     */
    public function install(array $meta, ContainerInterface $container)
    {
        dump('install '.self::VERSION);
    }

    /**
     * Update the plugin.
     */
    public function update(array $meta, ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine')->getManager();
        dump('update '.self::VERSION);
        $this->migration($entityManager->getConnection(), $meta['code']);
    }

    /**
     * Enable the plugin.
     */
    public function enable(array $meta, ContainerInterface $container)
    {
        dump('enable '.self::VERSION);
    }

    /**
     * Disable the plugin.
     */
    public function disable(array $meta, ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine')->getManager();
        dump('disable '.self::VERSION);
        $this->migration($entityManager->getConnection(), $meta['code'], '0');
    }

    /**
     * Uninstall the plugin.
     */
    public function uninstall(array $meta, ContainerInterface $container)
    {
        dump('uninstall '.self::VERSION);
    }
}
