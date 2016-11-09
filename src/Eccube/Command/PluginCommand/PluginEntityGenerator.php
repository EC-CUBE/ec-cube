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
use Eccube\Command\PluginCommand\AbstractGenerator;

class PluginEntityGenerator extends AbstractGenerator
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
        $this->output->writeln('---プラグインEntityジェネレータ');
        $this->output->writeln('---※先にテーブルを作成が必要です');
        $this->output->writeln('---※プログラムを終了するには' . self::STOP_PROCESS . 'を入力してください');
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('');
    }

    protected function start($paramList)
    {
        $fsList = array(
            'dir' => array(),
            'file' => array(),
        );

        $pluginCode = $paramList['pluginCode']['value'];
        $tableList = $paramList['tableList']['value'];
        $codePath = $this->app['config']['root_dir'] . '/app/Plugin/' . $pluginCode;

        $dirList = array('Entity', 'Migration', 'Repository', 'Resource', 'Resource/doctrine');
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
        foreach ($SchemaManager->listTables() as $Table) {
            foreach ($tableList as $tableName) {
                if ($tableName == $Table->getName()) {
                    $entityInfoList[] = $this->makeEntity($pluginCode, $Table);
                }
            }
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
        $this->output->writeln('■ファイルシステム');
        if (!empty($dirFileOk)) {
            $this->output->writeln('');
            $this->output->writeln('  以下のファイルとフォルダーを作成しました');
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

        $ret['/Migration/create_' . $nameFormated . '.php'] = $this->getMigration($TableInfo);

        return $ret;
    }

    protected function getFildset()
    {
        return array(
            'pluginCode' => array(
                'label' => '■プラグインコード: ',
                'value' => null,
                'name' => '■プラグインコードを入力してください',
                'validation' => array(
                    'isRequired' => true,
                    'inArray' => $this->getPluginList()
                )
            ),
            'tableList' => array(
                'label' => '■テーブル名: ',
                'value' => array(),
                'name' => '■テーブル名を入力してください',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => $this->getTableList()
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
        $table_name = str_replace(self ::PLUGIN_PREFIX, '', $TableInfo->getName());
        $nameFormated = $this->toCamelCase($table_name);

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
        $table_name = str_replace(self ::PLUGIN_PREFIX, '', $TableInfo->getName());
        $nameFormated = $this->toCamelCase($table_name);

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
        $table_name = str_replace(self::PLUGIN_PREFIX, '', $TableInfo->getName());
        $nameFormated = $this->toCamelCase($table_name);

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

    private function getMigration($TableInfo)
    {
        $table_name = str_replace(self::PLUGIN_PREFIX, '', $TableInfo->getName());
        $nameFormated = $this->toCamelCase($table_name);

        $ret = array();
        $line[] = '<?php';
        $line[] = '';
        $ret[] = ' function createTable' . ucfirst($nameFormated) . '(Schema $schema)';
        $ret[] = '    {';
        $ret[] = '        $table = $schema->createTable(\'' . $TableInfo->getName() . '\');';
        $columns = $TableInfo->getColumns();
        foreach ($columns as $column) {

            $typeName = $column->getType()->getName();
            $ret[] = '        $table->addColumn(\'' . $column->getName() . '\', \'' . $typeName . '\', array(';
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
                $ret[] = '            \'' . $parKey . '\' => ' . $parVal . ',';
            }
            $ret[] = '        ));';
        }

        $ret[] = '';
        $ret[] = '		';

        $indexes = $TableInfo->getindexes();

        foreach ($indexes as $index) {
            if ($index->isPrimary()) {
                $tmpCol = $index->getColumns();
                foreach ($tmpCol as $colName) {
                    $ret[] = '        $table->setPrimaryKey(array(\'' . $colName . '\'));';
                    break;
                }
            } else {
                $ret[] = '        $columnNames = array();';
                foreach ($index->getColumns() as $IdentName) {
                    $ret[] = '        $columnNames[] = \'' . $IdentName . '\';';
                }
                $ret[] = '        $indexName = \'' . $index->getName() . '\';';
                $ret[] = '        $options = ' . var_export($index->getOptions(), true) . ';';
                if ($index->isUnique()) {
                    $ret[] = '        $table->addUniqueIndex($columnNames, $indexName, $options);';
                } else {
                    $ret[] = '        $flags = ' . var_export($index->getFlags(), true) . ';';
                    $ret[] = '        $table->addIndex($columnNames, $indexName,$flags, $options);';
                }
            }
        }

        $ret[] = '    }';

        return join(PHP_EOL, $ret);
    }
}
