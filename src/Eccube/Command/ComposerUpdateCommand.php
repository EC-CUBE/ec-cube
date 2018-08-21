<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
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


use Eccube\Service\Composer\ComposerApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerUpdateCommand extends Command
{
    protected static $defaultName = 'eccube:composer:update';

    /**
     * @var ComposerApiService
     */
    private $composerService;

    public function __construct(ComposerApiService $composerService)
    {
        parent::__construct();
        $this->composerService = $composerService;
    }

    protected function configure()
    {
        $this->addOption('dry-run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->composerService->execUpdate($input->getOption('dry-run'), $output);
    }
}