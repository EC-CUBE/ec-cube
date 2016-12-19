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

use Symfony\Component\Yaml\Yaml;
use Eccube\Command\PluginCommand\AbstractPluginGenerator;

class EntityFromDbGenerator extends AbstractPluginGenerator
{

    const PLUGIN_PREFIX = 'plg_';

    /**
     * テーブルリスト
     * @var array
     */
    private $tableList = null;

    protected function getHeader()
    {
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('---Plugin Generator for Entity');
        $this->output->writeln('---[*] You need to create table schema first.');
        $this->output->writeln('---[*]You can exit from Console Application, by typing ' . self::STOP_PROCESS . ' instead of typing another word.');
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('');
    }

    protected function start()
    {
        $fsList = array(
            'dir' => array(),
            'file' => array(),
        );

        $pluginCode = $this->paramList['pluginCode']['value'];
        $tableList = $this->paramList['tableList']['value'];
        $codePath = $this->app['config']['root_dir'] . '/app/Plugin/' . $pluginCode;

        $dirList = array('Entity', 'Repository', 'Resource', 'Resource/doctrine', '/Resource/doctrine/migration');
        foreach ($dirList as $dirName) {
            $dirPath = $codePath . '/' . $dirName;
            if (!is_dir($dirPath)) {
                mkdir($dirPath);
            }
            if (is_dir($dirPath)) {
                $fsList['dir'][$dirPath] = true;
            } else {
                $fsList['dir'][$dirPath] = false;
            }
        }
        $entityInfoList = array();
        $SchemaManager = $this->app['orm.em']->getConnection()->getSchemaManager();
        $migration = array();
        foreach ($SchemaManager->listTables() as $Table) {
            foreach ($tableList as $tableName) {
                if ($tableName == $Table->getName()) {
                    $entityInfoList[] = $this->makeEntity($pluginCode, $Table);
                    $migration[] = $Table;
                }
            }
        }
        if (count($migration)) {
            $migrationContent = $this->makeMigration($pluginCode, $migration);
            $timeSt = date('YmdHis');
            $migrationContent = str_replace('[datetime]', $timeSt, $migrationContent);
            $path = '/Resource/doctrine/migration/Version' . $timeSt . '.php';
            $entityInfoList[] = array($path => $migrationContent);
        }

        foreach ($entityInfoList as $entityInfo) {
            foreach ($entityInfo as $path => $body) {
                $fullPath = $this->app['config']['root_dir'] . '/app/Plugin/' . $pluginCode . $path;
                file_put_contents($fullPath, $body);
                if (is_file($fullPath)) {
                    $fsList['file'][$fullPath] = true;
                } else {
                    $fsList['file'][$fullPath] = false;
                }
            }
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
        $this->output->writeln('[+]file system');
        if (!empty($dirFileOk)) {
            $this->output->writeln('');
            $this->output->writeln(' this files and folders were created.');
            foreach ($dirFileOk as $path) {
                $this->output->writeln('<info> - ' . $path . '</info>');
            }
        }
    }

    private function makeEntity($pluginCode, $TableInfo)
    {
        $tableName = str_replace(self::PLUGIN_PREFIX, '', $TableInfo->getName());
        $nameFormated = ucfirst($this->toCamelCase($tableName));

        $ret = array();
        $ret['/Entity/' . $nameFormated . '.php'] = $this->getSrc($pluginCode, $TableInfo);

        $ret['/Resource/doctrine/Plugin.' . $pluginCode . '.Entity.' . $nameFormated . '.dcm.yml'] = $this->getConfig($pluginCode, $TableInfo);

        $ret['/Repository/' . $nameFormated . 'Repository.php'] = $this->getRepo($pluginCode, $TableInfo);

        return $ret;
    }

    protected function initFildset()
    {
        $this->paramList = array(
            'pluginCode' => array(
                'no' => 1,
                'label' => '[+]Plugin Code: ',
                'value' => null,
                'name' => '[+]Please enter Plugin Code (only pascal case letters numbers are allowed)',
                'validation' => array(
                    'isRequired' => true,
                    'inArray' => $this->getPluginList()
                )
            ),
            'tableList' => array(
                'no' => 2,
                'label' => '[+]Table name: ',
                'value' => array(),
                'name' => '[+]Please enter table name',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => $this->getTableList()
                )
            ),
            'supportFlag' => array(
                'no' => 3,
                'label' => '[+]Old version support: ',
                'value' => null,
                'name' => '[+]Do you want to support old versions too? [y/n]',
                'show' => array(
                    1 => 'Yes', 0 => 'No'),
                'validation' => array(
                    'isRequired' => true,
                    'choice' => array('y' => 1, 'n' => 0)
                )
            )
        );
    }

