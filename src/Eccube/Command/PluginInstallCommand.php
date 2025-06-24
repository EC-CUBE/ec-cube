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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginInstallCommand extends Command
{
    use PluginCommandTrait;
    protected static $defaultName = 'eccube:plugin:install';

    protected function configure(): void
    {
        $this
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'path of tar or zip')
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code')
            ->addOption('if-not-exists', null, InputOption::VALUE_NONE, 'If plugin is already installed, skip install.')
            ->setDescription('Install plugin from local.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = $input->getOption('path');
        $code = $input->getOption('code');
        $ifNotExists = $input->getOption('if-not-exists');

        // アーカイブからインストール
        if ($path) {
            if ($this->pluginService->install($path, $ifNotExists)) {
                $io->success('Installed.');

                return Command::SUCCESS;
            }
        }

        // 設置済ファイルからインストール
        if ($code) {
            $this->pluginService->installWithCode($code, $ifNotExists);
            $this->clearCache($io);
            $io->success('Installed.');

            return Command::SUCCESS;
        }

        $io->error('path or code is required.');

        return Command::FAILURE;
    }
}
