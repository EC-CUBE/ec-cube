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

namespace Eccube\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'eccube:plugin:install', description: 'Install plugin from local.')]
class PluginInstallCommand extends Command
{
    use PluginCommandTrait;

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'path of tar or zip')
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code')
            ->addOption('if-not-exists', null, InputOption::VALUE_NONE, 'If plugin is already installed, skip install.');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = $input->getOption('path');
        $code = $input->getOption('code');
        $ifNotExists = $input->getOption('if-not-exists');

        // アーカイブからインストール
        if ($path) {
            // PluginService::install() は install(string $path, int $source = 0, bool $notExists = false)
            // のため、$source を省略すると $ifNotExists が $source に入り --if-not-exists が効かない。
            // $source は管理画面のアーカイブアップロード (PluginController::install()) と同じ 0 を渡す。
            // install() は成功時に true を返すか例外を投げるため、戻り値では分岐しない。
            $this->pluginService->install($path, 0, $ifNotExists);
            $cacheCleared = $this->clearCache($io);
            $io->success('Installed.');

            return $cacheCleared ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
        }

        // 設置済ファイルからインストール
        if ($code) {
            $this->pluginService->installWithCode($code, $ifNotExists);
            $cacheCleared = $this->clearCache($io);
            $io->success('Installed.');

            return $cacheCleared ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
        }

        $io->error('path or code is required.');

        return 1;
    }
}
