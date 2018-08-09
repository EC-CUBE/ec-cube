<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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

class PluginEnableCommand extends Command
{
    protected static $defaultName = 'eccube:plugin:enable';

    use PluginCommandTrait;

    protected function configure()
    {
        $this
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $code = $input->getOption('code');

        if (empty($code)) {
            $io->error('code is required.');

            return;
        }

        $plugin = $this->pluginRepository->findByCode($code);
        if (is_null($plugin)) {
            $io->error("Plugin `$code` is not found.");

            return;
        }

        $this->pluginService->enable($plugin);
        $this->clearCache($io);

        $io->success('Plugin Enabled.');
    }
}
