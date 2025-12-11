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
use Psr\Container\ContainerInterface;

/**
 * Class PluginManager.
 */
class PluginManager extends AbstractPluginManager
{
    public const VERSION = '1.0.0';

    /**
     * Install the plugin.
     *
     * @param array<mixed> $meta
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function install(array $meta, ContainerInterface $container): void
    {
        dump('install '.self::VERSION);
    }

    /**
     * Update the plugin.
     *
     * @param array<mixed> $meta
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function update(array $meta, ContainerInterface $container): void
    {
        $entityManager = $container->get('doctrine')->getManager();
        dump('update '.self::VERSION);
        $this->migration($entityManager->getConnection(), $meta['code']);
    }

    /**
     * Enable the plugin.
     *
     * @param array<mixed> $meta
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function enable(array $meta, ContainerInterface $container): void
    {
        dump('enable '.self::VERSION);
    }

    /**
     * Disable the plugin.
     *
     * @param array<mixed> $meta
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function disable(array $meta, ContainerInterface $container): void
    {
        $entityManager = $container->get('doctrine')->getManager();
        dump('disable '.self::VERSION);
        $this->migration($entityManager->getConnection(), $meta['code'], '0');
    }

    /**
     * Uninstall the plugin.
     *
     * @param array<mixed> $meta
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function uninstall(array $meta, ContainerInterface $container): void
    {
        dump('uninstall '.self::VERSION);
    }
}
