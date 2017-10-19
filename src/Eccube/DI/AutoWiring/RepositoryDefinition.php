<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\DI\AutoWiring;


use Eccube\DI\ComponentDefinition;

class RepositoryDefinition extends ComponentDefinition
{
    private $entityClass;

    /**
     * RepositoryDefinition constructor.
     * @param $id
     * @param $refClass
     * @param $entityClass
     */
    public function __construct($id, $refClass, $entityClass)
    {
        parent::__construct($id, $refClass);
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        // Entityが指定されいれば返す
        if ($this->entityClass) {
            return $this->entityClass;
        }

        $class = $this->getRefClass()->getName();

        $partsOfFqcn = explode('\\', $class);

        // 同じパッケージのEntityがあれば返す
        if (count($partsOfFqcn) > 2) {
            $parentNamespace = implode('\\', array_slice($partsOfFqcn, 0, count($partsOfFqcn)-2));
            $entityName = preg_replace('/(.*)Repository/', '$1', $partsOfFqcn[count($partsOfFqcn) -1]);
            $result = $parentNamespace . '\\Entity\\' . $entityName;
            if (class_exists($result)) {
                return $result;
            }
        }

        // Eccube\Entity以下のEntityを返す
        $ns = 'Eccube\\Entity\\';
        if (strpos($class, 'Master') !== false) {
            $ns .= 'Master\\';
        }

        $array = explode('\\', $class);
        $name = end($array);
        $name = str_replace('Repository', '', $name);
        $entity = $ns.$name;

        return $entity;
    }
}