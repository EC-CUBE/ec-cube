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
use Symfony\Contracts\Service\Attribute\Required;

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
     *
     * @return void
     */
    #[Required]
    public function setPluginService(PluginService $pluginService): void
    {
        $this->pluginService = $pluginService;
    }

    /**
     * @param PluginRepository $pluginRepository
     *
     * @return void
     */
    #[Required]
    public function setPluginRepository(PluginRepository $pluginRepository): void
    {
        $this->pluginRepository = $pluginRepository;
    }

    /**
     * @param SymfonyStyle $io
     *
     * @return void
     */
    protected function clearCache(SymfonyStyle $io): void
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
