<?php

declare(strict_types=1);

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
use PHPUnit\Framework\Attributes\Group;
use Plugin\MigrationSample\DoctrineMigrations\Version20181101012712;
use Plugin\MigrationSample\PluginManager;

#[Group('plugin-service')]
final class PluginManagerTest extends EccubeTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestIncomplete('Symfony 7.4 アップグレード後に対応予定');
    }

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
        $this->assertContains($migrationTableName, $tables);

        // migrationが実行され、バージョンが記録されることを確認
        $expected = Version20181101012712::class;
        $actual = $connection->fetchOne('select version from '.$migrationTableName);
        $this->assertSame($expected, $actual);
    }
}
