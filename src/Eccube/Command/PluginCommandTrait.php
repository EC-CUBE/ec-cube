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
                '--no-warmup' => true
            ]), $io);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }
    }
}