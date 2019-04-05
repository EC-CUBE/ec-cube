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

class PluginDevelopEntityFromDbTest extends AbstractCommandTest
{
    public static function setUpBeforeClass()
    {
        self::markTestIncomplete();
    }

    private $testCase;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();

        $this->markTestIncomplete();

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
        $this->createPluginDir($codePath);

        $this->dropTable($code);
        $this->createTable($code);
        $tableName = 'plg_'.strtolower($code);

        $testCase = [
            //プラグイン名
            'entity' => [
                [
                    'input' => 'd',
                ],
            ],
            //プラグインコード
            1 => [
                [
                    'input' => '',
                    'output' => 'Value cannot be empty',
                ],
                [
                    'input' => 'テストプラグイン名',
                    'output' => 'Please enter Plugin Code (First letter is uppercase alphabet only. alphabet and numbers are allowed.)',
                ],
                [
                    'input' => strtolower($code),
                    'output' => 'Please enter Plugin Code (First letter is uppercase alphabet only. alphabet and numbers are allowed.)',
                ],
                [
                    'input' => $code,
                    'output' => 'Table name:',
                ],
            ],
            //Table name:
            2 => [
                [
                    'input' => 'plg_',
                    'output' => [
                        'No results have been found',
                        $tableName,
                    ],
                ],
                [
                    'input' => $tableName,
                    'output' => [
                        'your entry list',
                        $tableName,
                    ],
                ],
                [
                    'input' => '',
                ],
            ],
            //supportFlag
            3 => [
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
                ],
            ],
            //確認
            'confirm' => [
                [
                    'output' => [
                        $tableName,
                    ],
                ],
                [
                    'input' => 'y',
                ],
            ],
        ];
        $this->setTestCase($testCase);

        $commandArg = [
            'command' => 'plugin:develop',
            'mode' => 'entity',
            '--no-ansi' => true,
        ];

        $this->executeTester([$this, 'checkQuestion'], $commandArg);

        //ファイルとフォルダー作成確認
        $ff = [
            $codePath,
            $codePath.'/Entity',
            $codePath.'/Entity/'.ucfirst(strtolower($code)).'.php',
            $codePath.'/Repository',
            $codePath.'/Repository/'.ucfirst(strtolower($code)).'Repository.php',
            $codePath.'/Resource',
            $codePath.'/Resource/doctrine',
            $codePath.'/Resource/doctrine/Plugin.'.$code.'.Entity.'.ucfirst(strtolower($code)).'.dcm.yml',
            $codePath.'/Resource/doctrine/migration',
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

    private function dropTable($code)
    {
        $tableName = 'plg_'.strtolower($code);

        /* @var $entityManager \Doctrine\ORM\EntityManager  */
        $entityManager = $this->app['orm.em'];
        /* @var $schema \Doctrine\DBAL\Schema\Schema  */
        $schema = $entityManager->getConnection()->getSchemaManager()->createSchema();
        $dbName = $schema->getName();
        $tableNames = array_flip($schema->getTableNames());

        if (!isset($tableNames[$dbName.'.'.$tableName])) {
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
        $table = $schema->createTable('plg_'.strtolower($code));
        $table->addColumn('id', 'integer', [
            'notnull' => true,
            'unsigned' => true,
            'autoincrement' => true,
            'comment' => '問合せID',
        ]);
        $table->addColumn('contact_type', 'integer', [
            'notnull' => true,
            'comment' => '問合せ種別',
        ]);
        $table->addColumn('contents', 'string', [
            'notnull' => true,
            'comment' => '問合せ内容',
            'length' => '4096',
        ]);
        $table->addColumn('customer_id', 'integer', [
            'notnull' => false,
            'unsigned' => true,
            'comment' => 'M顧客ID',
        ]);
        $table->addColumn('name_sei', 'string', [
            'notnull' => true,
            'comment' => '名前（姓）',
            'length' => '64',
        ]);
        $table->addColumn('name_mei', 'string', [
            'notnull' => true,
            'comment' => '名前（名）',
            'length' => '64',
        ]);
        $table->addColumn('kana_sei', 'string', [
            'notnull' => true,
            'comment' => 'カナ（姓）',
            'length' => '64',
        ]);
        $table->addColumn('kana_mei', 'string', [
            'notnull' => true,
            'comment' => 'カナ（名）',
            'length' => '64',
        ]);
        $table->addColumn('email', 'string', [
            'notnull' => true,
            'comment' => 'メールアドレス',
            'length' => '256',
        ]);
        $table->addColumn('phone', 'string', [
            'notnull' => true,
            'comment' => '電話番号',
            'length' => '16',
        ]);
        $table->addColumn('postal_code', 'string', [
            'notnull' => false,
            'comment' => '郵便番号',
            'length' => '8',
        ]);
        $table->addColumn('pref_id', 'integer', [
            'notnull' => false,
            'comment' => '都道府県ID',
        ]);
        $table->addColumn('addr01', 'string', [
            'notnull' => false,
            'comment' => '住所01',
            'length' => '128',
        ]);
        $table->addColumn('addr02', 'string', [
            'notnull' => false,
            'comment' => '住所02',
            'length' => '128',
        ]);
        $table->addColumn('note', 'string', [
            'notnull' => false,
            'comment' => '備考',
            'length' => '1024',
        ]);
        $table->addColumn('process_status', 'integer', [
            'notnull' => true,
            'comment' => '対応状況(ステータス)',
        ]);
        $table->addColumn('create_datetime', 'datetime', [
            'notnull' => true,
            'default' => '2000-01-01 00:00:00',
            'comment' => '作成日付',
        ]);
        $table->addColumn('update_datetime', 'datetime', [
            'notnull' => false,
            'comment' => '更新日付',
        ]);

        $table->setPrimaryKey(['id']);
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
