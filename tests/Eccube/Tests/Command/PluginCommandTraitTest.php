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

/**
 * キャッシュ削除に失敗したときに, 成功として扱われないことを検証する.
 *
 * bin/console が存在しないディレクトリを cwd に渡して失敗を再現する.
 */
final class PluginCommandTraitTest extends TestCase
{
    public function testClearCacheReturnsFalseAndGuidesManualOperation(): void
    {
        $tester = new CommandTester($this->createProbeCommand(sys_get_temp_dir()));

        $this->assertSame(PluginEnableCommand::EXIT_MANUAL_ACTION_REQUIRED, $tester->execute([], ['decorated' => false]));
        $this->assertStringContainsString('bin/console cache:clear --no-warmup', $tester->getDisplay());
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
