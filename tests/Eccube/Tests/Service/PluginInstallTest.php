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

namespace Eccube\Tests\Service;

use Eccube\Common\Constant;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class PluginInstallTest
 */
#[Group('plugin-service')]
#[Group('plugin-install-test')]
class PluginInstallTest extends AbstractServiceTestCase
{
    private ?PluginService $service = null;

    private ?PluginRepository $pluginRepository = null;

    private ?string $mockServerUrl = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = static::getContainer()->get(PluginService::class);
        $this->pluginRepository = $this->entityManager->getRepository(Plugin::class);

        // MockサーバのURLを設定（環境変数から取得、デフォルトはlocalhost:8080）
        $this->mockServerUrl = $_ENV['ECCUBE_PACKAGE_API_URL'] ?? 'http://127.0.0.1:8080';
    }

    protected function tearDown(): void
    {
        $this->cleanupTestPlugins();
        parent::tearDown();
    }

    /**
     * テスト用プラグインのクリーンアップ
     */
    private function cleanupTestPlugins()
    {
        $dirs = [];
        $finder = new Finder();
        $iterator = $finder
            ->in(static::getContainer()->getParameter('kernel.project_dir').'/app/Plugin')
            ->name('InstallTestPlugin*')
            ->directories();
        foreach ($iterator as $dir) {
            $dirs[] = $dir->getPathName();
        }

        foreach ($dirs as $dir) {
            $this->deleteFile($dir);
        }

        $files = Finder::create()
            ->in(static::getContainer()->getParameter('kernel.project_dir').'/app/proxy/entity')
            ->files();
        $f = new Filesystem();
        $f->remove($files);

        $this->deleteAllRows(['dtb_plugin']);
    }

    /**
     * ファイルを削除
     */
    private function deleteFile(mixed $path)
    {
        $f = new Filesystem();
        $f->remove($path);
    }

    /**
     * Mockサーバからプラグインをダウンロードしてインストールするテスト
     */
    public function testInstallPluginFromMockServer()
    {
        // Mockサーバが利用可能かチェック
        if (!$this->isMockServerAvailable()) {
            $this->markTestSkipped('Mock server is not available at '.$this->mockServerUrl);
        }

        // プラグインアーカイブのURL
        $pluginUrl = $this->mockServerUrl.'/InstallTestPlugin-1.0.0.tgz';

        // プラグインをダウンロード
        $tempFile = $this->downloadPluginFromUrl($pluginUrl);
        $this->assertFileExists($tempFile, 'Plugin archive should be downloaded successfully');

        try {
            // プラグインをインストール
            $result = $this->service->install($tempFile);
            $this->assertTrue($result, 'Plugin should be installed successfully');

            // プラグインがデータベースに登録されているかチェック
            $plugin = $this->pluginRepository->findOneBy(['code' => 'InstallTestPlugin']);
            $this->assertInstanceOf(Plugin::class, $plugin, 'Plugin should be registered in database');
            $this->assertSame('InstallTestPlugin', $plugin->getCode());
            $this->assertSame('1.0.0', $plugin->getVersion());
            $this->assertSame(Constant::DISABLED, (int) $plugin->isEnabled());

            // プラグインファイルが存在するかチェック
            $pluginDir = static::getContainer()->getParameter('kernel.project_dir').'/app/Plugin/InstallTestPlugin';
            $this->assertDirectoryExists($pluginDir, 'Plugin directory should exist');
            $this->assertFileExists($pluginDir.'/PluginManager.php', 'PluginManager.php should exist');
            $this->assertFileExists($pluginDir.'/Controller/TestController.php', 'TestController.php should exist');

            // プラグインを有効化
            $this->service->enable($plugin);
            $this->entityManager->refresh($plugin);
            $this->assertSame(Constant::ENABLED, (int) $plugin->isEnabled());

            // テスト用テーブルが作成されているかチェック
            $connection = $this->entityManager->getConnection();
            $tables = $connection->createSchemaManager()->listTableNames();
            $this->assertContains('plg_install_test_plugin', $tables, 'Test table should be created');

            // テスト用データが挿入されているかチェック
            $testData = $connection->fetchAssociative(
                'SELECT * FROM plg_install_test_plugin WHERE test_name = ?',
                ['test_enable']
            );
            $this->assertNotFalse($testData, 'Test data should be inserted');
            $this->assertSame('Plugin enabled successfully', $testData['test_value']);

            // プラグインを無効化
            $this->service->disable($plugin);
            $this->entityManager->refresh($plugin);
            $this->assertSame(Constant::DISABLED, (int) $plugin->isEnabled());

            // テスト用データがクリアされているかチェック
            $testData = $connection->fetchAssociative(
                'SELECT * FROM plg_install_test_plugin WHERE test_name = ?',
                ['test_enable']
            );
            $this->assertFalse($testData, 'Test data should be cleared when disabled');

            // プラグインをアンインストール
            $this->service->uninstall($plugin);
            $this->assertDirectoryDoesNotExist($pluginDir, 'Plugin directory should be removed after uninstall');

            // テスト用テーブルが削除されているかチェック
            $tables = $connection->createSchemaManager()->listTableNames();
            $this->assertNotContains('plg_install_test_plugin', $tables, 'Test table should be dropped after uninstall');
        } finally {
            // 一時ファイルを削除
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * プラグインのインストール・有効化・無効化・アンインストールの一連の流れをテスト
     */
    public function testPluginLifecycleFromMockServer()
    {
        // Mockサーバが利用可能かチェック
        if (!$this->isMockServerAvailable()) {
            $this->markTestSkipped('Mock server is not available at '.$this->mockServerUrl);
        }

        $pluginUrl = $this->mockServerUrl.'/InstallTestPlugin-1.0.0.tgz';
        $tempFile = $this->downloadPluginFromUrl($pluginUrl);

        try {
            // 1. インストール
            $result = $this->service->install($tempFile);
            $this->assertTrue($result, 'Installation should succeed');

            $plugin = $this->pluginRepository->findOneBy(['code' => 'InstallTestPlugin']);
            $this->assertInstanceOf(Plugin::class, $plugin, 'Plugin should be found after installation');
            $this->assertSame(Constant::DISABLED, (int) $plugin->isEnabled(), 'Plugin should be disabled after installation');

            // 2. 有効化
            $this->service->enable($plugin);
            $this->entityManager->refresh($plugin);
            $this->assertSame(Constant::ENABLED, (int) $plugin->isEnabled(), 'Plugin should be enabled');

            // 3. 無効化
            $this->service->disable($plugin);
            $this->entityManager->refresh($plugin);
            $this->assertSame(Constant::DISABLED, (int) $plugin->isEnabled(), 'Plugin should be disabled');

            // 4. 再有効化
            $this->service->enable($plugin);
            $this->entityManager->refresh($plugin);
            $this->assertSame(Constant::ENABLED, (int) $plugin->isEnabled(), 'Plugin should be enabled again');

            // 5. アンインストール
            $this->service->uninstall($plugin);
            $plugin = $this->pluginRepository->findOneBy(['code' => 'InstallTestPlugin']);
            $this->assertNotInstanceOf(Plugin::class, $plugin, 'Plugin should be removed from database after uninstall');
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * 無効なプラグインURLでのエラーハンドリングをテスト
     */
    public function testInstallPluginWithInvalidUrl()
    {
        $invalidUrl = $this->mockServerUrl.'/NonExistentPlugin-1.0.0.tgz';

        $this->expectException(\Exception::class);
        $this->downloadPluginFromUrl($invalidUrl);
    }

    /**
     * Mockサーバが利用可能かチェック
     */
    private function isMockServerAvailable(): bool
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'method' => 'HEAD',
            ],
        ]);

        $headers = @get_headers($this->mockServerUrl, true, $context);

        return $headers !== false && str_contains((string) $headers[0], '200');
    }

    /**
     * URLからプラグインをダウンロード
     */
    private function downloadPluginFromUrl(string $url): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'plugin_test_');

        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'method' => 'GET',
            ],
        ]);

        $content = @file_get_contents($url, false, $context);
        if ($content === false) {
            throw new \Exception("Failed to download plugin from URL: $url");
        }

        file_put_contents($tempFile, $content);

        return $tempFile;
    }
}
