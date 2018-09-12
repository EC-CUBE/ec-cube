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

use Eccube\Entity\Plugin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginSchemaUpdateCommand extends Command
{
    use PluginCommandTrait;

    protected static $defaultName = 'eccube:plugin:schema-update';

    protected function configure()
    {
        $this
            ->addArgument('code', InputArgument::REQUIRED, 'Plugin code')
            ->setDescription('Execute plugin schema update.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $code = $input->getArgument('code');

        /** @var Plugin $Plugin */
        $Plugin = $this->pluginRepository->findByCode($code);
        if (!$Plugin) {
            $io->error("No such plugin `${code}`.");

            return 1;
        }

        $config = $this->pluginService->readConfig($this->pluginService->calcPluginDir($code));
        $this->pluginService->generateProxyAndUpdateSchema($Plugin, $config);
        $this->clearCache($io);

        $io->success('Schema Updated.');
    }
}
