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

namespace Eccube\Tests\EventListener;

use Eccube\EventListener\RuntimeCachePoolClearListener;
use Eccube\Util\RuntimeCachePoolClearer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * cache:clear が実行時 cache pool を削除できなかったことを通知する.
 */
final class RuntimeCachePoolClearListenerTest extends TestCase
{
    private string $runtimeDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runtimeDir = sys_get_temp_dir().'/eccube-pool-listener-'.bin2hex(random_bytes(6));
        mkdir($this->runtimeDir, 0755, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->runtimeDir)) {
            chmod($this->runtimeDir, 0755);
        }
        (new Filesystem())->remove($this->runtimeDir);
        parent::tearDown();
    }

    public function testWarnsWhenPoolsCouldNotBeCleared(): void
    {
        if (0 === getmyuid()) {
            self::markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }

        mkdir($this->runtimeDir.'/pools/system', 0755, true);
        chmod($this->runtimeDir, 0555);

        $output = $this->dispatch('cache:clear', 0);

        $this->assertStringContainsString($this->runtimeDir.'/pools', $output['display']);
        $this->assertStringContainsString('cache:pool:clear', $output['display']);
    }

    /**
     * 終了コードは変更しない.
     *
     * cache:clear は composer.json の auto-scripts から実行されるため, 非ゼロを返すと
     * 分離した構成で composer install がスクリプト失敗として中断してしまう.
     */
    public function testKeepsSuccessExitCodeForComposerScripts(): void
    {
        if (0 === getmyuid()) {
            self::markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }

        mkdir($this->runtimeDir.'/pools/system', 0755, true);
        chmod($this->runtimeDir, 0555);

        $output = $this->dispatch('cache:clear', 0);

        $this->assertSame(0, $output['exitCode']);
    }

    public function testKeepsSuccessWhenPoolsAreCleared(): void
    {
        mkdir($this->runtimeDir.'/pools/system', 0755, true);

        $output = $this->dispatch('cache:clear', 0);

        $this->assertSame(0, $output['exitCode']);
        $this->assertSame('', trim($output['display']));
    }

    public function testIgnoresOtherCommands(): void
    {
        if (0 === getmyuid()) {
            self::markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }

        mkdir($this->runtimeDir.'/pools/system', 0755, true);
        chmod($this->runtimeDir, 0555);

        $output = $this->dispatch('cache:warmup', 0);

        $this->assertSame(0, $output['exitCode']);
        $this->assertSame('', trim($output['display']));
    }

    /**
     * 本処理が失敗している場合は, そちらのエラーを埋もれさせないため何も表示しない.
     */
    public function testStaysSilentWhenCommandAlreadyFailed(): void
    {
        if (0 === getmyuid()) {
            self::markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }

        mkdir($this->runtimeDir.'/pools/system', 0755, true);
        chmod($this->runtimeDir, 0555);

        $output = $this->dispatch('cache:clear', 1);

        $this->assertSame(1, $output['exitCode']);
        $this->assertSame('', trim($output['display']));
    }

    /**
     * @return array{exitCode: int, display: string}
     */
    private function dispatch(string $commandName, int $exitCode): array
    {
        $clearer = new RuntimeCachePoolClearer($this->runtimeDir);
        $clearer->clear($this->runtimeDir);

        $output = new BufferedOutput();
        $event = new ConsoleTerminateEvent(new Command($commandName), new ArrayInput([]), $output, $exitCode);

        (new RuntimeCachePoolClearListener($clearer))->onConsoleTerminate($event);

        return ['exitCode' => $event->getExitCode(), 'display' => $output->fetch()];
    }
}
