<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Plugin;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractPluginManager
{
    const MIGRATION_TABLE_PREFIX = 'migration_';

    /**
     * Migrate the schema.
     *
     * @param ContainerInterface $container
     * @param string $migrationFilePath
     * @param string $pluginCode
     * @param string $version
     * @return array An array of migration sql statements. This will be empty if the the $confirm callback declines to execute the migration
     */
    public function migrationSchema(ContainerInterface $container, $migrationFilePath, $pluginCode, $version = null)
    {
        $config = new Configuration($container->get('doctrine.dbal.connection'));
        $config->setMigrationsNamespace('DoctrineMigrations');
        $config->setMigrationsDirectory($migrationFilePath);
        $config->registerMigrationsFromDirectory($migrationFilePath);
        $config->setMigrationsTableName(self::MIGRATION_TABLE_PREFIX.$pluginCode);
        $migration = new Migration($config);
        $migration->setNoMigrationException(true);
        // null 又は 'last' を渡すと最新バージョンまでマイグレートする
        // 0か'first'を渡すと最初に戻る
        return $migration->migrate($version, false);
    }

    /**
     * Install the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    abstract public function install(array $meta, ContainerInterface $container);

    /**
     * Update the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    abstract public function update(array $meta, ContainerInterface $container);

    /**
     * Enable the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    abstract public function enable(array $meta, ContainerInterface $container);

    /**
     * Disable the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    abstract public function disable(array $meta, ContainerInterface $container);

    /**
     * Uninstall the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    abstract public function uninstall(array $meta, ContainerInterface $container);
}
