<?php

namespace Eccube\Plugin;

use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\Configuration\Configuration;

class AbstractPluginManager {

    CONST MIGRATION_TABLE_PREFIX = 'migration_';

    public function migrationSchema($app,$migrationFilePath,$pluginCode,$version=null)
    {
        $config = new Configuration($app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');
        $config->setMigrationsDirectory($migrationFilePath);
        $config->registerMigrationsFromDirectory($migrationFilePath );
        $config->setMigrationsTableName(self::MIGRATION_TABLE_PREFIX.$pluginCode);
        $migration = new Migration($config);
                                  // null 又は 'last' を渡すと最新バージョンまでマイグレートする
                                  // 0か'first'を渡すと最初に戻る
        $migration->migrate($version, false); 


    }
}
