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

use Eccube\Entity\Plugin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'eccube:plugin:update', description: 'Execute plugin update process.')]
class PluginUpdateCommand extends Command
{
    use PluginCommandTrait;

    /**
     * @return void
     */
    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('code', InputArgument::REQUIRED, 'Plugin code');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $code = $input->getArgument('code');

        /** @var Plugin|null $Plugin */
        $Plugin = $this->pluginRepository->findByCode($code);

        if (!$Plugin) {
            $io->error("No such plugin `{$code}`.");

            return 1;
        }

        $config = $this->pluginService->readConfig($this->pluginService->calcPluginDir($code));
        $this->pluginService->updatePlugin($Plugin, $config);
        $this->clearCache($io);

        $io->success('Updated.');

        return 0;
    }
}
