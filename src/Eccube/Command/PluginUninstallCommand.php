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

class PluginUninstallCommand extends Command
{
    use PluginCommandTrait;
    protected static $defaultName = 'eccube:plugin:uninstall';

    protected function configure()
    {
        $this
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code')
            ->addOption('uninstall-force', null, InputOption::VALUE_OPTIONAL, 'if set true, remove directory')
            ->setDescription('Uninstall plugin.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $code = $input->getOption('code');
        $uninstallForce = $input->getOption('uninstall-force');

        if (empty($code)) {
            $io->error('code is required.');

            return 1;
        }

        $plugin = $this->pluginRepository->findByCode($code);
        if (is_null($plugin)) {
            $io->error("Plugin `$code` is not installed.");

            return 1;
        }

        $this->pluginService->uninstall($plugin, $uninstallForce);
        $this->clearCache($io);

        $io->success('Uninstalled.');

        return 0;
    }
}
