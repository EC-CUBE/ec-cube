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
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\MigratorConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractPluginManager
{
    public const MIGRATION_TABLE_PREFIX = 'migration_';

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

        if (null == $version) {
            $version = 'latest';
        }

        $migrationNamespace = 'Plugin\\'.$pluginCode.'\\DoctrineMigrations';
        $migrationTableName = self::MIGRATION_TABLE_PREFIX.strtolower($pluginCode);
        $configuration = new Configuration();
        $configuration->addMigrationsDirectory($migrationNamespace, $migrationFilePath);
        $configuration->setAllOrNothing(false);
        $configuration->setCheckDatabasePlatform(false);

        $storageConfiguration = new TableMetadataStorageConfiguration();
        $storageConfiguration->setTableName($migrationTableName);
        $configuration->setMetadataStorageConfiguration($storageConfiguration);

        $dependencyFactory = DependencyFactory::fromConnection(
            new ExistingConfiguration($configuration),
            new ExistingConnection($connection)
        );

        $dependencyFactory->getMetadataStorage()->ensureInitialized();

        $migratorConfiguration = (new MigratorConfiguration())
            ->setDryRun(false)
            ->setTimeAllQueries(false)
            ->setAllOrNothing(false);

        $version = $dependencyFactory->getVersionAliasResolver()->resolveVersionAlias($version);
        $planCalculator = $dependencyFactory->getMigrationPlanCalculator();
        $plan = $planCalculator->getPlanUntilVersion($version);
        $migrator = $dependencyFactory->getMigrator();
        $migrator->migrate($plan, $migratorConfiguration);
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
