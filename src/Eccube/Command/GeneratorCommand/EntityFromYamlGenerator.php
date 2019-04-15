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

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Eccube\Doctrine\ORM\Mapping\Driver\YamlDriver;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class EntityFromYamlGenerator extends AbstractPluginGenerator
{

    /**
     * Yamlリスト
     *
     * @var array
     */
    private $ymlList = null;

    protected function getHeader()
    {
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('---Plugin Generator for Entity');
        $this->output->writeln('---[*]You need to create yml file first.');
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
            'ymlList' => array(
                'no' => 2,
                'label' => '[+]Yml file name: ',
                'value' => array(),
                'name' => '[+]Plese enter yml file name',
                'validation' => array(
                    'isRequired' => false,
                    'inArray' => 'getYamlList',
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

    protected function getYamlList()
    {

        $pluginCode = $this->paramList['pluginCode']['value'];

        if (!$pluginCode) {
            return array();
        }

        if ($this->ymlList === null) {
            $this->ymlList = array();

            $finder = new Finder();
            $finder->files()->depth('== 0');
            $files = $finder->in($this->app['config']['root_dir'].'/app/Plugin/'.$pluginCode.'/Resource/doctrine/');

            foreach ($files as $item) {
                $this->ymlList[$item->getFilename()] = $item->getRealPath();
            }

        }

        return $this->ymlList;
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

        $pluginCode = $this->paramList['pluginCode']['value'];
        $ymlList = $this->paramList['ymlList']['value'];

        $codePath = $this->app['config']['root_dir'].'/app/Plugin/'.$pluginCode;

        $dirList = array('Entity', 'Repository', 'Resource/doctrine/migration');
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

        // metadataの設定
        $classNames = array();
        foreach ($ymlList as $item) {
            $yml = Yaml::parse(file_get_contents($item));
            $classNames[] = key($yml);
        }

        $YamlDriver = new YamlDriver(array($codePath.'/Resource/doctrine'));
        $metadatas = array();
        foreach ($classNames as $className) {
            $ClassMetadataInfo = new ClassMetadataInfo($className);
            $YamlDriver->loadMetadataForClass($className, $ClassMetadataInfo);
            $metadatas[] = $ClassMetadataInfo;
        }

        // Entity、Repositoryファイルの作成
        $this->generateEntities($metadatas, $fsList);

        // migrationファイルの作成
        $this->generateMigration($metadatas, $fsList, $pluginCode, $codePath);

        // 完了メッセージ
        $this->completeMessage($fsList);

    }
}
