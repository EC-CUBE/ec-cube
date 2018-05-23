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

use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;

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
        try {
            /* @var Command $command */
            $command = $this->getApplication()->get('cache:clear');
            $command->run(new ArrayInput([
                'command' => 'cache:clear',
                '--no-warmup' => true,
            ]), $io);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }
    }
}
