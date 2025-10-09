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

namespace Plugin\InstallTestPlugin;

use Eccube\Plugin\AbstractPluginManager;
use Psr\Container\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function install(array $config, ContainerInterface $container)
    {
        echo '*******************************************'.PHP_EOL;
        echo 'Install InstallTestPlugin 1.0.0'.PHP_EOL;
        echo '*******************************************'.PHP_EOL;

        // インストール時のテスト用処理
        $this->createTestTable($container);
    }

    public function enable(array $config, ContainerInterface $container)
    {
        echo '*******************************************'.PHP_EOL;
        echo 'Enable InstallTestPlugin 1.0.0'.PHP_EOL;
        echo '*******************************************'.PHP_EOL;

        // 有効化時のテスト用処理
        $this->insertTestData($container);
    }

    public function disable(array $config, ContainerInterface $container)
    {
        echo '*******************************************'.PHP_EOL;
        echo 'Disable InstallTestPlugin 1.0.0'.PHP_EOL;
        echo '*******************************************'.PHP_EOL;

        // 無効化時のテスト用処理
        $this->clearTestData($container);
    }

    public function update(array $config, ContainerInterface $container)
    {
        echo '*******************************************'.PHP_EOL;
        echo 'Update InstallTestPlugin 1.0.0'.PHP_EOL;
        echo '*******************************************'.PHP_EOL;
    }

    public function uninstall(array $config, ContainerInterface $container)
    {
        echo '*******************************************'.PHP_EOL;
        echo 'Uninstall InstallTestPlugin 1.0.0'.PHP_EOL;
        echo '*******************************************'.PHP_EOL;

        // アンインストール時のテスト用処理
        $this->dropTestTable($container);
    }

    /**
     * テスト用テーブルを作成
     */
    private function createTestTable(ContainerInterface $container)
    {
        $connection = $container->get('doctrine.dbal.default_connection');

        $sql = 'CREATE TABLE IF NOT EXISTS plg_install_test_plugin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_name VARCHAR(255) NOT NULL,
            test_value TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )';

        $connection->executeStatement($sql);
    }

    /**
     * テスト用データを挿入
     */
    private function insertTestData(ContainerInterface $container)
    {
        $connection = $container->get('doctrine.dbal.default_connection');

        $sql = 'INSERT INTO plg_install_test_plugin (test_name, test_value) VALUES (?, ?)';
        $connection->executeStatement($sql, ['test_enable', 'Plugin enabled successfully']);
    }

    /**
     * テスト用データをクリア
     */
    private function clearTestData(ContainerInterface $container)
    {
        $connection = $container->get('doctrine.dbal.default_connection');

        $sql = 'DELETE FROM plg_install_test_plugin WHERE test_name = ?';
        $connection->executeStatement($sql, ['test_enable']);
    }

    /**
     * テスト用テーブルを削除
     */
    private function dropTestTable(ContainerInterface $container)
    {
        $connection = $container->get('doctrine.dbal.default_connection');

        $sql = 'DROP TABLE IF EXISTS plg_install_test_plugin';
        $connection->executeStatement($sql);
    }
}
