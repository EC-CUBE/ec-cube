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

    /**
     * @var array
     */
    protected $extendedEntities = array();

    /**
     * Setter for extendedEntities.
     *
     * @param string $extendedEntity
     *
     * @return $this
     */
    public function addExtendedEntity($extendedEntity)
    {
        if (!in_array($extendedEntity, $this->extendedEntities, true)) {
            $this->extendedEntities[] = $extendedEntity;
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames()
    {
        $driver = $this;
        $classNames = parent::getAllClassNames();
        return array_filter(
            $classNames,
            function ($className) use ($driver) {
                return !in_array($className, $driver->extendedEntities, true);
            }
        );
    }

    /**
     * Returns whether the class with the specified name is transient. Only non-transient
     * classes, that is entities and mapped superclasses, should have their metadata loaded.
     *
     * A class is non-transient if it is annotated with an annotation
     * from the {@see AnnotationDriver::entityAnnotationClasses}.
     *
     * @param string $className
     *
     * @return boolean
     */
    public function isTransient($className)
    {
        $isTransient = parent::isTransient($className);
        if (!$isTransient && in_array($className, $this->extendedEntities, true)) {
            $isTransient = true;
        }
        return $isTransient;
    }

    /**
     * Gets the element of schema meta data for the class from the mapping file.
     * This will lazily load the mapping file if it is not loaded yet.
     *
     * Overridden in order to merger mapping with parent class if 'extended_entity' is provided.
     *
     * @param string $className
     *
     * @return array The element of schema meta data.
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
     * Merges mappings recursively and overrides duplicated values with second mappings values.
     *
     * @param array $mapping1
     * @param array $mapping2
     *
     * @return array
     */
    protected function mergeMappings(array &$mapping1, array &$mapping2)
    {
        $merged = $mapping1;
        foreach ($mapping2 as $key => &$value) {
            if (is_array ($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->mergeMappings($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}