    private function getTableList()
    {
        if ($this->tableList === null) {
            $this->tableList = array();
            $SchemaManager = $this->app['orm.em']->getConnection()->getSchemaManager();
            foreach ($SchemaManager->listTables() as $Table) {
                $tableName = $Table->getName();
                if (strpos($tableName, self::PLUGIN_PREFIX) !== 0) {
                    continue;
                }
                $this->tableList[$tableName] = $tableName;
            }
        }

        return $this->tableList;
    }

    private function getPluginList()
    {
        $ret = array();
        $pluginDir = $this->app['config']['root_dir'] . '/app/Plugin';
        $iterator = new \DirectoryIterator($pluginDir);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                $ret[$fileInfo->getFilename()] = $fileInfo->getFilename();
            }
        }
        
        return $ret;
    }

    private function toCamelCase($name)
    {
        return lcfirst(implode('', array_map(function ($name) {
                    return ucfirst($name);
                }, explode('_', $name))));
    }

    private function getSrc($pluginCode, $TableInfo)
    {
        $nameFormated = $this->getShortClassName($TableInfo->getName());

        $ret = join(PHP_EOL, $this->getTopPart($pluginCode, $nameFormated));

        $indexes = $TableInfo->getindexes();

        $primaryKey = null;
        foreach ($indexes as $index) {
            if ($index->isPrimary()) {
                $tmpCol = $index->getColumns();
                foreach ($tmpCol as $colName) {
                    $primaryKey = $colName;
                    break;
                }
                break;
            }
        }

        $columns = $TableInfo->getColumns();

        $prop = array();
        $getMethods = array();
        $setMethods = array();
        foreach ($columns as $column) {

            if ($primaryKey == $column->getName()) {
                $columnName = 'id';
            } else {
                $columnName = $column->getName();
            }

            $varName = $column->getType()->getName();
            if ($varName == 'text') {
                $varName = 'string';
            }

            $tmpProp = '' . PHP_EOL;
            $tmpProp .= '    /**' . PHP_EOL;
            $tmpProp .= '     * @var ' . $varName . PHP_EOL;
            $tmpProp .= '     */' . PHP_EOL;
            $tmpProp .= '    private $' . $columnName . ';' . PHP_EOL;

            $prop[] = $tmpProp;

            $tmpGetMethName = $this->toCamelCase('get_' . $columnName);

            $tmpGet = '' . PHP_EOL;
            $tmpGet .= '    /**' . PHP_EOL;
            $tmpGet .= '     * Get ' . $columnName . PHP_EOL;
            $tmpGet .= '     *' . PHP_EOL;
            $tmpGet .= '     * @return ' . $varName . PHP_EOL;
            $tmpGet .= '     */' . PHP_EOL;
            $tmpGet .= '    public function ' . $tmpGetMethName . '()' . PHP_EOL;
            $tmpGet .= '    {' . PHP_EOL;
            $tmpGet .= '        return $this->' . $columnName . ';' . PHP_EOL;
            $tmpGet .= '    }' . PHP_EOL;

            $getMethods[] = $tmpGet;

            $tmpSetMethName = $this->toCamelCase('set_' . $columnName);

            $tmpSet = '' . PHP_EOL;
            $tmpSet .= '    /**' . PHP_EOL;
            $tmpSet .= '     * Set ' . $columnName . PHP_EOL;
            $tmpSet .= '     *' . PHP_EOL;
            $tmpSet .= '     * @param  ' . $varName . ' $' . $columnName . PHP_EOL;
            $tmpSet .= '     * @return ' . $nameFormated . PHP_EOL;
            $tmpSet .= '     */' . PHP_EOL;
            $tmpSet .= '    public function ' . $tmpSetMethName . '( $' . $columnName . ')' . PHP_EOL;
            $tmpSet .= '    {' . PHP_EOL;
            $tmpSet .= '        $this->' . $columnName . ' = $' . $columnName . ';' . PHP_EOL;
            $tmpSet .= '        return $this;' . PHP_EOL;
            $tmpSet .= '    }' . PHP_EOL;
            $setMethods[] = $tmpSet;
        }
        $ret .= PHP_EOL;
        $ret .= join(PHP_EOL, $prop);
        $ret .= PHP_EOL;
        $ret .= join(PHP_EOL, $getMethods);
        $ret .= PHP_EOL;
        $ret .= join(PHP_EOL, $setMethods);
        $ret .= PHP_EOL;
        $ret .= '}' . PHP_EOL;


        return $ret;
    }

    private function getTopPart($pluginCode, $nameFormated)
    {
        $line = array();
        $line[] = '<?php';
        $line[] = '/*';
        $line[] = ' * This file is part of ' . $pluginCode;
        $line[] = ' *';
        $line[] = ' *';
        $line[] = ' * For the full copyright and license information, please view the LICENSE';
        $line[] = ' * file that was distributed with this source code.';
        $line[] = ' */';
        $line[] = '';
        $line[] = 'namespace Plugin\\' . $pluginCode . '\Entity;';
        $line[] = '';
        $line[] = 'class ' . ucfirst($nameFormated) . ' extends \Eccube\Entity\AbstractEntity';
        $line[] = '{';
        $line[] = '    /**';
        $line[] = '     * @return string';
        $line[] = '     */';
        $line[] = '    public function __toString()';
        $line[] = '    {';
        $line[] = '        return $this->getMethod();';
        $line[] = '    }';

        return $line;
    }

    private function getConfig($pluginCode, $TableInfo)
    {
        $nameFormated = $this->getShortClassName($TableInfo->getName());

        $indexes = $TableInfo->getindexes();

        $primaryKey = null;
        foreach ($indexes as $index) {
            if ($index->isPrimary()) {
                $tmpCol = $index->getColumns();
                foreach ($tmpCol as $colName) {
                    $primaryKey = $colName;
                    break;
                }
                break;
            }
        }

        $id = array(
            'type' => null,
            'nullable' => null,
            'unsigned' => null,
            'id' => true,
            'column' => $primaryKey,
            'generator' => array(
                'strategy' => 'AUTO'
            )
        );

        $fields = array();
        $columns = $TableInfo->getColumns();
        foreach ($columns as $column) {
            $colName = $column->getName();
            $type = $column->getType()->getName();
            if ($type == 'boolean') {
                $type = 'integer';
            }

            if ($column->getNotNull()) {
                $nullable = false;
            } else {
                $nullable = true;
            }
            $unsigned = $column->getUnsigned();
            $defaultValue = $column->getDefault();
            if ($colName == $primaryKey) {
                $id['type'] = $type;
                $id['nullable'] = $nullable;
                $id['unsigned'] = $unsigned;
            } else {
                $tmp = array(
                    'type' => $type,
                    'nullable' => $nullable
                );
                if ($unsigned) {
                    $tmp['unsigned'] = true;
                }
                if ($defaultValue !== null) {
                    $tmp['options'] = array(
                        'default' => $defaultValue
                    );
                }
                if ($type == 'decimal') {
                    $tmp['precision'] = $column->getPrecision();
                    $tmp['scale'] = $column->getScale();
                }
                $fields[$colName] = $tmp;
            }
        }

        $yml = array(
            'Plugin\\' . $pluginCode . '\Entity\\' . ucfirst($nameFormated) => array(
                'type' => 'entity',
                'table' => $TableInfo->getName(),
                'repositoryClass' => 'Plugin\\' . $pluginCode . '\Repository\\' . ucfirst($nameFormated) . 'Repository',
                'id' => array(
                    'id' => $id
                ),
                'fields' => $fields,
                'lifecycleCallbacks' => array()
            )
        );
        return Yaml::dump($yml, 10);
    }

    private function getRepo($pluginCode, $TableInfo)
    {
        $nameFormated = $this->getShortClassName($TableInfo->getName());

        $line = array();
        $line[] = '<?php';
        $line[] = '/*';
        $line[] = ' * This file is part of ' . $pluginCode;
        $line[] = ' *';
        $line[] = ' *';
        $line[] = ' * For the full copyright and license information, please view the LICENSE';
        $line[] = ' * file that was distributed with this source code.';
        $line[] = ' */';
        $line[] = '';
        $line[] = 'namespace Plugin\\' . $pluginCode . '\Repository;';
        $line[] = '';
        $line[] = 'use Doctrine\ORM\EntityRepository;';
        $line[] = '';
        $line[] = '/**';
        $line[] = ' * ' . ucfirst($nameFormated);
        $line[] = ' *';
        $line[] = ' * This class was generated by the Doctrine ORM. Add your own custom';
        $line[] = ' * repository methods below.';
        $line[] = ' */';
        $line[] = 'class ' . ucfirst($nameFormated) . 'Repository extends EntityRepository';
        $line[] = '{';
        $line[] = '';
        $line[] = '}';

        return join(PHP_EOL, $line);
    }

    private function makeMigration($pluginCode, $migration)
    {
        if ($this->paramList['supportFlag']['value']) {
            $migrationFileCont = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/MigrationVersionSupport.php');
        } else {
            $migrationFileCont = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/MigrationVersion.php');
        }

        $entityList = $this->createEntityList($pluginCode, $migration);

        $entityListStr = join(',' . PHP_EOL, $entityList);
        $migrationFileCont = str_replace('[entityList]', $entityListStr, $migrationFileCont);

        if ($this->paramList['supportFlag']['value']) {
            $createParts = $this->makeCreateParts($migration);
            $tableNameArr = array();
            foreach ($createParts as $tableName => $tableArr) {
                $tableNameArr[] = '            $this->createTable' . $tableName . '($schema);';
            }
            $tableNameStr = join(PHP_EOL, $tableNameArr);
            $migrationFileCont = str_replace('[createTable]', $tableNameStr, $migrationFileCont);

            $createPartsStr = '';
            foreach ($createParts as $parts) {
                $createPartsStr .= join(PHP_EOL, $parts);
            }
            $migrationFileCont = str_replace('[createFunction]', $createPartsStr, $migrationFileCont);

            $dropParts = $this->makeDropParts($migration);
            $dropPartsStr = join(PHP_EOL, $dropParts);
            $migrationFileCont = str_replace('[dropTable]', $dropPartsStr, $migrationFileCont);
        }

        return $migrationFileCont;
    }

    private function createEntityList($pluginCode, $migration)
    {
        $ret = array();
        foreach ($migration as $TableInfo) {
            $ret[] = "        '" . 'Plugin\\' . $pluginCode . '\Entity\\' . ucfirst($this->getShortClassName($TableInfo->getName())) . "'";
        }
        return $ret;
    }

    private function getShortClassName($dbTableName)
    {
        return $this->toCamelCase(str_replace(self ::PLUGIN_PREFIX, '', $dbTableName));
    }

    private function makeCreateParts($migration)
    {
        $ret = array();
        foreach ($migration as $TableInfo) {
            $nameFormated = $this->getShortClassName($TableInfo->getName());
            $tmp = array();
            $tmp[] = PHP_EOL;
            $tmp[] = '    public function createTable' . ucfirst($nameFormated) . '(Schema $schema)';
            $tmp[] = '    {';
            $tmp[] = '        $table = $schema->createTable(\'' . $TableInfo->getName() . '\');';
            $columns = $TableInfo->getColumns();
            foreach ($columns as $column) {

                $typeName = $column->getType()->getName();
                $tmp[] = '        $table->addColumn(\'' . $column->getName() . '\', \'' . $typeName . '\', array(';
                $param = array();
                if ($column->getNotNull()) {
                    $param['notnull'] = 'true';
                } else {
                    $param['notnull'] = 'false';
                }
                if ($column->getUnsigned()) {
                    $param['unsigned'] = 'true';
                }
                if ($column->getDefault()) {
                    $param['default'] = '\'' . $column->getDefault() . '\'';
                }
                if ($column->getAutoincrement()) {
                    $param['autoincrement'] = 'true';
                }
                if ($column->getComment()) {
                    $param['comment'] = '\'' . str_replace('\'', '\\\'', $column->getComment()) . '\'';
                }
                if ($column->getLength()) {
                    $param['length'] = '\'' . $column->getLength() . '\'';
                }

                if ($typeName == 'decimal') {
                    $param['precision'] = $column->getPrecision();
                    $param['scale'] = $column->getScale();
                }
                foreach ($param as $parKey => $parVal) {
                    $tmp[] = '            \'' . $parKey . '\' => ' . $parVal . ',';
                }
                $tmp[] = '        ));';
            }
            $indexes = $TableInfo->getindexes();

            foreach ($indexes as $index) {
                if ($index->isPrimary()) {
                    $tmpCol = $index->getColumns();
                    foreach ($tmpCol as $colName) {
                        $tmp[] = '        $table->setPrimaryKey(array(\'' . $colName . '\'));';
                        break;
                    }
                } else {
                    $tmp[] = '        $columnNames = array();';
                    foreach ($index->getColumns() as $IdentName) {
                        $tmp[] = '        $columnNames[] = \'' . $IdentName . '\';';
                    }
                    $tmp[] = '        $indexName = \'' . $index->getName() . '\';';
                    $tmp[] = '        $options = ' . var_export($index->getOptions(), true) . ';';
                    if ($index->isUnique()) {
                        $tmp[] = '        $table->addUniqueIndex($columnNames, $indexName, $options);';
                    } else {
                        $tmp[] = '        $flags = ' . var_export($index->getFlags(), true) . ';';
                        $tmp[] = '        $table->addIndex($columnNames, $indexName,$flags, $options);';
                    }
                }
            }

            $tmp[] = '    }';
            $ret[ucfirst($nameFormated)] = $tmp;
        }

        return $ret;
    }

    private function makeDropParts($migration)
    {
        $ret = array();
        foreach ($migration as $TableInfo) {
            $ret[] = '            $schema->dropTable(\'' . $TableInfo->getName() . '\');';
        }

        return $ret;
    }
}
