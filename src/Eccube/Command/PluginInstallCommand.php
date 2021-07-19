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

    protected function configure()
    {
        $this
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'path of tar or zip')
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code')
            ->setDescription('Install plugin from local.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $path = $input->getOption('path');
        $code = $input->getOption('code');

        // アーカイブからインストール
        if ($path) {
            if ($this->pluginService->install($path)) {
                $io->success('Installed.');

                return 0;
            }
        }

        // 設置済ファイルからインストール
        if ($code) {
            $this->pluginService->installWithCode($code);
            $this->clearCache($io);
            $io->success('Installed.');

            return 0;
        }

        $io->error('path or code is required.');

        return 1;
    }
}
