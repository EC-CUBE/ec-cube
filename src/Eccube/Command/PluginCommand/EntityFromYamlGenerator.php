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
use Eccube\Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;

class EntityFromYamlGenerator extends AbstractPluginGenerator
{

    const PLUGIN_PREFIX = 'plg_';

    /**
     * Entityリスト
     * @var array
     */
    private $entityList = null;

    protected function getHeader()
    {
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('---Plugin Generator for Entity');
        $this->output->writeln('---[*] You need to create yaml file first.');
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
        $yamlList = $this->paramList['entityList']['value'];

        $codePath = $this->app['config']['root_dir'] . '/app/Plugin/' . $pluginCode;

        $dirList = array('Entity', 'Repository', '/Resource/doctrine/migration');
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
        $metadetas = array();


        $YamlDriver = new YamlDriver(array($codePath . '/Resource/doctrine'));
        foreach ($yamlList as $pathYaml => $fullClassName) {

            $MetadataInfo = new ClassMetadataInfo($fullClassName);
            $YamlDriver->loadMetadataForClass($fullClassName, $MetadataInfo);
            $metadetas[] = $MetadataInfo;

            //Entity作成
            $EntityGenerator = new EntityGenerator();
            $EntityGenerator->setBackupExisting(false);
            $EntityGenerator->setClassToExtend('Eccube\\Entity\\AbstractEntity');
            $EntityGenerator->setGenerateAnnotations(false);
            $EntityGenerator->setRegenerateEntityIfExists(true);
            $EntityGenerator->setGenerateStubMethods(true);
            $EntityGenerator->setUpdateEntityIfExists(false);

            $pathClass = $this->app['config']['root_dir'] . '/app/';
            $EntityGenerator->generate(array($MetadataInfo), $pathClass);
            $pathClass .= str_replace('\\', DIRECTORY_SEPARATOR, $MetadataInfo->name) . '.php';
            if (is_file($pathClass)) {
                $fsList['file'][$pathClass] = true;
            } else {
                $fsList['file'][$pathClass] = false;
            }

            //Repository作成
            $filename = $this->makeRepository($pluginCode, $MetadataInfo);
            if (is_file($filename)) {
                $fsList['file'][$filename] = true;
            } else {
                $fsList['file'][$filename] = false;
            }
        }

        //Migration作成
        if (count($metadetas)) {
            $migrationContent = $this->makeMigration($pluginCode, $metadetas);
            $timeSt = date('YmdHis');
            $migrationContent = str_replace('[datetime]', $timeSt, $migrationContent);
            $migPath = $codePath . '/Resource/doctrine/migration/Version' . $timeSt . '.php';

            file_put_contents($migPath, $migrationContent);
            if (is_file($migPath)) {
                $fsList['file'][$migPath] = true;
            } else {
                $fsList['file'][$migPath] = false;
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

    protected function initFildset()
    {
        $this->paramList = array(
            'pluginCode' => array(
                'no' => 1,
                 'label' => '[+]Plugin Code: ',
                'value' => null,
                'name' => '[+]Please enter Plugin Name (only pascal case letters numbers are allowed)',
                'validation' => array(
                    'isRequired' => true,
                    'inArray' => $this->getPluginList()
                )
            ),
            'entityList' => array(
                'no' => 2,
                'label' => '[+]Yaml file name: ',
                'value' => array(),
                'name' => '[+]Plese enter yaml file name',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => 'getEntityList'
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

    protected function getEntityList()
    {
        if (!$this->paramList['pluginCode']['value']) {
            return array();
        }

        if ($this->entityList === null) {
            $this->entityList = array();
            $directory = $this->app['config']['root_dir'] . '/app/Plugin/' . $this->paramList['pluginCode']['value'] . '/Resource/doctrine/';
            if (!is_dir($directory)) {
                return $this->entityList;
            }
            $dirListing = array_diff(scandir($directory), array('..', '.'));
            $ext = YamlDriver::DEFAULT_FILE_EXTENSION;
            $revCount = strlen($ext) * -1;
            foreach ($dirListing as $fileName) {
                if (substr($fileName, $revCount) == $ext) {
                    $className = str_replace('.', '\\', str_replace($ext, '', $fileName));
                    $this->entityList[$fileName] = $className;
                }
            }
        }

        return $this->entityList;
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

    private function makeRepository($pluginCode, $metadata)
    {
        $nameFormated = ucfirst($this->getShortClassName($metadata->table['name']));

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
        $line[] = ' * ' . $nameFormated;
        $line[] = ' *';
        $line[] = ' * This class was generated by the Doctrine ORM. Add your own custom';
        $line[] = ' * repository methods below.';
        $line[] = ' */';
        $line[] = 'class ' . $nameFormated . 'Repository extends EntityRepository';
        $line[] = '{';
        $line[] = '';
        $line[] = '}';
        $line[] = '';

        $cont = join(PHP_EOL, $line);
        $filename = $this->app['config']['root_dir'] . '/app/Plugin/' . $pluginCode . '/Repository/' . $nameFormated . 'Repository.php';
        file_put_contents($filename, $cont);
        return $filename;
    }

    private function makeMigration($pluginCode, $metadetas)
    {
        if ($this->paramList['supportFlag']['value']) {
            $migrationFileCont = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/MigrationVersionSupport.php');
        } else {
            $migrationFileCont = file_get_contents($this->app['config']['root_dir'] . '/src/Eccube/Command/PluginCommand/Resource/MigrationVersion.php');
        }

        $entityList = $this->createEntityList($pluginCode, $metadetas);

        $entityListStr = join(',' . PHP_EOL, $entityList);
        $migrationFileCont = str_replace('[entityList]', $entityListStr, $migrationFileCont);
        if ($this->paramList['supportFlag']['value']) {
            $createParts = $this->makeCreateParts($metadetas);
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

            $dropParts = $this->makeDropParts($metadetas);
            $dropPartsStr = join(PHP_EOL, $dropParts);
            $migrationFileCont = str_replace('[dropTable]', $dropPartsStr, $migrationFileCont);
        }

        return $migrationFileCont;
    }

    private function createEntityList($pluginCode, $migrations)
    {
        $ret = array();
        foreach ($migrations as $metadata) {
            $ret[] = "        '" . 'Plugin\\' . $pluginCode . '\Entity\\' . ucfirst($this->getShortClassName($metadata->table['name'])) . "'";
        }
        return $ret;
    }

    private function getShortClassName($dbTableName)
    {
        return $this->toCamelCase(str_replace(self ::PLUGIN_PREFIX, '', $dbTableName));
    }

    private function makeCreateParts($metadetas)
    {
        $ret = array();
        foreach ($metadetas as $metadata) {

            $nameFormated = $this->getShortClassName($metadata->table['name']);
            $tmp = array();
            $tmp[] = '';
            $tmp[] = '    public function createTable' . ucfirst($nameFormated) . '(Schema $schema)';
            $tmp[] = '    {';
            $tmp[] = '        $table = $schema->createTable(\'' . $metadata->table['name'] . '\');';
            $columns = $metadata->fieldMappings;
            foreach ($columns as $column) {

                $typeName = $column['type'];
                $tmp[] = '        $table->addColumn(\'' . $column['columnName'] . '\', \'' . $typeName . '\', array(';
                $param = array();
                if (isset($column['nullable']) && $column['nullable']) {
                    $param['notnull'] = 'true';
                } else {
                    $param['notnull'] = 'false';
                }

                foreach ($param as $parKey => $parVal) {
                    $tmp[] = '            \'' . $parKey . '\' => ' . $parVal . ',';
                }
                $tmp[] = '        ));';
            }


            $tmp[] = '    }';
            $tmp[] = '';
            $ret[ucfirst($nameFormated)] = $tmp;
        }

        return $ret;
    }

    private function makeDropParts($metadetas)
    {
        $ret = array();
        foreach ($metadetas as $metadata) {
            $ret[] = '            $schema->dropTable(\'' . $metadata->table['name'] . '\');';
        }

        return $ret;
    }
}
