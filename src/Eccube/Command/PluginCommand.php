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

use Eccube\Exception\PluginException;
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
            ->addArgument('mode', InputArgument::REQUIRED, 'mode(install/uninstall/enable/disable/update)', null)
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'path of tar or zip')
            ->addOption('code', null, InputOption::VALUE_OPTIONAL, 'plugin code')
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, '0 or 1 (All Delete)')
            ->setDescription('plugin commandline installer.')
            ->setHelp(
                <<<EOF
                The <info>%command.name%</info> plugin installer runner for developer;
EOF
            );
    }


    protected function getPluginFromCode($pluginCode)
    {
        return $this->app['eccube.repository.plugin']->findOneBy(array('del_flg' => 0, 'code' => $pluginCode));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app->initialize();
        $this->app->boot();

        $mode = $input->getArgument('mode');
        $path = $input->getOption('path');
        $code = $input->getOption('code');
        $force = $input->getOption('force');

        $service = $this->app['eccube.service.plugin'];

        if ($mode == 'install') {
            // パスもしくはプラグインコードがない場合
            if (empty($path) && empty($code)) {
                $output->writeln('path or code is required.');

                return;

            }

            if (!empty($path)) {
                if ($service->install($path)) {
                    $output->writeln('success');

                    return;
                }
            }

            if (!empty($code)) {
                if ($this->installUnCompressPlugin($this->pluginPath.$code)) {
                    $output->writeln('success');

                    return;
                }
            }
        }

        // フォルダを削除せずインストールを行い、その後インストール
        if ($mode == 'reload') {
            if (empty($code)) {
                $output->writeln('code is required.');

                return;
            }
            $stepFlg = false;
            $plugin = $this->getPluginFromCode($code);
            if ($service->uninstall($plugin)) {
                $stepFlg = true;
            }
            if ($stepFlg) {
                if ($this->installUnCompressPlugin($this->pluginPath.$code)) {
                    $output->writeln('success');

                    return;
                }
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
            // uninstallのみオプションにより2パターン存在する
            // ディレクトリは削除せず
            if ($mode == 'uninstall' && empty($force)) {
                if ($this->uninstallOnlyDb($plugin)) {
                    $output->writeln('success');

                    return;
                }
            }

            // ディレクトリ毎削除
            if ($mode == 'uninstall' && !empty($force)) {
                if ($service->uninstall($plugin, $path)) {
                    $output->writeln('success');

                    return;
                }
            }

            if ($service->$mode($plugin)) {
                $output->writeln('success');

                return;
            }
        }
        $output->writeln('undefined mode.');
    }

    /**
     * 設置のみプラグインのインストール
     * @param $path
     * @param int $source
     * @return bool
     * @throws PluginException
     * @throws \Exception
     */
    protected function installUnCompressPlugin($path, $source = 0)
    {
        $service = $this->app['eccube.service.plugin'];
        try {
            $service->checkPluginArchiveContent($path);

            $config = $service->readYml($path.'/'.$service::CONFIG_YML);
            $event = $service->readYml($path.'/'.$service::EVENT_YML);
            $service->registerPlugin($config, $event, $source); // dbにプラグイン登録
        } catch (PluginException $e) {
                throw $e;
        } catch (\Exception $e) { // インストーラがどんなExceptionを上げるかわからないので
                throw $e;
        }

        return true;

     }

    /**
     * 該当プラグインディレクトリの削除を伴わないアンインストール
     * @param \Eccube\Entity\Plugin $plugin
     * @return bool
     * @throws \Exception
     */
    protected function uninstallOnlyDb(\Eccube\Entity\Plugin $plugin)
    {
        $service = $this->app['eccube.service.plugin'];

        $pluginDir = $service->calcPluginDir($plugin->getCode());

        $service->callPluginManagerMethod($service->readYml($pluginDir.'/'.$service::CONFIG_YML), 'disable');
        $service->callPluginManagerMethod($service->readYml($pluginDir.'/'.$service::CONFIG_YML), 'uninstall');
        $service->unregisterPlugin($plugin);

        return true;
    }

}
