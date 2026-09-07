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
use Eccube\Service\Permission\UserIdentity;
use Eccube\Service\Permission\WebServerUserResolver;
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
    private const CONTAINER_CLASS = 'Eccube_KernelProdContainer';

    private string $runtimeDir;

    private string $buildDir;

    protected function setUp(): void
    {
        parent::setUp();
        $base = sys_get_temp_dir().'/eccube-pool-listener-'.bin2hex(random_bytes(6));
        $this->runtimeDir = $base.'/runtime';
        $this->buildDir = $base.'/build';
        mkdir($this->runtimeDir, 0755, true);
        mkdir($this->buildDir, 0755, true);
        // 既定はコンパイル済みコンテナがある状態 (cache:clear を warmup 込みで実行した後)
        file_put_contents($this->buildDir.'/'.self::CONTAINER_CLASS.'.php', '<?php');
    }

    protected function tearDown(): void
    {
        if (is_dir($this->runtimeDir)) {
            chmod($this->runtimeDir, 0755);
        }
        (new Filesystem())->remove(\dirname($this->runtimeDir));
        parent::tearDown();
    }

    /**
     * --no-warmup を付けるとビルドディレクトリからコンパイル済みコンテナが消える.
     *
     * 権限を分離した構成では Web サーバーが作り直せず 500 になる. 復旧できるのは
     * CLI ユーザーの eccube:cache:build だけなので, cache pool の案内より先に伝える.
     */
    public function testWarnsWhenWebServerCannotRebuildContainer(): void
    {
        unlink($this->buildDir.'/'.self::CONTAINER_CLASS.'.php');

        $output = $this->dispatch('cache:clear', 0, $this->foreignUser());

        $this->assertStringContainsString($this->buildDir, $output['display']);
        $this->assertStringContainsString('eccube:cache:build', $output['display']);
    }

    /**
     * 権限を分離していない構成では Web サーバー自身が作り直せるため案内しない.
     */
    public function testStaysSilentWhenWebServerCanRebuildContainer(): void
    {
        unlink($this->buildDir.'/'.self::CONTAINER_CLASS.'.php');

        $output = $this->dispatch('cache:clear', 0, $this->currentUser());

        $this->assertStringNotContainsString('eccube:cache:build', $output['display']);
    }

    /**
     * Web サーバーの実行ユーザーを特定できない場合は, 誤った警告を出さない.
     */
    public function testStaysSilentWhenWebServerUserIsUnknown(): void
    {
        unlink($this->buildDir.'/'.self::CONTAINER_CLASS.'.php');

        $output = $this->dispatch('cache:clear', 0);

        $this->assertStringNotContainsString('eccube:cache:build', $output['display']);
    }

    public function testStaysSilentWhenCompiledContainerExists(): void
    {
        $output = $this->dispatch('cache:clear', 0, $this->foreignUser());

        $this->assertStringNotContainsString('eccube:cache:build', $output['display']);
    }

    /**
     * コンテナが無い場合は, ビルドの案内を cache pool の案内より先に出す.
     */
    public function testContainerWarningComesFirst(): void
    {
        if (0 === getmyuid()) {
            self::markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }

        unlink($this->buildDir.'/'.self::CONTAINER_CLASS.'.php');
        mkdir($this->runtimeDir.'/pools/system', 0755, true);
        chmod($this->runtimeDir, 0555);

        $display = $this->dispatch('cache:clear', 0, $this->foreignUser())['display'];

        $this->assertLessThan(
            strpos($display, 'cache:pool:clear'),
            strpos($display, 'eccube:cache:build'),
            'Web サーバーを復旧させる操作を先に案内する'
        );
    }

    /**
     * ビルドディレクトリを所有せず, other にも書き込み権が無いユーザー.
     */
    private function foreignUser(): UserIdentity
    {
        return new UserIdentity(fileowner($this->buildDir) + 4242, filegroup($this->buildDir) + 4242, 'test');
    }

    private function currentUser(): UserIdentity
    {
        return new UserIdentity((int) fileowner($this->buildDir), (int) filegroup($this->buildDir), 'test');
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
    private function dispatch(string $commandName, int $exitCode, ?UserIdentity $webServerUser = null): array
    {
        $clearer = new RuntimeCachePoolClearer($this->runtimeDir);
        $clearer->clear($this->runtimeDir);

        $resolver = $this->createMock(WebServerUserResolver::class);
        $resolver->method('resolve')->willReturn($webServerUser);

        $output = new BufferedOutput();
        $event = new ConsoleTerminateEvent(new Command($commandName), new ArrayInput([]), $output, $exitCode);

        (new RuntimeCachePoolClearListener($clearer, $resolver, $this->buildDir, $this->buildDir, self::CONTAINER_CLASS))
            ->onConsoleTerminate($event);

        return ['exitCode' => $event->getExitCode(), 'display' => $output->fetch()];
    }
}
