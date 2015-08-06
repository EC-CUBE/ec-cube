<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class PluginCommand extends \Knp\Command\Command
{

    protected $app;

    public function __construct(\Eccube\Application $app, $name = null) 
    {
        parent::__construct($name);
        $this->app = $app;
    }

    protected function configure() 
    {
        $this
            ->setName('plugin:develop')
            ->addArgument('mode', InputArgument::REQUIRED, 'mode(install/uninstall/enable/disable/update)', null) 
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'path of tar or zip') 
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code') 
            ->setDescription('plugin commandline installer.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> plugin installer runner for developer;
EOF
            );
    }


    protected function getPluginFromCode($pluginCode) 
    {
        return $this->app['eccube.repository.plugin']->findOneBy(array('del_flg'=>0, 'code'=>$pluginCode));
    }

    protected function execute(InputInterface $input, OutputInterface $output) 
    {
        $this->app->initialize();
        $this->app->boot();

        $mode = $input->getArgument('mode');
        $path = $input->getOption('path');
        $code = $input->getOption('code');

        $service = $this->app['eccube.service.plugin'];

        if ($mode == 'install') {
            if (empty($path)) {
                $output->writeln('path is required.');
                return;
            }
            if ($service->install($path)) {
                $output->writeln('success');
                return;
            }
        }
        if ($mode == 'update') {
            if (empty($code)) {
                $output->writeln('code is required.');
                return;
            }
            if (empty($path)) {
                $output->writeln('path is required.');
                return;
            }
            $plugin = $this->getPluginFromCode($code);
            if ($service->update($plugin, $path)) {
                $output->writeln('success');
                return;
            }
        }
        if (in_array($mode, array('enable', 'disable', 'uninstall'), true)) {
            if (empty($code)) {
                $output->writeln('code is required.');
                return;
            }

            $plugin = $this->getPluginFromCode($code);
            if ($service->$mode($plugin)) {
                $output->writeln('success');
                return;
            }
        }
        $output->writeln('undefined mode.');
    }
}
