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

#[AsCommand(name: 'eccube:plugin:enable')]
class PluginEnableCommand extends Command
{
    use PluginCommandTrait;

    /**
     * @return void
     */
    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $code = $input->getOption('code');

        if (empty($code)) {
            $io->error('code is required.');

            return 1;
        }

        $plugin = $this->pluginRepository->findByCode($code);
        if (is_null($plugin)) {
            $io->error("Plugin `$code` is not found.");

            return 1;
        }

        if (!$plugin->isInitialized()) {
            $this->pluginService->installWithCode($plugin->getCode());
        }

        $this->pluginService->enable($plugin);
        $this->clearCache($io);

        $io->success('Plugin Enabled.');

        return 0;
    }
}
