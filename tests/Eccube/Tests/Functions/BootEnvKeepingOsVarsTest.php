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

namespace Eccube\Tests\Functions;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * OS 環境変数 APP_ENV が設定済みの環境での .env の読み込みを検証する.
 *
 * テストのプロセスは既に環境変数を読み込んでいるため, OS 環境変数を指定した別プロセスで読み込ませる.
 */
final class BootEnvKeepingOsVarsTest extends TestCase
{
    private string $workDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workDir = sys_get_temp_dir().'/eccube_boot_env_'.bin2hex(random_bytes(4));
        mkdir($this->workDir);
        file_put_contents($this->workDir.'/.env', implode("\n", [
            'APP_ENV=prod',
            'APP_DEBUG=0',
            'ECCUBE_ADMIN_ROUTE=newadmin',
            'DATABASE_URL=sqlite:///var/eccube.db',
            '',
        ]));
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->workDir);
        parent::tearDown();
    }

    /**
     * .env にだけある値は, OS 環境変数 APP_ENV があっても読み込まれる.
     */
    public function testLoadsDotenvOnlyVars(): void
    {
        $vars = $this->boot(['APP_ENV' => 'prod'], false);

        $this->assertSame('prod', $vars['APP_ENV']);
        $this->assertSame('newadmin', $vars['ECCUBE_ADMIN_ROUTE']);
        $this->assertSame('sqlite:///var/eccube.db', $vars['DATABASE_URL']);
    }

    /**
     * OS 環境変数の値は .env で上書きしない.
     */
    public function testKeepsOsVars(): void
    {
        $vars = $this->boot(['APP_ENV' => 'prod', 'ECCUBE_ADMIN_ROUTE' => 'fixedadmin'], true);

        $this->assertSame('fixedadmin', $vars['ECCUBE_ADMIN_ROUTE']);
    }

    /**
     * bin/console 相当（$loadAppDebug = false）では, OS 環境変数に無い APP_DEBUG を .env から取り込まない.
     * OS 環境変数で env を dev に切り替えたときに, .env（prod）の APP_DEBUG=0 で debug が無効にならない.
     */
    public function testSkipsDotenvAppDebugForConsole(): void
    {
        $vars = $this->boot(['APP_ENV' => 'dev'], false);

        $this->assertSame('dev', $vars['APP_ENV']);
        $this->assertNull($vars['APP_DEBUG']);
        $this->assertNotContains('APP_DEBUG', explode(',', (string) $vars['SYMFONY_DOTENV_VARS']));
        $this->assertSame('newadmin', $vars['ECCUBE_ADMIN_ROUTE']);
    }

    /**
     * bin/console 相当でも, OS 環境変数の APP_DEBUG はそのまま使う.
     */
    public function testKeepsOsAppDebugForConsole(): void
    {
        $vars = $this->boot(['APP_ENV' => 'prod', 'APP_DEBUG' => '1'], false);

        $this->assertSame('1', $vars['APP_DEBUG']);
    }

    /**
     * index.php 相当（$loadAppDebug = true）では, .env の APP_DEBUG を取り込む.
     */
    public function testLoadsDotenvAppDebugForWeb(): void
    {
        $vars = $this->boot(['APP_ENV' => 'prod'], true);

        $this->assertSame('0', $vars['APP_DEBUG']);
    }

    /**
     * @param array<string, string> $osVars
     *
     * @return array<string, string|null>
     */
    private function boot(array $osVars, bool $loadAppDebug): array
    {
        $script = $this->workDir.'/boot.php';
        file_put_contents($script, <<<'PHP'
            <?php
            require $argv[1];
            boot_env_keeping_os_vars($argv[2], $argv[3] === '1');
            $keys = ['APP_ENV', 'APP_DEBUG', 'ECCUBE_ADMIN_ROUTE', 'DATABASE_URL', 'SYMFONY_DOTENV_VARS'];
            echo json_encode(array_combine($keys, array_map(fn ($k) => $_SERVER[$k] ?? null, $keys)));
            PHP);

        $command = ['env', '-i', 'PATH='.getenv('PATH')];
        foreach ($osVars as $key => $value) {
            $command[] = $key.'='.$value;
        }
        array_push(
            $command,
            PHP_BINARY,
            $script,
            dirname(__DIR__, 4).'/vendor/autoload.php',
            $this->workDir.'/.env',
            $loadAppDebug ? '1' : '0',
        );

        $process = new Process($command);
        $process->mustRun();

        return json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
    }
}
