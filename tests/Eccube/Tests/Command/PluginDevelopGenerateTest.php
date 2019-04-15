<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Command;

use Eccube\Command\PluginCommand;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class PluginDevelopGenerateTest extends AbstractCommandTest
{
    private $testCase;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();

        $this->markTestIncomplete();

        if ($this->app['config']['database']['driver'] == 'pdo_sqlite') {
            $this->markTestSkipped('Can not support for sqlite3');
        }

        $this->initCommand(new PluginCommand());
    }

    public function testFirst()
    {
        // TODO question helperのunit testの実装方法を変更
        // http://symfony.com/doc/current/components/console/helpers/questionhelper.html
        $this->markTestSkipped();

        $code = 'PluginUnittestSample';

        $codePath = $this->app['config']['root_dir'].'/app/Plugin/'.$code;
        $this->removePluginDir($codePath);
        $this->removePluginDb($code);

        $testCase = [
            //プラグイン名
            1 => [
                [
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ],
                [
                    'input' => 'Plugin Name',
                    'output' => 'Plugin Code:',
                ],
            ],
            //プラグインコード
            2 => [
                [
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ],
                [
                    'input' => 'Plugin Code',
                    'output' => 'Please enter Plugin Code (First letter is uppercase alphabet only. alphabet and numbers are allowed.)',
                ],
                [
                    'input' => strtolower($code),
                    'output' => 'Please enter Plugin Code (First letter is uppercase alphabet only. alphabet and numbers are allowed.',
                ],
                [
                    'input' => $code,
                    'output' => 'Version:',
                ],
            ],
            //バージョン
            3 => [
                [
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ],
                [
                    'input' => 'ひらがな',
                    'output' => 'correct format is x.y.z',
                ],
                [
                    'input' => 'alphabet',
                    'output' => 'correct format is x.y.z',
                ],
                [
                    'input' => '1.0.0',
                    'output' => 'Author:',
                ],
            ],
            //バージョン
            4 => [
                [
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ],
                [
                    'input' => 'Author Name',
                    'output' => 'Old version support:',
                ],
            ],
            //サーポットバージョン
            5 => [
                [
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ],
                [
                    'input' => 'a',
                    'output' => 'No results have been found',
                ],
                [
                    'input' => 'y',
                    'output' => 'Site events:',
                ],
            ],
            //サイト共通イベント
            6 => [
                [
                    'input' => 'entry',
                    'output' => [
                        'there are more then one search result',
                        'eccube.event.render.entry.before',
                    ],
                ],
                [
                    'input' => 'eccube.event.render.entry.before',
                    'output' => [
                        'your entry list',
                        'eccube.event.render.entry.before',
                    ],
                ],
                [
                    'input' => 'product_list',
                    'output' => [
                        'there are more then one search result',
                        'eccube.event.render.product_list.before',
                    ],
                ],
                [
                    'input' => 'eccube.event.render.product_list.before',
                    'output' => [
                        'your entry list',
                        'eccube.event.render.product_list.before',
                    ],
                ],
                [
                    'input' => '',
                ],
            ],
            //フックポイント
            7 => [
                [
                    'input' => 'entry',
                    'output' => [
                        'No results have been found',
                        'front.entry.index.initialize',
                    ],
                ],
                [
                    'input' => 'front.entry.index.initialize',
                    'output' => [
                        'your entry list',
                        'front.entry.index.initialize',
                    ],
                ],
                [
                    'input' => 'change_password',
                    'output' => [
                        'No results have been found',
                        'admin.admin.change_password.complete',
                    ],
                ],
                [
                    'input' => 'admin.admin.change_password.complete',
                    'output' => [
                        'your entry list',
                        'admin.admin.change_password.complete',
                    ],
                ],
                [
                    'input' => '',
                ],
            ],
            //orm.path
            8 => [
                [
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ],
                [
                    'input' => 'a',
                    'output' => 'No results have been found',
                ],
                [
                    'input' => 'y',
                    'output' => 'Use orm.path:',
                ],
            ],
            //確認
            'confirm' => [
                [
                    'output' => [
                        'Plugin Name',
                        $code,
                        '1.0.0',
                        'Author Name',
                        'Yes',
                        'eccube.event.render.entry.before',
                        'eccube.event.render.product_list.before',
                        'front.entry.index.initialize',
                        'admin.admin.change_password.complete',
                        'Yes',
                    ],
                ],
                [
                    'input' => 'y',
                    'output' => '',
                ],
            ],
        ];
        $this->setTestCase($testCase);

        $commandArg = [
            'command' => 'plugin:develop',
            'mode' => 'generate',
            '--no-ansi' => true,
        ];

        $this->executeTester([$this, 'checkQuestion'], $commandArg);

        //ファイルとフォルダー作成確認
        $ff = [
            $codePath,
            $codePath.'/ServiceProvider',
            $codePath.'/ServiceProvider/'.$code.'ServiceProvider.php',
            $codePath.'/Controller',
            $codePath.'/Form/Type',
            $codePath.'/Resource/template/admin',
            $codePath.'/config.yml',
            $codePath.'/PluginManager.php',
            $codePath.'/Controller/ConfigController.php',
            $codePath.'/Controller/'.$code.'Controller.php',
            $codePath.'/Form/Type/'.$code.'ConfigType.php',
            $codePath.'/Resource/template/admin/config.twig',
            $codePath.'/Resource/template/index.twig',
            $codePath.'/event.yml',
            $codePath.'/'.$code.'Event.php',
            $codePath.'/LICENSE',
        ];

        $this->checkFileAndFolder($ff);

        $this->removePluginDir($codePath);
        $this->removePluginDb($code);
    }

    public function checkQuestion($text, Question $question)
    {
        $output = $this->getLastContent();
        foreach ($this->testCase as $no => $row) {
            if (is_numeric($no)) {
                $searchStr = $this->getQuestionMark($no);
            } else {
                $searchStr = $no;
            }
            if (strpos($text, $searchStr) !== false) {
                foreach ($row as $subNo => $case) {
                    $ret = null;
                    if (isset($case['input'])) {
                        $ret = $case['input'];
                        unset($this->testCase[$no][$subNo]['input']);
                    } elseif (isset($case['output'])) {
                        if (is_array($case['output'])) {
                            $checkOutput = $case['output'];
                        } else {
                            $checkOutput = [$case['output']];
                        }
                        foreach ($checkOutput as $node) {
                            $this->assertInOutput($output, $node);
                        }
                        unset($this->testCase[$no][$subNo]['output']);
                    }
                    if ($ret !== null) {
                        return $ret;
                    }
                    continue;
                }
            }
        }

        throw new \Exception('Test case not set.'.PHP_EOL.' output='.$output);
    }

    protected function assertInOutput($output, $msg)
    {
        if (strpos($output, $msg) !== false) {
            $this->assertTrue(true);

            return true;
        }
        $error = 'Input string not found in output.'.PHP_EOL.' search='.$msg.PHP_EOL.' output='.$output.''.PHP_EOL;
        $this->assertTrue(false, $error);

        return false;
    }

    protected function setTestCase($testCase)
    {
        $this->testCase = $testCase;
    }

    protected function removePluginDir($pluginPath)
    {
        if (!empty($pluginPath) && file_exists($pluginPath)) {
            $fs = new Filesystem();
            $fs->remove($pluginPath);
        }
    }

    protected function removePluginDb($code)
    {
        /* @var $entityManager \Doctrine\ORM\EntityManager  */
        $entityManager = $this->app['orm.em'];

        $entityManager->clear();
        $pluginList = $entityManager->getRepository('\Eccube\\Entity\Plugin')->findBy(['code' => $code]);
        if ($pluginList) {
            foreach ($pluginList as $plugin) {
                $entityManager->remove($plugin);
            }
            $entityManager->flush();
        }
    }

    protected function checkFileAndFolder($ff)
    {
        foreach ($ff as $path) {
            $msg = 'fail assert that a file/path exists.('.$path.')';
            $this->assertTrue(file_exists($path), $msg);
        }
    }
}
