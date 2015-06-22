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


namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plugin
 */
class Plugin
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $class_name;

    /**
     * @var integer
     */
    private $enable;

    /**
     * @var string
     */
    private $version;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var integer
     */
    private $source;


    private $update_date;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $PluginEventHandlers;


    public function __construct()
    {
        $this->PluginEventHandlers = new \Doctrine\Common\Collections\ArrayCollection();

    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Plugin
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Plugin
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set class_name
     *
     * @param string $className
     * @return Plugin
     */
    public function setClassName($className)
    {
        $this->class_name = $className;

        return $this;
    }

    /**
     * Get class_name
     *
     * @return string 
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Set enable
     *
     * @param integer $enable
     * @return Plugin
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;

        return $this;
    }

    /**
     * Get enable
     *
     * @return integer 
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return Plugin
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Plugin
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return Plugin
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return PluginEventHandler
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set source
     *
     * @param integer $delFlg
     * @return PluginEventHandler
     */
    public function setSource($delFlg)
    {
        $this->source = $delFlg;

        return $this;
    }

    /**
     * Get source
     *
     * @return integer
     */
    public function getSource()
    {
        return $this->source;
    }

    public function getPluginEventHandlers()
    {
        return $this->PluginEventHandlers;
    }
    public function addPluginEventHandler(\Eccube\Entity\PluginEventHandler $PluginEventHandler)
    {
        $this->PluginEventHandlers[] = $PluginEventHandler;
        return $this;
    }
    public function removePluginEventHandler(\Eccube\Entity\PluginEventHandler $PluginEventHandler)
    {
        $this->PluginEventHandlers->removeElement($PluginEventHandler);
        return $this;
    }
}
