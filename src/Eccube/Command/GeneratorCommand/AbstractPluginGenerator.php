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

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\EntityRepositoryGenerator;
use Eccube\Application;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;

abstract class AbstractPluginGenerator
{

    const DEFAULT_NESTING_LEVEL = 100;
    const NEW_HOOK_VERSION = '3.0.9';
    const STOP_PROCESS = 'quit';
    const INPUT_OPEN = '[';
    const INPUT_CLOSE = ']';
    const PLUGIN_PREFIX = 'plg_';

    /**
     * app
     *
     * @var \Eccube\Application
     */
    protected $app;

    /**
     * QuestionHelper
     *
     * @var \Symfony\Component\Console\Helper\QuestionHelper
     */
    protected $dialog;

    /**
     * InputInterface
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * InputInterface
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * $paramList
     * @var array $paramList
     */
    protected $paramList;

    /**
     *
     * @var int
     */
    private $nestingLevel;

    /**
     * ヘッダー
     */
    abstract protected function getHeader();

    /**
     * start()
     */
    abstract protected function start();

    /**
     * フィルドーセット
     */
    abstract protected function initFieldSet();

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->nestingLevel = self::DEFAULT_NESTING_LEVEL;
    }

    /**
     *
     * @param \Symfony\Component\Console\Helper\QuestionHelper $dialog
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function init($dialog, $input, $output)
    {
        $this->dialog = $dialog;
        $this->input = $input;
        $this->output = $output;
        $this->initFieldSet();
    }

    public function run()
    {
        // ヘッダー部分
        $this->getHeader();

        foreach ($this->paramList as $paramKey => $params) {
            $value = $this->makeLineRequest($params);
            if ($value === false) {
                $this->exitGenerator();

                return;
            }
            $this->paramList[$paramKey]['value'] = $value;
        }

        $this->output->writeln('');
        $this->output->writeln('---Entry confirmation');
        foreach ($this->paramList as $paramKey => $params) {
            if (is_array($params['value'])) {
                $this->output->writeln($params['label']);
                foreach ($params['value'] as $keys => $val) {
                    $this->output->writeln('<info>  '.$keys.'</info>');
                }
            } else {
                if (isset($params['show'])) {
                    $disp = $params['show'][$params['value']];
                } else {
                    $disp = $params['value'];
                }
                $this->output->writeln($params['label'].' <info>'.$disp.'</info>');
            }
        }
        $this->output->writeln('');
        $Question = new Question('<comment>[confirm] Do you want to proceed? [y/n] : </comment>', '');
        $value = $this->dialog->ask($this->input, $this->output, $Question);
        if ($value != 'y') {
            $this->exitGenerator();

            return;
        }

        $this->start();
    }

    protected function exitGenerator($msg = 'Quitting Bye bye.')
    {
        $this->output->writeln($msg);
    }

    protected function makeLineRequest($params)
    {
        // nesting loop protection
        if ($this->getNestingLevel() < 0) {
            rewind($this->output->getStream());
            $display = stream_get_contents($this->output->getStream());
            throw new \Exception($display);
        }
        $this->nestingLevel--;

        $this->output->writeln($params['name']);
        $Question = new Question('<comment>Input'.self::INPUT_OPEN.$params['no'].self::INPUT_CLOSE.' : </comment>', '');
        $value = $this->dialog->ask($this->input, $this->output, $Question);
        $value = trim($value);
        if ($value === self::STOP_PROCESS) {
            return false;
        }
        foreach ($params['validation'] as $key => $row) {

            if ($key == 'isRequired' && $row == true) {
                if ($value === '' || strlen($value) == 0) {

                    $this->output->writeln('[!] Value cannot be empty.');

                    return $this->makeLineRequest($params);
                }
            } elseif ($key == 'pattern' && preg_match($row, $value) == false) {
                $this->output->writeln('<error>[!] Value is not valid.</error>');

                return $this->makeLineRequest($params);
            } elseif ($key == 'isCode') {

                if (in_array($value, $row)) {
                    $this->output->writeln('<error>[!] Plugin with this code already exists.</error>');

                    return $this->makeLineRequest($params);
                }
            } elseif ($key == 'isNotCode') {

                if (!in_array($value, $row)) {
                    $this->output->writeln('<error>[!] This plugin code does not exist.</error>');

                    return $this->makeLineRequest($params);
                }

            } elseif ($key == 'inArray' || $key == 'choice') {

                if (is_string($row)) {
                    $row = $this->$row();
                }
                if ($value == '') {
                    return $params['value'];
                }
                if (isset($row[$value])) {
                    if (!is_array($params['value'])) {
                        $value = $row[$value];
                        continue;
                    }
                    $params['value'][$value] = $row[$value];
                    $this->output->writeln('<info>--- your entry list</info>');
                    foreach ($params['value'] as $subKey => $node) {
                        $this->output->writeln('<info> - '.$subKey.'</info>');
                    }
                    $this->output->writeln('');
                    $this->output->writeln('--- Press Enter to move to the next step ---');

                    return $this->makeLineRequest($params);
                } else {
                    $searchList = array();
                    $max = 16;
                    foreach ($row as $eventKey => $eventConst) {
                        if (strpos($eventKey, $value) !== false || strpos($eventConst, $value) !== false) {
                            if (count($searchList) >= $max) {
                                $searchList['-- there are more then '.$max.''] = '';
                                break;
                            }
                            $searchList[$eventKey] = $eventConst;
                        }
                    }
                    $this->output->writeln('<error>[!] No results have been found</error>');
                    if (!empty($searchList)) {
                        $this->output->writeln('--- there are more then one search result');
                    }
                    foreach ($searchList as $subKey => $node) {
                        $this->output->writeln(' - '.$subKey);
                    }

                    if (!empty($searchList)) {
                        $this->output->writeln('');
                    }

                    return $this->makeLineRequest($params);
                }
            }
        }

        return $value;
    }

    protected function getNestingLevel()
    {
        return $this->nestingLevel;
    }

    protected function setNestingLevel($nestingLevel)
    {
        $this->nestingLevel = $nestingLevel;
    }


    /**
     * app/Plugin直下にあるディレクトリ名(プラグインコード)を取得
     *
     * @return array
     */
    protected function getPluginCodes()
    {
        $finder = new Finder();

        $finder->directories()->depth('== 0');

        $dirs = $finder->in($this->app['config']['root_dir'].'/app/Plugin/');

        $codes = array();
        foreach ($dirs as $item) {
            $codes[] = $item->getRelativePathname();
        }

        return $codes;
    }

    /**
     * Entity、Repositoryファイルの作成
     *
     * @param array $metadatas
     * @param array $fsList
     */
    protected function generateEntities(array $metadatas, array &$fsList)
    {
        /** @var ClassMetadataInfo $class */
        foreach ($metadatas as $class) {

            // Entity作成
            $EntityGenerator = new EntityGenerator();
            $EntityGenerator->setBackupExisting(false);
            $EntityGenerator->setClassToExtend('Eccube\\Entity\\AbstractEntity');
            $EntityGenerator->setGenerateAnnotations(false);
            $EntityGenerator->setRegenerateEntityIfExists(true);
            $EntityGenerator->setGenerateStubMethods(true);
            $EntityGenerator->setUpdateEntityIfExists(false);

            $appPath = $this->app['config']['root_dir'].'/app/';
            $EntityGenerator->generate(array($class), $appPath);

            $filename = $appPath.str_replace('\\', DIRECTORY_SEPARATOR, $class->name).'.php';
            if (is_file($filename)) {
                $fsList['file'][$filename] = true;
            } else {
                $fsList['file'][$filename] = false;
            }

            // Repository作成
            $RepositoryGenerator = new EntityRepositoryGenerator();
            $RepositoryGenerator->writeEntityRepositoryClass($class->customRepositoryClassName, $appPath);

            $filename = $appPath.str_replace('\\', DIRECTORY_SEPARATOR, $class->customRepositoryClassName).'.php';
            if (is_file($filename)) {
                $fsList['file'][$filename] = true;
            } else {
                $fsList['file'][$filename] = false;
            }
        }

    }


    /**
     * migraionファイルの作成
     *
     * @param array $metadatas
     * @param array $fsList
     * @param $pluginCode
     * @param $codePath
     */
    protected function generateMigration(array $metadatas, array &$fsList = array(), $pluginCode, $codePath)
    {
        if (count($metadatas)) {
            $migrationContent = $this->makeMigration($pluginCode, $metadatas);
            $date = date('YmdHis');
            $migrationContent = str_replace('[datetime]', $date, $migrationContent);
            $migPath = $codePath.'/Resource/doctrine/migration/Version'.$date.'.php';

            file_put_contents($migPath, $migrationContent);
            if (is_file($migPath)) {
                $fsList['file'][$migPath] = true;
            } else {
                $fsList['file'][$migPath] = false;
            }
        }
    }


    /**
     * migrationファイルの作成
     *
     * @param $pluginCode
     * @param array $metadatas
     * @return mixed|string
     */
    protected function makeMigration($pluginCode, array $metadatas)
    {
        if ($this->paramList['supportFlag']['value']) {
            $migrationFileCont = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/MigrationVersionSupport.php');
        } else {
            $migrationFileCont = file_get_contents($this->app['config']['root_dir'].'/src/Eccube/Command/GeneratorCommand/generatortemplate/MigrationVersion.php');
        }

        $migrationFileCont = str_replace('[code]', $pluginCode, $migrationFileCont);

        $entityList = array();
        foreach ($metadatas as $metadata) {
            $entityList[] = '        \''.$metadata->name.'\'';
        }

        $entityListStr = join(','.PHP_EOL, $entityList);
        $migrationFileCont = str_replace('[entityList]', $entityListStr, $migrationFileCont);
        if ($this->paramList['supportFlag']['value']) {
            $createParts = $this->makeCreateParts($metadatas);
            $tableNameArr = array();
            foreach ($createParts as $tableName => $tableArr) {
                $tableNameArr[] = '            $this->createTable'.$tableName.'($schema);';
            }
            $tableNameStr = join(PHP_EOL, $tableNameArr);
            $migrationFileCont = str_replace('[createTable]', $tableNameStr, $migrationFileCont);

            $createPartsStr = '';
            foreach ($createParts as $parts) {
                $createPartsStr .= join(PHP_EOL, $parts);
            }
            $migrationFileCont = str_replace('[createFunction]', $createPartsStr, $migrationFileCont);

            $dropParts = $this->makeDropParts($metadatas);
            $dropPartsStr = join(PHP_EOL, $dropParts);
            $migrationFileCont = str_replace('[dropTable]', $dropPartsStr, $migrationFileCont);
        }

        return $migrationFileCont;
    }


    protected function makeCreateParts($metadatas)
    {
        $ret = array();
        foreach ($metadatas as $metadata) {

            $nameFormated = Inflector::camelize($metadata->table['name']);
            $tmp = array();
            $tmp[] = '';
            $tmp[] = '    /**';
            $tmp[] = '     * @param Schema $schema';
            $tmp[] = '     */';
            $tmp[] = '    public function createTable'.ucfirst($nameFormated).'(Schema $schema)';
            $tmp[] = '    {';
            $tmp[] = '        $table = $schema->createTable(\''.$metadata->table['name'].'\');';
            $columns = $metadata->fieldMappings;
            foreach ($columns as $column) {

                $typeName = $column['type'];
                $tmp[] = '        $table->addColumn(\''.$column['columnName'].'\', \''.$typeName.'\', array(';
                $param = array();
                if (isset($column['nullable']) && $column['nullable']) {
                    $param['notnull'] = 'true';
                } else {
                    $param['notnull'] = 'false';
                }

                foreach ($param as $parKey => $parVal) {
                    $tmp[] = '            \''.$parKey.'\' => '.$parVal.',';
                }
                $tmp[] = '        ));';
            }


            $tmp[] = '    }';
            $tmp[] = '';
            $ret[ucfirst($nameFormated)] = $tmp;
        }

        return $ret;
    }

    protected function makeDropParts($metadatas)
    {
        $ret = array();
        foreach ($metadatas as $metadata) {
            $ret[] = '            $schema->dropTable(\''.$metadata->table['name'].'\');';
        }

        return $ret;
    }


    /**
     * メッセージ表示
     *
     * @param array $fsList
     */
    protected function completeMessage(array $fsList)
    {

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
        $this->output->writeln('[+]File system');
        if (!empty($dirFileOk)) {
            $this->output->writeln('');
            $this->output->writeln(' this files and folders were created.');
            foreach ($dirFileOk as $path) {
                $this->output->writeln('<info> - '.$path.'</info>');
            }
        }

        if (!empty($dirFileNg)) {
            $this->output->writeln('');
            $this->output->writeln(' this files and folders was not created.');
            foreach ($dirFileOk as $path) {
                $this->output->writeln('<error> - '.$path.'</error>');
            }
        }

    }
}
