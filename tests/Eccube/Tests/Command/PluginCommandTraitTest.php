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

namespace Eccube\Tests\Command;

use Eccube\Command\PluginCommandTrait;
use Eccube\Command\PluginEnableCommand;
use Eccube\Common\EccubeConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * キャッシュ削除に失敗したときに, 成功として扱われないことを検証する.
 *
 * bin/console が存在しないディレクトリを cwd に渡して失敗を再現する.
 */
final class PluginCommandTraitTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $createdDirs = [];

    protected function tearDown(): void
    {
        foreach ($this->createdDirs as $dir) {
            (new Filesystem())->remove($dir);
        }
        $this->createdDirs = [];
        parent::tearDown();
    }

    public function testClearCacheReturnsFalseAndGuidesManualOperation(): void
    {
        $tester = new CommandTester($this->createProbeCommand(sys_get_temp_dir()));

        $this->assertSame(PluginEnableCommand::EXIT_MANUAL_ACTION_REQUIRED, $tester->execute([], ['decorated' => false]));
        $this->assertStringContainsString('bin/console cache:clear --no-warmup', $tester->getDisplay());
    }

    /**
     * cache:clear の後に eccube:cache:build まで実行する.
     *
     * cache:clear --no-warmup はコンパイル済みコンテナを消すだけで作り直さない.
     * 再生成しないと, build ディレクトリへ書けない Web サーバーは次のリクエストで 500 になる.
     */
    public function testClearCacheAlsoRebuildsBuildDir(): void
    {
        $projectDir = $this->createFakeConsole(['cache:clear' => 0, 'eccube:cache:build' => 0]);
        $tester = new CommandTester($this->createProbeCommand($projectDir));

        $this->assertSame(0, $tester->execute([], ['decorated' => false]));

        $display = $tester->getDisplay();
        $this->assertStringContainsString('bin/console cache:clear --no-warmup', $display);
        $this->assertStringContainsString('bin/console eccube:cache:build --no-twig', $display);
    }

    /**
     * 再生成に失敗した場合は成功として扱わない.
     */
    public function testClearCacheReportsFailureOfRebuild(): void
    {
        $projectDir = $this->createFakeConsole(['cache:clear' => 0, 'eccube:cache:build' => 1]);
        $tester = new CommandTester($this->createProbeCommand($projectDir));

        $this->assertSame(PluginEnableCommand::EXIT_MANUAL_ACTION_REQUIRED, $tester->execute([], ['decorated' => false]));
        $this->assertStringContainsString('bin/console eccube:cache:build --no-twig', $tester->getDisplay());
    }

    /**
     * サブコマンドごとに終了コードを決める偽の bin/console を作る.
     *
     * 実際のビルドはメモリと時間を要し, 環境によって結果が変わるため, ここでは
     * 「どのコマンドを, どの順で実行するか」だけを検証する.
     *
     * @param array<string, int> $exitCodes サブコマンド名 => 終了コード
     *
     * @return string プロジェクトルートとして渡すディレクトリ
     */
    private function createFakeConsole(array $exitCodes): string
    {
        $projectDir = sys_get_temp_dir().'/eccube-plugin-cmd-'.bin2hex(random_bytes(6));
        mkdir($projectDir.'/bin', 0777, true);
        $this->createdDirs[] = $projectDir;

        $cases = '';
        foreach ($exitCodes as $name => $code) {
            $cases .= sprintf("  %s) exit %d ;;\n", $name, $code);
        }

        file_put_contents($projectDir.'/bin/console', "#!/bin/sh\ncase \"$1\" in\n".$cases."  *) exit 127 ;;\nesac\n");
        chmod($projectDir.'/bin/console', 0755);

        return $projectDir;
    }

    public function testManualActionExitCodeDoesNotCollideWithSymfonyReservedCodes(): void
    {
        // 定数はトレイトに定義しているため, 使用側のコマンドクラス経由で参照する
        $this->assertSame(3, PluginEnableCommand::EXIT_MANUAL_ACTION_REQUIRED);
        $this->assertNotSame(Command::SUCCESS, PluginEnableCommand::EXIT_MANUAL_ACTION_REQUIRED);
        $this->assertNotSame(Command::FAILURE, PluginEnableCommand::EXIT_MANUAL_ACTION_REQUIRED);
        $this->assertNotSame(Command::INVALID, PluginEnableCommand::EXIT_MANUAL_ACTION_REQUIRED);
    }

    private function createProbeCommand(string $projectDir): Command
    {
        $eccubeConfig = $this->createMock(EccubeConfig::class);
        $eccubeConfig->method('get')->with('kernel.project_dir')->willReturn($projectDir);

        $command = new class('eccube:tests:clear-cache') extends Command {
            use PluginCommandTrait;

            #[\Override]
            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                return $this->clearCache(new SymfonyStyle($input, $output)) ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
            }
        };
        $command->setEccubeConfig($eccubeConfig);

        return $command;
    }
}
