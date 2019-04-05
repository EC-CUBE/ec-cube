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

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\Driver\DatabaseDriver;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Symfony\Component\Finder\Finder;

class EntityFromDbGenerator extends AbstractPluginGenerator
{

    /**
     * テーブルリスト
     *
     * @var array
     */
    private $tableList = null;

    protected function getHeader()
    {
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('---Plugin Generator for Entity');
        $this->output->writeln('---[*]You need to create table schema first.');
        $this->output->writeln('---[*]You can exit from Console Application, by typing '.self::STOP_PROCESS.' instead of typing another word.');
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('');
    }

    protected function initFieldSet()
    {
        $this->paramList = array(
            'pluginCode' => array(
                'no' => 1,
                'label' => '[+]Plugin Code: ',
                'value' => null,
                'name' => '[+]Please enter Plugin Code (First letter is uppercase alphabet only. alphabet and numbers are allowed.)',
                'validation' => array(
                    'isRequired' => true,
                    'isNotCode' => $this->getPluginCodes(),
                )
            ),
            'tableList' => array(
                'no' => 2,
                'label' => '[+]Table name: ',
                'value' => array(),
                'name' => '[+]Please enter table name',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => $this->getTableList(),
                )
            ),
            'supportFlag' => array(
                'no' => 3,
                'label' => '[+]Old version support: ',
                'value' => null,
                'name' => '[+]Do you want to support old versions too? [y/n]',
                'show' => array(1 => 'Yes', 0 => 'No'),
                'validation' => array(
                    'isRequired' => true,
                    'choice' => array('y' => 1, 'n' => 0),
                )
            )
        );
    }

    /**
     * プラグイン用テーブル一覧(plg_xxxx)の取得
     *
     * @return array
     */
    protected function getTableList()
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

    protected function start()
    {
        $pluginCode = $this->paramList['pluginCode']['value'];

        $codes = $this->getPluginCodes();
        if (!in_array($pluginCode, $codes)) {
            $this->exitGenerator('<error>This plugin code does not exist.</error>');

            return;
        }

        $fsList = array(
            'dir' => array(),
            'file' => array(),
        );

        $tableList = $this->paramList['tableList']['value'];
        $codePath = $this->app['config']['root_dir'].'/app/Plugin/'.$pluginCode;

        $dirList = array('Entity', 'Repository', 'Resource', 'Resource/doctrine', 'Resource/doctrine/migration');
        foreach ($dirList as $dirName) {
            $dirPath = $codePath.'/'.$dirName;
            if (!is_dir($dirPath)) {
                mkdir($dirPath);
            }
            if (is_dir($dirPath)) {
                $fsList['dir'][$dirPath] = true;
            } else {
                $fsList['dir'][$dirPath] = false;
            }
        }

        $doctrinePath = $codePath.'/Resource/doctrine';

        if (count($tableList)) {

            // ymlファイルの作成
            $em = $this->app['orm.em'];

            $databaseDriver = new DatabaseDriver(
                $em->getConnection()->getSchemaManager()
            );

            $em->getConfiguration()->setMetadataDriverImpl(
                $databaseDriver
            );

            $databaseDriver->setNamespace('Plugin\\'.$pluginCode.'\\Entity\\');

            $cmf = new DisconnectedClassMetadataFactory();
            $cmf->setEntityManager($em);
            $metadatas = $cmf->getAllMetadata();

            $filters = array_map(function ($value) {
                return ucfirst(Inflector::camelize(str_replace('plg_', '', $value)));
            }, $tableList);
            $metadatas = MetadataFilter::filter($metadatas, $filters);

            $cme = new ClassMetadataExporter();
            $exporter = $cme->getExporter('yml', $doctrinePath);

            /** @var ClassMetadataInfo $class */
            foreach ($metadatas as $class) {
                $class->name = str_replace('Plg', '', $class->name);
                $class->rootEntityName = str_replace('Plg', '', $class->rootEntityName);
                $name = explode('\\', $class->name);
                $class->customRepositoryClassName = 'Plugin\\'.$pluginCode.'\\Repository\\'.$name[count($name) - 1].'Repository';
                $class->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);
            }

            $exporter->setMetadata($metadatas);
            $exporter->export();

            $finder = new Finder();
            $finder->files()->depth('== 0');
            $files = $finder->in($doctrinePath);

            foreach ($files as $item) {
                $fsList['file'][$item->getRealPath()] = true;
            }

            // Entity、Repositoryファイルの作成
            $this->generateEntities($metadatas, $fsList);

            // migrationファイルの作成
            $this->generateMigration($metadatas, $fsList, $pluginCode, $codePath);

            // 完了メッセージ
            $this->completeMessage($fsList);
        }

    }

}
