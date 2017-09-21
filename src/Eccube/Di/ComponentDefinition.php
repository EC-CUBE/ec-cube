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

namespace Eccube\Di;


class ComponentDefinition
{

    /**
     * Component ID
     * @var string
     */
    private $id;

    /**
     * Component Class
     * @var \ReflectionClass
     */
    private $refClass;

    /**
     * Dependencies
     * @var Dependency[]
     */
    private $dependencies = [];

    /**
     * ComponentDefinition constructor.
     * @param $id
     * @param $refClass
     */
    public function __construct($id, $refClass)
    {
        $this->id = $id;
        $this->refClass = $refClass;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \ReflectionClass
     */
    public function getRefClass()
    {
        return $this->refClass;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->refClass->getName();
    }

    /**
     * @return Dependency[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function addDenendency(Dependency $dependency)
    {
        $this->dependencies[] = $dependency;
    }
}