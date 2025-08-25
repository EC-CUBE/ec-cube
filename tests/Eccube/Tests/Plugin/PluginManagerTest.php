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

namespace Eccube\Tests\Plugin;

use Eccube\Plugin\AbstractPluginManager;
use Eccube\Tests\EccubeTestCase;
use Plugin\MigrationSample\PluginManager;

/**
 * @group plugin-service
 */
class PluginManagerTest extends EccubeTestCase
{
    public function testMigration()
    {
        $pluginManager = new PluginManager();
        $connection = $this->entityManager->getConnection();
        $pluginCode = 'MigrationSample';
        $version = null;
        $migrationFilePath = null;
        $pluginManager->migration($connection, $pluginCode, $version, $migrationFilePath);

        // migration用のテーブルが生成されていることを確認
        $tables = $connection->createSchemaManager()->listTableNames();
        $migrationTableName = AbstractPluginManager::MIGRATION_TABLE_PREFIX.strtolower($pluginCode);
        self::assertContains($migrationTableName, $tables);

        // migrationが実行され、バージョンが記録されることを確認
        $expected = \Plugin\MigrationSample\DoctrineMigrations\Version20181101012712::class;
        $actual = $connection->fetchOne('select version from '.$migrationTableName);
        self::assertSame($expected, $actual);
    }
}
