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

namespace Eccube\Command\PluginCommand;

use Eccube\Common\Constant;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Eccube\Entity\Plugin;
use Eccube\Entity\PluginEventHandler;
use Eccube\Command\PluginCommand\AbstractPluginGenerator;

class PluginGenerator extends AbstractPluginGenerator
{

    /**
     *
     * @var array
     */
    private $hookPoints = null;

    /**
     *
     * @var array
     */
    private $events = null;

    protected function getHeader()
    {
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('---プラグインジェネレータ');
        $this->output->writeln('---※プログラムを終了するには' . self::STOP_PROCESS . 'を入力してください');
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('');
    }

    protected function initFildset()
    {
        $this->paramList = array(
            'pluginName' => array(
                'no' => 1,
                'label' => '■プラグイン名: ',
                'value' => null,
                'name' => '■プラグイン名を入力してください',
                'validation' => array(
                    'isRequired' => true,
                )
            ),
            'pluginCode' => array(
                'no' => 2,
                'label' => '■プラグインコード: ',
                'value' => null,
                'name' => '■プラグインコードは英数字で1文字目は必ず半角英字の大文字で入力してください',
                'validation' => array(
                    'isRequired' => true,
                    'patern' => '/^[A-Z][0-9a-zA-Z]*$/'
                )
            ),
            'version' => array(
                'no' => 3,
                'label' => '■バージョン: ',
                'value' => null,
                'name' => '■バージョン形式はメジャー.マイナー.パッチ(x.x.x)',
                'validation' => array(
                    'isRequired' => true,
                    'patern' => '/^\d+.\d+.\d+$/'
                )
            ),
            'author' => array(
                'no' => 4,
                'label' => '■作成者: ',
                'value' => null,
                'name' => '■作成者名や会社名を入力してください',
                'validation' => array(
                    'isRequired' => true,
                )
            ),
            'supportFlag' => array(
                'no' => 5,
                'label' => '■サーポットバージョン: ',
                'value' => null,
                'name' => '■サーホットバージョン対応いりますか? [y/n]',
                'show' => array(1 => 'Yes' ,0 => 'No'),
                'validation' => array(
                    'isRequired' => true,
                    'choice' => array('y' => 1, 'n' => 0)
                )
            ),
            'events' => array(
                'no' => 6,
                'label' => '■サイト共通イベント: ',
                'value' => array(),
                'name' => '■サイト共通イベントを入力してください(プラグイン仕様書を参照 http://www.ec-cube.net/plugin/)',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => 'getEvents'
                )
            ),
            'hookPoints' => array(
                'no' => 7,
                'label' => '■フックポイント: ',
                'value' => array(),
                'name' => '■フックポイントを入力してください、例：front.cart.up.initialize',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => $this->getHookPoints()
                )
            )
        );
    }

    private function getHookPoints()
    {
        if ($this->hookPoints === null) {
            $Ref = new \ReflectionClass('\Eccube\Event\EccubeEvents');
            $this->hookPoints = array_flip($Ref->getConstants());
        }
        return $this->hookPoints;
    }

    protected function getEvents()
    {
        if (!isset($this->paramList['supportFlag']['value'])) {
            return array();
        }
        if ($this->events === null) {
            $this->events = array();
            $routeEvents = array();
            if ($this->paramList['supportFlag']['value']) {
                $this->events = include $this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/eventsList.php';
                $routeEvents['eccube.event.controller.__route__.before'] = 'Controller__route__Before';
                $routeEvents['eccube.event.controller.__route__.after'] = 'Controller__route__After';
                $routeEvents['eccube.event.controller.__route__.finish'] = 'Controller__route__Finish';
                $routeEvents['eccube.event.render.__route__.before'] = 'Render__route__Before';
            }
            $this->events += include $this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/eventsListNew.php';

            $routeEvents['eccube.event.route.__route__.request'] = 'Route__route__Request';
            $routeEvents['eccube.event.route.__route__.controller'] = 'Route__route__Controller';
            $routeEvents['eccube.event.route.__route__.response'] = 'Route__route__Response';
            $routeEvents['eccube.event.route.__route__.exception'] = 'Route__route__Exception';
            $routeEvents['eccube.event.route.__route__.terminate'] = 'Route__route__Terminate';
            $allRoutes = array();

            $controllers = $this->app['controllers'];
            $collection = $controllers->flush();
            foreach ($collection as $eventName => $dummy) {
                $allRoutes[] = $eventName;
            }

            $routes = $this->app['routes']->all();

            foreach ($routes as $eventName => $dummy) {
                $allRoutes[] = $eventName;
            }

            foreach ($allRoutes as $eventName) {
                $eventOnFunc = join(array_map('ucfirst', explode('_', strtolower($eventName))));

                foreach ($routeEvents as $keys => $node) {
                    $this->events[str_replace('__route__', $eventName, $keys)] = str_replace('__route__', $eventOnFunc, $node);
                }
            }
        }

        return $this->events;
    }

    protected function start()
    {
        $pluginCode = $this->paramList['pluginCode']['value'];

        $Plugin = $this->app['eccube.repository.plugin']->findOneBy(array('code' => $pluginCode));
        if ($Plugin) {
            $this->exitGenerator('<error>同じcodeのプラグインが既に作成されています</error>');
            return;
        }
        $this->createFilesAndFolders($pluginCode, $this->paramList);
        $this->createDbRecords($pluginCode, $this->paramList);
        $this->exitGenerator('プラグイン作成完了しました');
    }

    private function createFilesAndFolders($code, $paramList)
    {
        $fsList = array(
            'dir' => array(),
            'file' => array(),
        );

        // config.ymlを作成
        $config = array();
        $config['name'] = $paramList['pluginName']['value'];
        $config['code'] = $code;
        $config['version'] = $paramList['version']['value'];
        if (!empty($paramList['hookPoints']['value'])) {
            $config['event'] = $code . 'Event';
        }
        $config['service'] = array($code . 'ServiceProvider');

        $codePath = $this->app['config']['root_dir'] . '/app/Plugin/' . $code;

        $file = new Filesystem();
        $file->mkdir($codePath);
        if (is_dir($codePath)) {
            $fsList['dir'][$codePath] = true;
        } else {
            $fsList['dir'][$codePath] = false;
        }

        $srcPath = $codePath . '/config.yml';
        file_put_contents($srcPath, Yaml::dump($config));
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        $author = $paramList['pluginName']['value'];
        $year = date('Y');

        // PluginManager
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/PluginManager.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);

        $srcPath = $codePath . '/PluginManager.php';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // ServiceProvider
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/ServiceProvider.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);

        $file->mkdir($codePath . '/ServiceProvider');
        if (is_dir($codePath . '/ServiceProvider')) {
            $fsList['dir'][$codePath . '/ServiceProvider'] = true;
        } else {
            $fsList['dir'][$codePath . '/ServiceProvider'] = false;
        }

        $srcPath = $codePath . '/ServiceProvider/' . $code . 'ServiceProvider.php';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // ConfigController
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/ConfigController.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);
        $from = '/\[code_name\]/';
        $pluginFileAfter = preg_replace($from, mb_strtolower($code), $pluginFileAfter);

        $file->mkdir($codePath . '/Controller');
        if (is_dir($codePath . '/Controller')) {
            $fsList['dir'][$codePath . '/Controller'] = true;
        } else {
            $fsList['dir'][$codePath . '/Controller'] = false;
        }

        $srcPath = $codePath . '/Controller/ConfigController.php';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // Controller
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/Controller.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);
        $from = '/\[code_name\]/';
        $pluginFileAfter = preg_replace($from, mb_strtolower($code), $pluginFileAfter);

        $srcPath = $codePath . '/Controller/' . $code . 'Controller.php';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // Form
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/ConfigType.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);
        $from = '/\[code_name\]/';
        $pluginFileAfter = preg_replace($from, mb_strtolower($code), $pluginFileAfter);

        $file->mkdir($codePath . '/Form/Type');
        if (is_dir($codePath . '/Form/Type')) {
            $fsList['dir'][$codePath . '/Form/Type'] = true;
        } else {
            $fsList['dir'][$codePath . '/Form/Type'] = false;
        }

        $srcPath = $codePath . '/Form/Type/' . $code . 'ConfigType.php';
        file_put_contents($codePath . '/Form/Type/' . $code . 'ConfigType.php', $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // Twig
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/config.twig');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);

        $file->mkdir($codePath . '/Resource/template/admin');
        if (is_dir($codePath . '/Resource/template/admin')) {
            $fsList['dir'][$codePath . '/Resource/template/admin'] = true;
        } else {
            $fsList['dir'][$codePath . '/Resource/template/admin'] = false;
        }

        $srcPath = $codePath . '/Resource/template/admin/config.twig';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // index.twig
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/index.twig');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);

        $file->mkdir($codePath . '/Resource/template/admin');
        if (is_dir($codePath . '/Resource/template/admin')) {
            $fsList['dir'][$codePath . '/Resource/template/admin'] = true;
        } else {
            $fsList['dir'][$codePath . '/Resource/template/admin'] = false;
        }

        $srcPath = $codePath . '/Resource/template/index.twig';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        $onFunctions = array();
        $onEvents = array();

        //イベント
        $events = $paramList['events']['value'];
        if (count($events) > 0) {
            foreach ($events as $eventKey => $eventConst) {
                $onEvents[$eventKey] = array(array('on' . $eventConst . ', NORMAL'));
                $onFunctions[] = 'on' . $eventConst;
            }
        }

        //フックポイント
        $hookPoints = $paramList['hookPoints']['value'];
        if (count($hookPoints)) {
            foreach ($hookPoints as $hookKey => $hookConst) {
                $onName = 'on' . join(array_map('ucfirst', explode('_', strtolower($hookConst))));
                $onEvents[$hookKey] = array(array($onName . ', NORMAL'));
                $onFunctions[] = $onName;
            }
        }

        if (count($onEvents)) {
            $srcPath = $codePath . '/event.yml';
            file_put_contents($srcPath, str_replace('\'', '', Yaml::dump($onEvents)));
            if (is_file($srcPath)) {
                $fsList['file'][$srcPath] = true;
            } else {
                $fsList['file'][$srcPath] = false;
            }

            $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/EventHookpoint2.php');

            // Event
            $from = '/\[code\]/';
            $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
            $from = '/\[author\]/';
            $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
            $from = '/\[year\]/';
            $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);

            $functions = '';
            foreach ($onFunctions as $functionName) {
                $functions .= "    public function " . $functionName . "(EventArgs \$event)\n    {\n    }\n\n";
            }
            $from = '/\[hookpoint_function\]/';
            $pluginFileAfter = preg_replace($from, $functions, $pluginFileAfter);
            $srcPath = $codePath . '/' . $code . 'Event.php';
            file_put_contents($srcPath, $pluginFileAfter);
            if (is_file($srcPath)) {
                $fsList['file'][$srcPath] = true;
            } else {
                $fsList['file'][$srcPath] = false;
            }
        }

        // config.ymlを再作成
        $config = array();
        $config['name'] = $paramList['pluginName']['value'];
        $config['code'] = $code;
        $config['version'] = $paramList['version']['value'];
        $config['event'] = $code . 'Event';
        $config['service'] = array($code . 'ServiceProvider');
        $srcPath = $codePath . '/config.yml';
        file_put_contents($srcPath, Yaml::dump($config));
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // LICENSE
        $srcPath = $codePath . '/LICENSE';
        $file->copy($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/LICENSE', $srcPath);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        $dirFileNg = array();
        $dirFileOk = array();
        foreach ($fsList['dir'] as $path => $flag) {
            if ($flag) {
                $dirFileOk[] = $path;
            } else {
                $dirFileNg[] = $path;
            }
        }
        foreach ($fsList['file'] as $path => $flag) {
            if ($flag) {
                $dirFileOk[] = $path;
            } else {
                $dirFileNg[] = $path;
            }
        }
        $this->output->writeln('');
        $this->output->writeln('■ファイルシステム');
        if (!empty($dirFileOk)) {
            $this->output->writeln('');
            $this->output->writeln('  以下のファイルとフォルダーを作成しました');
            foreach ($dirFileOk as $path) {
                $this->output->writeln('<info> - ' . $path . '</info>');
            }
        }

        if (!empty($dirFileNg)) {
            $this->output->writeln('');
            $this->output->writeln('  以下のファイルとフォルダーを作成失敗しました');
            foreach ($dirFileOk as $path) {
                $this->output->writeln('<error> - ' . $path . '</error>');
            }
        }
    }

    private function createDbRecords($code, $paramList)
    {
        // DB登録
        $Plugin = new Plugin();
        $Plugin->setName($paramList['pluginName']['value']);
        $Plugin->setCode($code);
        $Plugin->setClassName('');
        $Plugin->setVersion($paramList['version']['value']);
        $Plugin->setEnable(Constant::DISABLED);
        $Plugin->setSource(0);
        $Plugin->setDelFlg(Constant::DISABLED);

        $this->app['orm.em']->persist($Plugin);
        $this->app['orm.em']->flush($Plugin);

        $this->output->writeln('');
        $this->output->writeln('■データベース');
        if ($Plugin->getId()) {
            $this->output->writeln('<info> テーブル「DB.Plugin」に登録しました（id=' . $Plugin->getId() . '）</info>');
        } else {
            $this->output->writeln('<error> テーブル「DB.Plugin」に登録失敗しました（id=' . $Plugin->getId() . '）</error>');
        }

        $hookPoints = $paramList['hookPoints']['value'];
        if (empty($hookPoints)) {
            return;
        }

        $eventCount = 0;
        foreach ($hookPoints as $hookKey => $hookConst) {
            $PluginEventHandler = new PluginEventHandler();
            $functionName = 'on' . join(array_map('ucfirst', explode('_', strtolower($hookConst))));
            $PluginEventHandler->setPlugin($Plugin)
                ->setEvent($hookKey)
                ->setPriority($this->app['eccube.repository.plugin_event_handler']->calcNewPriority($hookKey, $functionName))
                ->setHandler($functionName)
                ->setHandlerType('NORMAL')
                ->setDelFlg(Constant::DISABLED);
            $this->app['orm.em']->persist($PluginEventHandler);
            $eventCount++;
        }
        $this->app['orm.em']->flush();
        if ($eventCount) {
            $this->output->writeln('');
            $this->output->writeln('<info> テーブル「DB.PluginEventHandler」に登録しました（件数=' . $eventCount . '）</info>');
        }
    }
}
