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
use Eccube\Command\PluginCommand\AbstractGenerator;

class PluginGenerator extends AbstractGenerator
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

    protected function getFildset()
    {
        return array(
            'pluginName' => array(
                'label' => '■プラグイン名: ',
                'value' => null,
                'name' => '■プラグイン名を入力してください',
                'validation' => array(
                    'isRequired' => true,
                )
            ),
            'pluginCode' => array(
                'label' => '■プラグインコード: ',
                'value' => null,
                'name' => '■プラグインコードは英数字で1文字目は必ず半角英字の大文字で入力してください',
                'validation' => array(
                    'isRequired' => true,
                    'patern' => '/^[A-Z][0-9a-zA-Z]*$/'
                )
            ),
            'version' => array(
                'label' => '■バージョン: ',
                'value' => null,
                'name' => '■バージョン形式はメジャー.マイナー.パッチ(x.x.x)',
                'validation' => array(
                    'isRequired' => true,
                    'patern' => '/^\d+.\d+.\d+$/'
                )
            ),
            'author' => array(
                'label' => '■作成者: ',
                'value' => null,
                'name' => '■作成者名や会社名を入力してください',
                'validation' => array(
                    'isRequired' => true,
                )
            ),
            'events' => array(
                'label' => '■共通イベント: ',
                'value' => array(),
                'name' => '■共通イベントを入力してください、例：eccube.event.front.response',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => $this->getEvents()
                )
            ),
            'hookPoints' => array(
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
            if ($this->isNextVersion()) {
                $Ref = new \ReflectionClass('\Eccube\Event\EccubeEvents');
                $this->hookPoints = array_flip($Ref->getConstants());
            } else {
                $this->hookPoints = array();
            }
        }

        return $this->hookPoints;
    }

    private function getEvents()
    {
        if ($this->events === null) {
            if ($this->isNextVersion()) {
                $this->events = include $this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/eventsListNew.php';
            } else {
                $this->events = include $this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/eventsList.php';
            }
        }

        return $this->events;
    }

    protected function start($paramList)
    {

        $pluginCode = $paramList['pluginCode']['value'];

        $Plugin = $this->app['eccube.repository.plugin']->findOneBy(array('code' => $pluginCode));
        if ($Plugin) {
            $this->exitGenerator('<error>同じcodeのプラグインが既に作成されています</error>');
            return;
        }
        $this->createFilesAndFolders($pluginCode, $paramList);
        $this->createDbRecords($pluginCode, $paramList);
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

        if ($this->isNextVersion()) {
            $events = $paramList['events']['value'];
            $eventsArr = array();
            if (count($events) > 0) {
                foreach ($events as $eventKey => $eventConst) {
                    $eventsArr[$eventKey] = array(array('on' . $eventConst . ', NORMAL'));
                }
            }

            $hookPoints = $paramList['hookPoints']['value'];
            $hookArr = array();
            $hookpointFunctions = array();
            if (count($hookPoints)) {
                foreach ($hookPoints as $hookKey => $hookConst) {
                    $onName = 'on' . join(array_map('ucfirst', explode('_', strtolower($hookConst))));
                    $hookArr[$hookKey] = array(array($onName . ', NORMAL'));
                    $hookpointFunctions[] = $onName;
                }
            }

            if (count($hookArr)) {
                $srcPath = $codePath . '/event.yml';
                file_put_contents($srcPath, str_replace('\'', '', Yaml::dump($hookArr + $eventsArr)));
                if (is_file($srcPath)) {
                    $fsList['file'][$srcPath] = true;
                } else {
                    $fsList['file'][$srcPath] = false;
                }
                if (count($eventsArr)) {
                    $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/EventHookpoint2.php');
                } else {
                    $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/EventHookpoint.php');
                }

                // Event
                $from = '/\[code\]/';
                $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
                $from = '/\[author\]/';
                $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
                $from = '/\[year\]/';
                $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);

                $functions = '';
                foreach ($hookpointFunctions as $functionName) {
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
            } else {
                if (count($eventsArr)) {
                    $srcPath = $codePath . '/event.yml';
                    file_put_contents($srcPath, str_replace('\'', '', Yaml::dump($eventsArr)));
                    if (is_file($srcPath)) {
                        $fsList['file'][$srcPath] = true;
                    } else {
                        $fsList['file'][$srcPath] = false;
                    }
                    // Event
                    $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/Event2.php');
                    $from = '/\[code\]/';
                    $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
                    $from = '/\[author\]/';
                    $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
                    $from = '/\[year\]/';
                    $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);

                    $srcPath = $codePath . '/' . $code . 'Event.php';
                    file_put_contents($srcPath, $pluginFileAfter);
                    if (is_file($srcPath)) {
                        $fsList['file'][$srcPath] = true;
                    } else {
                        $fsList['file'][$srcPath] = false;
                    }
                }
            }
        } else {
            if (count($eventsArr)) {
                $srcPath = $codePath . '/event.yml';
                file_put_contents($srcPath, str_replace('\'', '', Yaml::dump($eventsArr)));
                if (is_file($srcPath)) {
                    $fsList['file'][$srcPath] = true;
                } else {
                    $fsList['file'][$srcPath] = false;
                }
                // Event
                $pluginFileBefore = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/Event.php');
                $from = '/\[code\]/';
                $pluginFileAfter = preg_replace($from, $code, $pluginFileBefore);
                $from = '/\[author\]/';
                $pluginFileAfter = preg_replace($from, $author, $pluginFileAfter);
                $from = '/\[year\]/';
                $pluginFileAfter = preg_replace($from, $year, $pluginFileAfter);
                $srcPath = $codePath . '/' . $code . 'Event.php';
                file_put_contents($srcPath, $pluginFileAfter);
                if (is_file($srcPath)) {
                    $fsList['file'][$srcPath] = true;
                } else {
                    $fsList['file'][$srcPath] = false;
                }
            }
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
