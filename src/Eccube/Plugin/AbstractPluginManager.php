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

namespace Eccube\Plugin;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractPluginManager
{
    const MIGRATION_TABLE_PREFIX = 'migration_';

    /**
     * プラグインのマイグレーションを実行する.
     *
     * PluginManager 実行時に、 Doctrine SchemaUpdate が自動的に行なわれるため、
     * このメソッドは主にデータの更新に使用する.
     *
     * 引数 $version で指定したバージョンまでマイグレーションする.
     * null を渡すと最新バージョンまでマイグレートする.
     * 0 を渡すと最初に戻る。
     *
     * @param Connection $connection Doctrine Connection
     * @param string $pluginCode プラグインコード
     * @param string $version マイグレーション先のバージョン
     * @param string $migrationFilePath マイグレーションファイルを格納したファイルパス. 指定しない場合は app/Plugin/<pluginCode>/DoctrineMigrations を使用する
     */
    public function migration(Connection $connection, $pluginCode, $version = null, $migrationFilePath = null)
    {
        if (null === $migrationFilePath) {
            $migrationFilePath = __DIR__.'/../../../app/Plugin/'.$pluginCode.'/DoctrineMigrations';
        }
        $config = new Configuration($connection);
        $config->setMigrationsNamespace('\Plugin\\'.$pluginCode.'\DoctrineMigrations');
        $config->setMigrationsDirectory($migrationFilePath);
        $config->registerMigrationsFromDirectory($migrationFilePath);
        $config->setMigrationsTableName(self::MIGRATION_TABLE_PREFIX.$pluginCode);
        $migration = new Migration($config);
        $migration->migrate($version, false);
    }

    /**
     * Install the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function install(array $meta, ContainerInterface $container)
    {
        // quiet.
    }

    /**
     * Update the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function update(array $meta, ContainerInterface $container)
    {
        // quiet.
    }

    /**
     * Enable the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function enable(array $meta, ContainerInterface $container)
    {
        // quiet.
    }

    /**
     * Disable the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function disable(array $meta, ContainerInterface $container)
    {
        // quiet.
    }

    /**
     * Uninstall the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function uninstall(array $meta, ContainerInterface $container)
    {
        // quiet.
    }
}
