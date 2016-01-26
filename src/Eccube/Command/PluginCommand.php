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
    protected $pluginPath;

    public function __construct(\Eccube\Application $app, $name = null)
    {
        parent::__construct($name);
        $this->app = $app;
        $this->pluginPath = $app['config']['plugin_realdir'].DIRECTORY_SEPARATOR;
    }

    protected function configure()
    {
        $this
            ->setName('plugin:develop')
            ->addArgument('mode', InputArgument::REQUIRED, 'mode(install/uninstall/enable/disable/update/reload)', null)
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'path of tar or zip')
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code')
            ->addOption('only-db', null, InputOption::VALUE_OPTIONAL, 'plugin code')
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
        $onlyDbFlg = $input->getOption('only-db');

        $service = $this->app['eccube.service.plugin'];
        if ($mode == 'install') {
            // 解凍なしのインストール ( 設置プラグイン用 )
            if (!is_null($onlyDbFlg)) {
                if ($service->installOnlyDb($this->pluginPath.$onlyDbFlg)) {
                    $output->writeln('success');
                    return;
                }
            }
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
        if ($mode == 'reload') {
            if (empty($code)) {
                $output->writeln('code is required.');
                return;
            }
            $stepFlg = false;
            if ($service->uninstallOnlyDb($this->pluginPath.$code)) {
                $stepFlg = true;
            }
            if ($stepFlg) {
                if ($service->installOnlyDb($this->pluginPath.$code)) {
                    $output->writeln('success');
                    return;
                }
            }
        }

        if (in_array($mode, array('enable', 'disable', 'uninstall'), true)) {
            // 解凍なしのアンインストール ( 設置プラグイン用 )
            if (!is_null($onlyDbFlg)) {
                if ($mode == 'uninstall') {
                    $plugin = $this->getPluginFromCode($onlyDbFlg);
                    if ($service->uninstallOnlyDb($plugin)) {
                        $output->writeln('success');

                        return;
                    }
                }
                $output->writeln('entered only-db option, this option not has this mode.');
                return;
            }

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
