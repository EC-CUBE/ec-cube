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

use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

trait PluginCommandTrait
{
    /**
     * @var PluginService
     */
    protected $pluginService;

    /**
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * @param PluginService $pluginService
     * @required
     */
    public function setPluginService(PluginService $pluginService)
    {
        $this->pluginService = $pluginService;
    }

    /**
     * @param PluginRepository $pluginRepository
     * @required
     */
    public function setPluginRepository(PluginRepository $pluginRepository)
    {
        $this->pluginRepository = $pluginRepository;
    }

    protected function clearCache(SymfonyStyle $io)
    {
        $command = ['bin/console', 'cache:clear', '--no-warmup'];
        try {
            $io->text(sprintf('<info>Run %s</info>...', implode(' ', $command)));
            $process = new Process($command);
            $process->mustRun();
            $io->text($process->getOutput());
        } catch (ProcessFailedException $e) {
            $io->error($e->getMessage());
        }
    }
}
