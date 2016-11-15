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

use Knp\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ConfigCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('debug:config')
            ->setDefinition(array(
                new InputArgument('filter', InputArgument::OPTIONAL, 'Show details for all config matching this filter'),
                new InputOption('configphp', null, InputOption::VALUE_NONE, 'Check if you are using Config PHP File'),
            ))
            ->setDescription('Shows a list of config file')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command outputs a list of config file,
Output can be filtered with an optional argument.

  <info>php %command.full_name%</info>

The command lists all config.

  <info>php %command.full_name% database</info>

The command lists the database yml file.
For filter, yml file name or key name can be set.

  <info>php %command.full_name% --configphp</info>

The command checks whether Config PHP File is used.

EOF
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var \Eccube\Application $app */
        $app = $this->getSilexApplication();

        $filter = $input->getArgument('filter');

        $optional = $input->getOption('configphp');
        if ($optional) {
            // ymlファイルではなく、phpファイルが有効になっているかチェック
            $ymlPath = $this->getProjectDirectory().'/app/config/eccube';
            $config_php = $ymlPath.'/config.php';
            if (file_exists($config_php)) {
                $output->writeln('Config PHP File : <info>used.</info>');
            } else {
                $output->writeln('Config PHP File : <info>not used.</info>');
            }

            if (!$filter) {
                return;
            }
        }

        $recursive = function ($config, $space = '    ') use (&$recursive, $output) {
            foreach ($config as $key => $item) {
                if (is_array($item)) {
                    $space = '    ';
                    $output->writeln($space."<comment>{$key}</comment> :");
                    $space .= '    ';

                    $recursive($item, $space);
                } else {
                    $output->writeln($space."<comment>{$key}</comment> : <info>{$item}</info>");
                }
            }
        };


        if ($filter) {
            // コマンド実行時にパラメータを指定

            $config = array();
            $app->parseConfig($filter, $config);

            if (!empty($config)) {
                // ymlファイル名が指定された場合、ymlファイルの内容を出力
                $output->writeln("YML File Name : <info>{$filter}</info>");
                foreach ($config as $key => $item) {
                    if (is_array($item)) {
                        $output->writeln("<comment>{$key}</comment> :");
                        $recursive($item);
                    } else {
                        $output->writeln("<comment>{$key}</comment> : <info>{$item}</info>");
                    }
                }

                return;
            }

            if (!isset($app['config'][$filter])) {
                $output->writeln('Not Found filter : $app[\'config\'][\'<error>'.$filter.'</error>\']');

                return;
            }

            $config = $app['config'][$filter];

            $output->writeln('$app[\'config\'][\'<comment>'.$filter.'</comment>\']');
            if (is_array($config)) {
                foreach ($config as $key => $item) {
                    if (is_array($item)) {
                        $output->writeln("<comment>{$key}</comment> :");
                        $recursive($item);
                    } else {
                        $output->writeln("<comment>{$key}</comment> : <info>{$item}</info>");
                    }
                }
            } else {
                $output->writeln("<comment>{$filter}</comment> : <info>{$config}</info>");
            }
        } else {
            // $app['config']の内容を全て出力する
            $config = $app['config'];

            foreach ($config as $key => $item) {
                if (is_array($item)) {
                    $output->writeln("<comment>{$key}</comment> :");
                    $recursive($item);
                } else {
                    $output->writeln("<comment>{$key}</comment> : <info>{$item}</info>");
                }
            }
        }
    }
}
