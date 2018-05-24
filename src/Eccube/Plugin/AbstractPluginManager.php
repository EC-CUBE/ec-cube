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

class AbstractPluginManager
{
    const MIGRATION_TABLE_PREFIX = 'migration_';

    public function migrationSchema($app, $migrationFilePath, $pluginCode, $version = null)
    {
        $config = new Configuration($app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');
        $config->setMigrationsDirectory($migrationFilePath);
        $config->registerMigrationsFromDirectory($migrationFilePath);
        $config->setMigrationsTableName(self::MIGRATION_TABLE_PREFIX.$pluginCode);
        $migration = new Migration($config);
        $migration->setNoMigrationException(true);
        // null 又は 'last' を渡すと最新バージョンまでマイグレートする
        // 0か'first'を渡すと最初に戻る
        $migration->migrate($version, false);
    }
}
