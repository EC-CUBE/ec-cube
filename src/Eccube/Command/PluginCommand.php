<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Command\GeneratorCommand\EntityFromDbGenerator;
use Eccube\Command\GeneratorCommand\EntityFromYamlGenerator;
use Eccube\Command\GeneratorCommand\PluginGenerator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Constraints as Assert;

class PluginCommand extends \Knp\Command\Command
{

    protected $app;


    protected function configure()
    {
        $this
            ->setName('plugin:develop')
            ->addArgument('mode', InputArgument::REQUIRED, 'install/uninstall/enable/disable/update/generate/entity', null)
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'path of tar or zip')
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code')
            ->addOption('uninstall-force', null, InputOption::VALUE_OPTIONAL, 'if set true, remove directory')
            ->setDescription('plugin commandline installer.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> plugin installer runner for developer,

  <info>php %command.full_name% [install/uninstall/enable/disable/update/generate/entity]</info>

Usage:

ex1) The command install plugin from tar or zip.
  <info>php %command.full_name% install --path[=PATH]</info>

ex2) The command uninstall plugin.
  <info>php %command.full_name% uninstall --code[=CODE] --uninstall-force[=UNINSTALL-FORCE]</info>
  if [--uninstall-force] set true, remove directory.

ex3) The command enable plugin.
  <info>php %command.full_name% enable --code[=CODE]</info>

ex4) The command disable plugin.
  <info>php %command.full_name% disable --code[=CODE]</info>

ex5) The command update plugin.
  <info>php %command.full_name% update --code[=CODE]</info>

ex6) The command generate plugin.
  <info>php %command.full_name% generate</info>
  create plugin skeleton.

ex7) The command entity plugin.
  <info>php %command.full_name% entity</info>
  create Entity, Repository, Migration.

EOF
            );
    }

    protected function getPluginFromCode($pluginCode)
    {
        return $this->app['eccube.repository.plugin']->findOneBy(array('del_flg' => 0, 'code' => $pluginCode));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getSilexApplication();
        $this->app->initialize();
        $this->app->boot();

        $mode = $input->getArgument('mode');

        // プラグイン作成
        if ($mode == 'generate') {
            $PluginGenerator = new PluginGenerator($this->app);
            $PluginGenerator->init($this->getHelper('question'), $input, $output);
            $PluginGenerator->run();

            return;
        }
        // プラグインEntity用作成
        if ($mode == 'entity') {
            $output->writeln('');
            $Question = new Question('<comment>[entity]How to generate entities from db schema or yml? [d => db, y => yml] : </comment>', '');
            $QuestionHelper = $this->getHelper('question');
            $value = $QuestionHelper->ask($input, $output, $Question);
            $value = substr(strtolower(trim($value)), 0, 1);
            if ($value == 'd') {
                $PluginEntityGenerator = new EntityFromDbGenerator($this->app);
                $PluginEntityGenerator->init($QuestionHelper, $input, $output);
                $PluginEntityGenerator->run();
            } elseif ($value == 'y') {
                $PluginEntityGenerator = new EntityFromYamlGenerator($this->app);
                $PluginEntityGenerator->init($QuestionHelper, $input, $output);
                $PluginEntityGenerator->run();
            } else {
                // 入力値正しくない
                $output->writeln('Input value is incorrect, please choose [d] for database schema or [y] for yml file.');
            }

            return;
        }
        $path = $input->getOption('path');
        $code = $input->getOption('code');
        $uninstallForce = $input->getOption('uninstall-force');
        $service = $this->app['eccube.service.plugin'];

        if ($mode == 'install') {
            // アーカイブからインストール
            if ($path) {
                if ($service->install($path)) {
                    $output->writeln('success');

                    return;
                }
            }
            // 設置済ファイルからインストール
            if ($code) {
                $pluginDir = $service->calcPluginDir($code);
                $service->checkPluginArchiveContent($pluginDir);
                $config = $service->readYml($pluginDir.'/config.yml');
                $event = $service->readYml($pluginDir.'/event.yml');
                $service->checkSamePlugin($config['code']);
                $service->registerPlugin($config, $event);

                $output->writeln('success');

                return;
            }

            $output->writeln('path or code is required.');

            return;
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

        if ($mode == 'uninstall') {
            if (empty($code)) {
                $output->writeln('code is required.');

                return;
            }

            $plugin = $this->getPluginFromCode($code);

            // ディレクトリも含め全て削除.
            if ($uninstallForce) {
                if ($service->uninstall($plugin)) {
                    $output->writeln('success');

                    return;
                }

                return;
            }

            // ディレクトリは残し, プラグインを削除.
            $pluginDir = $service->calcPluginDir($code);
            $config = $service->readYml($pluginDir.'/config.yml');
            $service->callPluginManagerMethod($config, 'disable');
            $service->callPluginManagerMethod($config, 'uninstall');
            $service->unregisterPlugin($plugin);

            $output->writeln('success');

            return;
        }

        if (in_array($mode, array('enable', 'disable'), true)) {
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

        $output->writeln(' mode is not correct, try help for more options');
        $output->writeln(' plugin:develop --help  ');
    }

}
