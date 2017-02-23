<?php

namespace Eccube\Tests\Command;

use Eccube\Application;
use Eccube\Command\PluginCommand;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class PluginDevelopEntityFromYamlTest extends AbstractCommandTest
{

    private $testCase;

    public function setUp()
    {
        parent::setUp();
        if ($this->app['config']['database']['driver'] == 'pdo_sqlite') {
            $this->markTestSkipped('Can not support for sqlite3');
        }

        $this->initCommand(new PluginCommand());
    }

    public function testFirst()
    {
        $code = 'PluginUnittestSample';

        $codePath = $this->app['config']['root_dir'] . '/app/Plugin/' . $code;
        $this->removePluginDir($codePath);
        $this->createPluginDir($codePath, $code);

        $yamlName = 'Plugin.' . $code . '.Entity.' . ucfirst(strtolower($code)) . '.dcm.yml';

        $testCase = array(
            //プラグイン名
            'entity' => array(
                array(
                    'input' => 'y'
                )
            ),
            //プラグインコード
            1 => array(
                array(
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ),
                array(
                    'input' => 'テストプラグイン名',
                    'output' => 'Please enter Plugin Code (First letter is uppercase alphabet only. alphabet and numbers are allowed.)',
                ),
                array(
                    'input' => strtolower($code),
                    'output' => 'Please enter Plugin Code (First letter is uppercase alphabet only. alphabet and numbers are allowed.)',
                ),
                array(
                    'input' => $code,
                    'output' => 'Table name:',
                ),
            ),
            //Table name:
            2 => array(
                array(
                    'input' => $code,
                    'output' => array(
                        'No results have been found',
                        $yamlName
                    ),
                ),
                array(
                    'input' => $yamlName,
                    'output' => array(
                        'your entry list',
                        $yamlName
                    ),
                ),
                array(
                    'input' => '',
                ),
            ),
            //supportFlag
            3 => array(
                array(
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ),
                array(
                    'input' => 'a',
                    'output' => 'No results have been found',
                ),
                array(
                    'input' => 'y'
                ),
            ),
            //確認
            'confirm' => array(
                array(
                    'output' => array(
                        $yamlName
                    ),
                ),
                array(
                    'input' => 'y'
                ),
            )
        );
        $this->setTestCase($testCase);

        $commandArg = array(
            'command' => 'plugin:develop',
            'mode' => 'entity',
            '--no-ansi' => true,
        );

        $this->executeTester(array($this, 'checkQuestion'), $commandArg);

        //ファイルとフォルダー作成確認
        $ff = array(
            $codePath,
            $codePath . '/Entity',
            $codePath . '/Entity/' . ucfirst(strtolower($code)) . '.php',
            $codePath . '/Repository',
            $codePath . '/Repository/' . ucfirst(strtolower($code)) . 'Repository.php',
            $codePath . '/Resource',
            $codePath . '/Resource/doctrine',
            $codePath . '/Resource/doctrine/Plugin.' . $code . '.Entity.' . ucfirst(strtolower($code)) . '.dcm.yml',
            $codePath . '/Resource/doctrine/migration',
        );
        $this->checkFileAndFolder($ff);
        $this->removePluginDir($codePath);
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

    protected function createPluginDir($codePath, $code)
    {
        if (!is_dir($codePath)) {
            mkdir($codePath);
        }

        $codePath .= '/Resource';
        if (!is_dir($codePath)) {
            mkdir($codePath);
        }
        $codePath .= '/doctrine';
        if (!is_dir($codePath)) {
            mkdir($codePath);
        }
        $yamlPath = $codePath . '/Plugin.' . $code . '.Entity.' . ucfirst(strtolower($code)) . '.dcm.yml';
        if (!is_file($yamlPath)) {
            
        }
        if (!is_file($yamlPath)) {
            $body = $this->createYamlBody($code);
            file_put_contents($yamlPath, $body);
        }
    }

    protected function checkFileAndFolder($ff)
    {
        foreach ($ff as $path) {
            $msg = 'fail assert that a file/path exists.(' . $path . ')';
            $this->assertTrue(file_exists($path), $msg);
        }
    }

    private function createYamlBody($code)
    {
        $body = "Plugin\\" . $code . "\Entity\\" . ucfirst(strtolower($code)) . ":
    type: entity
    table: plg_" . strtolower($code) . "
    repositoryClass: Plugin\\" . $code . "\Repository\\" . ucfirst(strtolower($code)) . "Repository
    id:
        id:
            type: integer
            nullable: false
            unsigned: true
            id: true
            column: id
            generator:
                strategy: AUTO
    fields:
        contact_type:
            type: integer
            nullable: false
        contents:
            type: string
            nullable: false
        customer_id:
            type: integer
            nullable: true
            unsigned: true
        name_sei:
            type: string
            nullable: false
        name_mei:
            type: string
            nullable: false
        kana_sei:
            type: string
            nullable: false
        kana_mei:
            type: string
            nullable: false
        email:
            type: string
            nullable: false
        phone:
            type: string
            nullable: false
        zipcode:
            type: string
            nullable: true
        pref_id:
            type: integer
            nullable: true
        addr01:
            type: string
            nullable: true
        addr02:
            type: string
            nullable: true
        note:
            type: string
            nullable: true
        process_status:
            type: integer
            nullable: false
        create_datetime:
            type: datetime
            nullable: false
            options:
                default: '2000-01-01 00:00:00'
        update_datetime:
            type: datetime
            nullable: true
    lifecycleCallbacks: {  }
    ";
        return $body;
    }
}
