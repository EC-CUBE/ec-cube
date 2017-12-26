<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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

    protected function configure()
    {
        $this->setName('eccube:plugin:install')
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
                return;
            }
        }

        // 設置済ファイルからインストール
        if ($code) {
            $pluginDir = $this->pluginService->calcPluginDir($code);
            $this->pluginService->checkPluginArchiveContent($pluginDir);
            $config = $this->pluginService->readYml($pluginDir.'/config.yml');
            $event = $this->pluginService->readYml($pluginDir.'/event.yml');
            $this->pluginService->checkSamePlugin($config['code']);
            $this->pluginService->postInstall($config, $event, false);

            $this->clearCache($io);
            $io->success('Installed.');

            return;
        }

        $io->error('path or code is required.');
    }
}