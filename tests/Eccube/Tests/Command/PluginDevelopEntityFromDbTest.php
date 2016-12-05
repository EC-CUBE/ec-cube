<?php

namespace Eccube\Tests\Command;

use Guzzle\Http\Client;
use Eccube\Application;
use Eccube\Command\PluginCommand;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class PluginDevelopEntityFromDbTest extends AbstractCommandTest
{

    private $testCase;

    public function setUp()
    {
        parent::setUp();
        $this->initCommand(new PluginCommand());
    }

    public function testFirst()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
        return ;
        $code = 'PluginUnittestSample';

        $codePath = $this->app['config']['root_dir'] . '/app/Plugin/' . $code;
        $this->removePluginDir($codePath);
        
        $this->createTable($code);
        $this->dropTable($code);
        
        $testCase = array(
            //プラグイン名
            'entity' => array(
                array(
                    'input' => 'd',
                    'output' => 'プラグインコードを入力してください',
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
                    'output' => 'プラグインコードは英数字で1文字目は必ず半角英字の大文字で入力',
                ),
                array(
                    'input' => strtolower($code),
                    'output' => 'プラグインコードは英数字で1文字目は必ず半角英字の大文字で入力',
                ),
                array(
                    'input' => $code,
                    'output' => 'バージョン',
                ),
            ),
            //バージョン
            3 => array(
                array(
                    'input' => '',
                    'output' => '入力されていません',
                ),
                array(
                    'input' => 'ひらがな',
                    'output' => '有効な値ではありません',
                ),
                array(
                    'input' => 'alphabet',
                    'output' => '有効な値ではありません',
                ),
                array(
                    'input' => '1.3.4',
                    'output' => '作成者名',
                )
            ),
            //バージョン
            4 => array(
                array(
                    'input' => '',
                    'output' => '入力されていません',
                ),
                array(
                    'input' => '作成者名',
                    'output' => 'サーホットバージョン',
                ),
            ),
            //サーポットバージョン
            5 => array(
                array(
                    'input' => '',
                    'output' => '入力されていません',
                ),
                array(
                    'input' => 'a',
                    'output' => '入力値は正しくありません',
                ),
                array(
                    'input' => 'y',
                    'output' => 'サイト共通イベント',
                ),
            ),
            //サイト共通イベント
            6 => array(
                array(
                    'input' => 'entry',
                    'output' => array(
                        '入力値は正しくありません',
                        'eccube.event.render.entry.before'
                    ),
                ),
                array(
                    'input' => 'eccube.event.render.entry.before',
                    'output' => array(
                        '現在リスト',
                        'eccube.event.render.entry.before'
                    ),
                ),
                array(
                    'input' => 'product_list',
                    'output' => array(
                        '入力値は正しくありません',
                        'eccube.event.render.product_list.before'
                    ),
                ),
                array(
                    'input' => 'eccube.event.render.product_list.before',
                    'output' => array(
                        '現在リスト',
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
                        '入力値は正しくありません',
                        'front.entry.index.initialize'
                    ),
                ),
                array(
                    'input' => 'front.entry.index.initialize',
                    'output' => array(
                        '現在リスト',
                        'front.entry.index.initialize'
                    ),
                ),
                array(
                    'input' => 'change_password',
                    'output' => array(
                        '入力値は正しくありません',
                        'admin.admin.change_password.complete'
                    ),
                ),
                array(
                    'input' => 'admin.admin.change_password.complete',
                    'output' => array(
                        '現在リスト',
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
                        'テストプラグイン名',
                        $code,
                        '1.3.4',
                        '作成者名',
                        'Yes',
                        'eccube.event.render.entry.before',
                        'eccube.event.render.product_list.before',
                        'front.entry.index.initialize',
                        'admin.admin.change_password.complete',
                    ),
                ),
                array(
                    'input' => 'y',
                    'output' => 'ysadsad',
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
    
    private function dropTable($code){
        /* @var $entityManager \Doctrine\ORM\EntityManager  */
        $entityManager = $this->app['orm.em'];
        $schema = $entityManager->getConnection()->getSchemaManager()->createSchema();
        $toSchema = clone $schema;
        
        $toSchema->dropTable('plg_'. strtolower($code));
        $platform = $entityManager->getConnection()->getDatabasePlatform();
        $queries = $schema->getMigrateToSql($toSchema, $platform);
        
        if(is_array($queries)){
            foreach($queries as $query){
                $entityManager->getConnection()->executeQuery($query);
            }
        }
        
    }
    private function createTable($code){
        
        $schema = new \Doctrine\DBAL\Schema\Schema();
        $table = $schema->createTable('plg_'. strtolower($code));
        $table->addColumn('id', 'integer', array(
            'notnull' => true,
            'unsigned' => true,
            'autoincrement' => true,
            'comment' => '問合せID',
        ));
        $table->addColumn('contact_type', 'integer', array(
            'notnull' => true,
            'comment' => '問合せ種別',
        ));
        $table->addColumn('contents', 'string', array(
            'notnull' => true,
            'comment' => '問合せ内容',
            'length' => '4096',
        ));
        $table->addColumn('customer_id', 'integer', array(
            'notnull' => false,
            'unsigned' => true,
            'comment' => 'M顧客ID',
        ));
        $table->addColumn('name_sei', 'string', array(
            'notnull' => true,
            'comment' => '名前（姓）',
            'length' => '64',
        ));
        $table->addColumn('name_mei', 'string', array(
            'notnull' => true,
            'comment' => '名前（名）',
            'length' => '64',
        ));
        $table->addColumn('kana_sei', 'string', array(
            'notnull' => true,
            'comment' => 'カナ（姓）',
            'length' => '64',
        ));
        $table->addColumn('kana_mei', 'string', array(
            'notnull' => true,
            'comment' => 'カナ（名）',
            'length' => '64',
        ));
        $table->addColumn('email', 'string', array(
            'notnull' => true,
            'comment' => 'メールアドレス',
            'length' => '256',
        ));
        $table->addColumn('phone', 'string', array(
            'notnull' => true,
            'comment' => '電話番号',
            'length' => '16',
        ));
        $table->addColumn('zipcode', 'string', array(
            'notnull' => false,
            'comment' => '郵便番号',
            'length' => '7',
        ));
        $table->addColumn('pref_id', 'integer', array(
            'notnull' => false,
            'comment' => '都道府県ID',
        ));
        $table->addColumn('addr01', 'string', array(
            'notnull' => false,
            'comment' => '住所01',
            'length' => '128',
        ));
        $table->addColumn('addr02', 'string', array(
            'notnull' => false,
            'comment' => '住所02',
            'length' => '128',
        ));
        $table->addColumn('note', 'string', array(
            'notnull' => false,
            'comment' => '備考',
            'length' => '1024',
        ));
        $table->addColumn('process_status', 'integer', array(
            'notnull' => true,
            'comment' => '対応状況(ステータス)',
        ));
        $table->addColumn('create_datetime', 'datetime', array(
            'notnull' => true,
            'default' => '2000-01-01 00:00:00',
            'comment' => '作成日付',
        ));
        $table->addColumn('update_datetime', 'datetime', array(
            'notnull' => false,
            'comment' => '更新日付',
        ));

        $table->setPrimaryKey(array('id'));
        /* @var $entityManager \Doctrine\ORM\EntityManager  */
        $entityManager = $this->app['orm.em'];
        $platform = $entityManager->getConnection()->getDatabasePlatform();
        $queries = $schema->toSql($platform);
        
        if(is_array($queries)){
            foreach($queries as $query){
                $entityManager->getConnection()->executeQuery($query);
            }
        }
    }
    
}
