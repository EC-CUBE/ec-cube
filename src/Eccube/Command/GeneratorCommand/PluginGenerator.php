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

namespace Eccube\Command\GeneratorCommand;

use Eccube\Common\Constant;
use Eccube\Entity\Plugin;
use Eccube\Entity\PluginEventHandler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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
        $this->output->writeln('---Plugin Generator');
        $this->output->writeln('---[*]You can exit from Console Application, by typing '.self::STOP_PROCESS.' instead of typing another word.');
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('');
    }

    protected function initFieldSet()
    {
        $this->paramList = array(
            'pluginName' => array(
                'no' => 1,
                'label' => '[+]Plugin Name: ',
                'value' => null,
                'name' => '[+]Please enter Plugin Name',
                'validation' => array(
                    'isRequired' => true,
                )
            ),
            'pluginCode' => array(
                'no' => 2,
                'label' => '[+]Plugin Code: ',
                'value' => null,
                'name' => '[+]Please enter Plugin Code (First letter is uppercase alphabet only. alphabet and numbers are allowed.)',
                'validation' => array(
                    'isRequired' => true,
                    'pattern' => '/^[A-Z][0-9a-zA-Z]*$/',
                    'isCode' => $this->getPluginCodes(),
                )
            ),
            'version' => array(
                'no' => 3,
                'label' => '[+]Version: ',
                'value' => null,
                'name' => '[+]Please enter version (correct format is x.y.z)',
                'validation' => array(
                    'isRequired' => true,
                    'pattern' => '/^\d+.\d+.\d+$/',
                )
            ),
            'author' => array(
                'no' => 4,
                'label' => '[+]Author: ',
                'value' => null,
                'name' => '[+]Please enter author name or company',
                'validation' => array(
                    'isRequired' => true,
                )
            ),
            'supportFlag' => array(
                'no' => 5,
                'label' => '[+]Old version support: ',
                'value' => null,
                'name' => '[+]Do you want to support old versions too? [y/n]',
                'show' => array(1 => 'Yes', 0 => 'No'),
                'validation' => array(
                    'isRequired' => true,
                    'choice' => array('y' => 1, 'n' => 0),
                )
            ),
            'events' => array(
                'no' => 6,
                'label' => '[+]SiteEvents: ',
                'value' => array(),
                'name' => '[+]Please enter site events(you can find documentation here http://www.ec-cube.net/plugin/)',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => 'getEvents',
                )
            ),
            'hookPoints' => array(
                'no' => 7,
                'label' => '[+]hookpoint: ',
                'value' => array(),
                'name' => '[+]Please enter hookpoint, sample：front.cart.up.initialize',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => $this->getHookPoints(),
                )
            ),
            'useOrmPath' => array(
                'no' => 8,
                'label' => '[+]Use orm.path: ',
                'value' => null,
                'name' => '[+]Would you like to use orm.path? [y/n]',
                'show' => array(1 => 'Yes', 0 => 'No'),
                'validation' => array(
                    'isRequired' => true,
                    'choice' => array('y' => 1, 'n' => 0),
                )
            ),
        );
    }

    /**
     * フックポイント一覧の取得
     *
     * @return array
     */
    protected function getHookPoints()
    {
        if ($this->hookPoints === null) {
            $Ref = new \ReflectionClass('\Eccube\Event\EccubeEvents');
            $this->hookPoints = array_flip($Ref->getConstants());
        }

        return $this->hookPoints;
    }

    /**
     * イベント一覧の取得
     *
     * @return array|mixed
     */
    protected function getEvents()
    {
        if (!isset($this->paramList['supportFlag']['value'])) {
            return array();
        }
        if ($this->events === null) {
            $this->events = array();
            $routeEvents = array();
            if ($this->paramList['supportFlag']['value']) {
                $this->events = include $this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/eventList.php';
                $routeEvents['eccube.event.controller.__route__.before'] = 'Controller__route__Before';
                $routeEvents['eccube.event.controller.__route__.after'] = 'Controller__route__After';
                $routeEvents['eccube.event.controller.__route__.finish'] = 'Controller__route__Finish';
                $routeEvents['eccube.event.render.__route__.before'] = 'Render__route__Before';
            }
            $this->events += include $this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/eventListNew.php';

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

        $codes = $this->getPluginCodes();
        if (in_array($pluginCode, $codes)) {
            $this->exitGenerator('<error>Plugin with this code already exists.</error>');

            return;
        }

        $this->createFilesAndFolders($pluginCode, $this->paramList);
        $this->createDbRecords($pluginCode, $this->paramList);
        $this->exitGenerator('Plugin was created successfully');
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
        if (!empty($paramList['hookPoints']['value']) || !empty($paramList['events']['value'])) {
            $config['event'] = $code.'Event';
        }
        $config['service'] = array($code.'ServiceProvider');
        if ($this->paramList['useOrmPath']['value']) {
            $config['orm.path'] = array('/Resource/doctrine');
        }

        $codePath = $this->app['config']['root_dir'].'/app/Plugin/'.$code;

        $file = new Filesystem();
        $file->mkdir($codePath);
        if (is_dir($codePath)) {
            $fsList['dir'][$codePath] = true;
        } else {
            $fsList['dir'][$codePath] = false;
        }

        $srcPath = $codePath.'/config.yml';
        file_put_contents($srcPath, Yaml::dump($config));
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        $author = $paramList['author']['value'];
        $year = date('Y');

        // PluginManager
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/PluginManager.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);

        $srcPath = $codePath.'/PluginManager.php';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // ServiceProvider
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/ServiceProvider.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[lower_code\]/';
        $pluginFileAfter = preg_replace($from, mb_strtolower($code), $pluginFileAfter);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);

        $file->mkdir($codePath.'/ServiceProvider');
        if (is_dir($codePath.'/ServiceProvider')) {
            $fsList['dir'][$codePath.'/ServiceProvider'] = true;
        } else {
            $fsList['dir'][$codePath.'/ServiceProvider'] = false;
        }

        $srcPath = $codePath.'/ServiceProvider/'.$code.'ServiceProvider.php';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // ConfigController
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/ConfigController.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);
        $from = '/\[code_name\]/';
        $pluginFileAfter = preg_replace($from, mb_strtolower($code), $pluginFileAfter);

        $file->mkdir($codePath.'/Controller');
        if (is_dir($codePath.'/Controller')) {
            $fsList['dir'][$codePath.'/Controller'] = true;
        } else {
            $fsList['dir'][$codePath.'/Controller'] = false;
        }

        $srcPath = $codePath.'/Controller/ConfigController.php';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // Controller
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/Controller.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);
        $from = '/\[code_name\]/';
        $pluginFileAfter = preg_replace($from, mb_strtolower($code), $pluginFileAfter);

        $srcPath = $codePath.'/Controller/'.$code.'Controller.php';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // Form
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/ConfigType.php');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
        $from = '/\[author\]/';
        $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
        $from = '/\[year\]/';
        $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);
        $from = '/\[code_name\]/';
        $pluginFileAfter = preg_replace($from, mb_strtolower($code), $pluginFileAfter);

        $file->mkdir($codePath.'/Form/Type');
        if (is_dir($codePath.'/Form/Type')) {
            $fsList['dir'][$codePath.'/Form/Type'] = true;
        } else {
            $fsList['dir'][$codePath.'/Form/Type'] = false;
        }

        $srcPath = $codePath.'/Form/Type/'.$code.'ConfigType.php';
        file_put_contents($codePath.'/Form/Type/'.$code.'ConfigType.php', $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // Twig
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/config.twig');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);

        $file->mkdir($codePath.'/Resource/template/admin');
        if (is_dir($codePath.'/Resource/template/admin')) {
            $fsList['dir'][$codePath.'/Resource/template/admin'] = true;
        } else {
            $fsList['dir'][$codePath.'/Resource/template/admin'] = false;
        }

        $srcPath = $codePath.'/Resource/template/admin/config.twig';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        // index.twig
        $pluginFileBefore = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/index.twig');
        $from = '/\[code\]/';
        $pluginFileAfter = preg_replace($from, mb_strtolower($code), $pluginFileBefore);

        $file->mkdir($codePath.'/Resource/template/admin');
        if (is_dir($codePath.'/Resource/template/admin')) {
            $fsList['dir'][$codePath.'/Resource/template/admin'] = true;
        } else {
            $fsList['dir'][$codePath.'/Resource/template/admin'] = false;
        }

        $srcPath = $codePath.'/Resource/template/index.twig';
        file_put_contents($srcPath, $pluginFileAfter);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        $onFunctions = array();
        $eventKeys = array();
        $onEvents = array();

        // イベント
        $events = $paramList['events']['value'];
        if (count($events) > 0) {
            foreach ($events as $eventKey => $eventConst) {
                $onEvents[$eventKey] = array(array('on'.$eventConst.', NORMAL'));
                $onFunctions[$eventKey] = 'on'.$eventConst;
                $eventKeys[] = $eventKey;
            }
        }

        // フックポイント
        $hookPoints = $paramList['hookPoints']['value'];
        if (count($hookPoints)) {
            foreach ($hookPoints as $hookKey => $hookConst) {
                $onName = 'on'.join(array_map('ucfirst', explode('_', strtolower($hookConst))));
                $onEvents[$hookKey] = array(array($onName.', NORMAL'));
                $onFunctions[$hookKey] = $onName;
            }
        }

        if (count($onEvents)) {
            $srcPath = $codePath.'/event.yml';
            file_put_contents($srcPath, str_replace('\'', '', Yaml::dump($onEvents)));
            if (is_file($srcPath)) {
                $fsList['file'][$srcPath] = true;
            } else {
                $fsList['file'][$srcPath] = false;
            }

            $pluginFileBefore = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/Event.php');

            // Event
            $from = '/\[code\]/';
            $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
            $from = '/\[author\]/';
            $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
            $from = '/\[year\]/';
            $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);

            $functions = '';
            $args = include $this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/eventArguments.php';
            foreach ($onFunctions as $key => $name) {
                if (in_array($key, $eventKeys)) {
                    // 共通イベントは引数の型を利用するイベントにより変更
                    $ext = pathinfo($key, PATHINFO_EXTENSION);
                    if (array_key_exists($ext, $args)) {
                        $functions .= "    /**\n     * @param {$args[$ext]} \$event\n     */\n    public function {$name}({$args[$ext]} \$event)\n    {\n    }\n\n";
                    } else {
                        // 旧イベントの場合、引数は「eccube.event.render」のみ可能
                        if (preg_match("/^eccube.event.render\./", $key)) {
                            $functions .= "    /**\n     * @param {$args['eccube.event.render']} \$event\n     */\n    public function {$name}({$args['eccube.event.render']} \$event)\n    {\n    }\n\n";
                        } else {
                            $functions .= "    /**\n     *\n     */\n    public function {$name}()\n    {\n    }\n\n";
                        }
                    }
                } else {
                    // HookPointイベントの引数はEventArgs共通
                    $functions .= "    /**\n     * @param EventArgs \$event\n     */\n    public function {$name}(EventArgs \$event)\n    {\n    }\n\n";
                }
            }
            $from = '/\[hookpoint_function\]/';
            $pluginFileAfter = preg_replace($from, $functions, $pluginFileAfter);
            $srcPath = $codePath.'/'.$code.'Event.php';
            file_put_contents($srcPath, $pluginFileAfter);
            if (is_file($srcPath)) {
                $fsList['file'][$srcPath] = true;
            } else {
                $fsList['file'][$srcPath] = false;
            }
        }

        // LICENSE
        $srcPath = $codePath.'/LICENSE';
        $file->copy($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/LICENSE', $srcPath);
        if (is_file($srcPath)) {
            $fsList['file'][$srcPath] = true;
        } else {
            $fsList['file'][$srcPath] = false;
        }

        $this->completeMessage($fsList);

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
        $this->output->writeln('[+]Database');
        if ($Plugin->getId()) {
            $this->output->writeln('<info> Plugin information was added to table [DB.Plugin] (id='.$Plugin->getId().')</info>');
        } else {
            $this->output->writeln('<error> there was a problem inserting plugin information to table [DB.Plugin] (id='.$Plugin->getId().')</error>');
        }

        $hookPoints = $paramList['hookPoints']['value'];
        if (empty($hookPoints)) {
            return;
        }

        $eventCount = 0;
        foreach ($hookPoints as $hookKey => $hookConst) {
            $PluginEventHandler = new PluginEventHandler();
            $functionName = 'on'.join(array_map('ucfirst', explode('_', strtolower($hookConst))));
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
            $this->output->writeln('<info> Plugin information was added to table [DB.PluginEventHandler] (inserts number='.$eventCount.') </info>');
        }
    }
}
