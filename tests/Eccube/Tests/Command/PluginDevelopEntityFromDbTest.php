<?php

namespace Eccube\Tests\Command;

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
        $this->createPluginDir($codePath);

        $this->dropTable($code);
        $this->createTable($code);
        $tableName = 'plg_' . strtolower($code);

        $testCase = array(
            //プラグイン名
            'entity' => array(
                array(
                    'input' => 'd',
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
                    'input' => 'plg_',
                    'output' => array(
                        'No results have been found',
                        $tableName
                    ),
                ),
                array(
                    'input' => $tableName,
                    'output' => array(
                        'your entry list',
                        $tableName
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
                        $tableName
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

    protected function createPluginDir($codePath)
    {
        if (!is_dir($codePath)) {
            mkdir($codePath);
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

    private function dropTable($code)
    {

        $tableName = 'plg_' . strtolower($code);

        /* @var $entityManager \Doctrine\ORM\EntityManager  */
        $entityManager = $this->app['orm.em'];
        /* @var $schema \Doctrine\DBAL\Schema\Schema  */
        $schema = $entityManager->getConnection()->getSchemaManager()->createSchema();
        $dbName = $schema->getName();
        $tableNames = array_flip($schema->getTableNames());

        if (!isset($tableNames[$dbName . '.' . $tableName])) {
            return;
        }

        $toSchema = clone $schema;

        $toSchema->dropTable($tableName);
        $platform = $entityManager->getConnection()->getDatabasePlatform();
        $queries = $schema->getMigrateToSql($toSchema, $platform);

        if (is_array($queries)) {
            foreach ($queries as $query) {
                $entityManager->getConnection()->executeQuery($query);
            }
        }
    }

    private function createTable($code)
    {

        $schema = new \Doctrine\DBAL\Schema\Schema();
        $table = $schema->createTable('plg_' . strtolower($code));
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

        if (is_array($queries)) {
            foreach ($queries as $query) {
                $entityManager->getConnection()->executeQuery($query);
            }
        }
    }

    public function tearDown()
    {

        $this->app->initDoctrine();

    }
}
