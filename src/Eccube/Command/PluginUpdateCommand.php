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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Plugin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginUpdateCommand extends Command
{
    protected static $defaultName = 'eccube:plugin:update';

    use PluginCommandTrait;

    private $pluginRealDir;

    /**
     * PluginUpdateCommand constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        parent::__construct();
        $this->pluginRealDir = $eccubeConfig['plugin_realdir'];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var Plugin $Plugin */
        $Plugin = $this->pluginRepository->findByCode('Emperor');
        $config = $this->pluginService->readConfig($this->pluginRealDir.DIRECTORY_SEPARATOR.$Plugin->getCode());
        $this->pluginService->updatePlugin($Plugin, $config);
        $this->clearCache($io);

        $io->success('Updated.');
    }
}
