<?php

namespace Eccube\Tests\Command;

use Guzzle\Http\Client;
use Eccube\Application;
use Eccube\Command\PluginCommand;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class PluginDevelopGenerateTest extends AbstractCommandTest
{

    private $testCase;

    public function setUp()
    {
        parent::setUp();
        $this->initCommand(new PluginCommand());
    }

    public function testFirst()
    {
        $code = 'PluginUnittestSample';

        $codePath = $this->app['config']['root_dir'] . '/app/Plugin/' . $code;
        $this->removePluginDir($codePath);
        $this->removePluginDb($code);

        $testCase = array(
            //プラグイン名
            1 => array(
                array(
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ),
                array(
                    'input' => 'Plugin Name',
                    'output' => 'Plugin Code:',
                ),
            ),
            //プラグインコード
            2 => array(
                array(
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ),
                array(
                    'input' => 'Plugin Name',
                    'output' => 'only pascal case letters numbers are allowed',
                ),
                array(
                    'input' => strtolower($code),
                    'output' => 'only pascal case letters numbers are allowed',
                ),
                array(
                    'input' => $code,
                    'output' => 'Version:',
                ),
            ),
            //バージョン
            3 => array(
                array(
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ),
                array(
                    'input' => 'ひらがな',
                    'output' => 'correct format is x.y.z',
                ),
                array(
                    'input' => 'alphabet',
                    'output' => 'correct format is x.y.z',
                ),
                array(
                    'input' => '1.0.0',
                    'output' => 'Author:',
                )
            ),
            //バージョン
            4 => array(
                array(
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ),
                array(
                    'input' => 'Author Name',
                    'output' => 'Old version support:',
                ),
            ),
            //サーポットバージョン
            5 => array(
                array(
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ),
                array(
                    'input' => 'a',
                    'output' => 'No results have been found',
                ),
                array(
                    'input' => 'y',
                    'output' => 'Site events:',
                ),
            ),
            //サイト共通イベント
            6 => array(
                array(
                    'input' => 'entry',
                    'output' => array(
                        'there are more then one search result',
                        'eccube.event.render.entry.before'
                    ),
                ),
                array(
                    'input' => 'eccube.event.render.entry.before',
                    'output' => array(
                        'your entry list',
                        'eccube.event.render.entry.before'
                    ),
                ),
                array(
                    'input' => 'product_list',
                    'output' => array(
                        'there are more then one search result',
                        'eccube.event.render.product_list.before'
                    ),
                ),
                array(
                    'input' => 'eccube.event.render.product_list.before',
                    'output' => array(
                        'your entry list',
                        'eccube.event.render.product_list.before'
                    ),
                ),
                array(
                    'input' => '',
                ),
            ),
            //フックポイント
            7 => array(
                array(
                    'input' => 'entry',
                    'output' => array(
                        'No results have been found',
                        'front.entry.index.initialize'
                    ),
                ),
                array(
                    'input' => 'front.entry.index.initialize',
                    'output' => array(
                        'your entry list',
                        'front.entry.index.initialize'
                    ),
                ),
                array(
                    'input' => 'change_password',
                    'output' => array(
                        'No results have been found',
                        'admin.admin.change_password.complete'
                    ),
                ),
                array(
                    'input' => 'admin.admin.change_password.complete',
                    'output' => array(
                        'your entry list',
                        'admin.admin.change_password.complete'
                    ),
                ),
                array(
                    'input' => '',
                ),
            ),
            //確認
            'confirm' => array(
                array(
                    'output' => array(
                        'Plugin Name',
                        $code,
                        '1.0.0',
                        'Author Name',
                        'Yes',
                        'eccube.event.render.entry.before',
                        'eccube.event.render.product_list.before',
                        'front.entry.index.initialize',
                        'admin.admin.change_password.complete',
                    ),
                ),
                array(
                    'input' => 'y',
                    'output' => '',
                ),
            )
        );
        $this->setTestCase($testCase);

        $commandArg = array(
            'command' => 'plugin:develop',
            'mode' => 'generate',
            '--no-ansi' => true,
        );

        $this->executeTester(array($this, 'checkQuestion'), $commandArg);

        //ファイルとフォルダー作成確認
        $ff = array(
            $codePath,
            $codePath . '/ServiceProvider',
            $codePath . '/ServiceProvider/' . $code . 'ServiceProvider.php',
            $codePath . '/Controller',
            $codePath . '/Form/Type',
            $codePath . '/Resource/template/admin',
            $codePath . '/config.yml',
            $codePath . '/PluginManager.php',
            $codePath . '/Controller/ConfigController.php',
            $codePath . '/Controller/' . $code . 'Controller.php',
            $codePath . '/Form/Type/' . $code . 'ConfigType.php',
            $codePath . '/Resource/template/admin/config.twig',
            $codePath . '/Resource/template/index.twig',
            $codePath . '/event.yml',
            $codePath . '/' . $code . 'Event.php',
            $codePath . '/LICENSE',
        );

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
                            $checkOutput = array($case['output']);
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

        throw new \Exception('Test case not set.' . PHP_EOL . ' output=' . $output);
    }

    protected function assertInOutput($output, $msg)
    {
        if (strpos($output, $msg) !== false) {
            $this->assertTrue(true);
            return true;
        }
        $error = 'Input string not found in output.' . PHP_EOL . ' search=' . $msg . PHP_EOL . ' output=' . $output . '' . PHP_EOL;
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
        $pluginList = $entityManager->getRepository('\Eccube\\Entity\Plugin')->findBy(array('code' => $code));
        if ($pluginList) {
            foreach ($pluginList as $plugin) {
                $pluginHandlerList = $entityManager->getRepository('\Eccube\Entity\PluginEventHandler')->findBy(array('plugin_id' => $plugin->getId()));
                if ($pluginHandlerList) {
                    foreach ($pluginHandlerList as $pluginHandler) {
                        $entityManager->remove($pluginHandler);
                    }
                }
                $entityManager->remove($plugin);
            }
            $entityManager->flush();
        }
    }

    protected function checkFileAndFolder($ff)
    {
        foreach ($ff as $path) {
            $msg = 'fail assert that a file/path exists.(' . $path . ')';
            $this->assertTrue(file_exists($path), $msg);
        }
    }
}
