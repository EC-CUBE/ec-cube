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

namespace Eccube\Doctrine\ORM\Mapping\Driver;

use Symfony\Component\Yaml\Yaml;

/**
 * The YamlDriver reads the mapping metadata from yaml schema files.
 *
 * YamlDriverのPHP7対応. Doctrine2.4で修正されれば不要.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/1338
 * @package Eccube\Doctrine\ORM\Mapping\Driver
 */
class YamlDriver extends \Doctrine\ORM\Mapping\Driver\YamlDriver
{
    /**
     * {@inheritDoc}
     */
    protected function loadMappingFile($file)
    {
        return Yaml::parse(file_get_contents($file));
    }

    /*
     * 以下、エンティティ拡張機構
     *
     * Copyright (c) by Paulius Jarmalavicius
     * Released under the MIT license
     * https://opensource.org/licenses/mit-license.php
     */

    /**
     * @var array
     */
    protected $extendedEntities = array();

    /**
     * 継承元のエンティティを追加する
     *
     * @param string $extendedEntity
     * @return $this
     */
    public function addExtendedEntity($extendedEntity)
    {
        if (!in_array($extendedEntity, $this->getExtendedEntities(), true)) {
            $this->extendedEntities[] = $extendedEntity;
        }
        return $this;
    }

    /**
     * 継承元エンティティの配列を取得する
     *
     * @return array
     */
    public function getExtendedEntities()
    {
        return $this->extendedEntities;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClassNames()
    {
        $driver = $this;
        $classNames = parent::getAllClassNames();
        $filter = function ($className) use ($driver) {
            return !in_array($className, $driver->getExtendedEntities(), true);
        };
        return array_filter($classNames, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function isTransient($className)
    {
        return parent::isTransient($className) || in_array($className, $this->getExtendedEntities(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function getElement($className)
    {
        $result = parent::getElement($className);
        if (isset($result['extended_entity'])) {
            $extendedElement = $this->getElement($result['extended_entity']);
            unset($result['extended_entity']);
            $result = $this->mergeMappings($extendedElement, $result);
        }
        return $result;
    }

    /**
     * @param array $mapping1
     * @param array $mapping2
     * @return array
     */
    protected function mergeMappings(array &$mapping1, array &$mapping2)
    {
        $merged = $mapping1;
        foreach ($mapping2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->mergeMappings($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}