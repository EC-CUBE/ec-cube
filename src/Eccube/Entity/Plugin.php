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


namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plugin
 */
class Plugin extends AbstractEntity
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

    // local property
    /**
     * @var string
     */
    private $update_status;

    /**
     * @var string
     */
    private $new_version;

    /**
     * @var string
     */
    private $last_update_date;

    /**
     * @var string
     */
    private $product_url;

    /**
     * @var array
     */
    private $eccube_version;

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
     * @return Plugin
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
     * @param integer $source
     * @return Plugin
     */
    public function setSource($source)
    {
        $this->source = $source;

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

    /**
     * Set update_status
     *
     * @param string $updateStatus
     * @return Plugin
     */
    public function setUpdateStatus($updateStatus)
    {
        $this->update_status = $updateStatus;

        return $this;
    }

    /**
     * Get update_status
     *
     * @return string
     */
    public function getUpdateStatus()
    {
        return $this->update_status;
    }

    /**
     * Set new_version
     *
     * @param string $newVersion
     * @return Plugin
     */
    public function setNewVersion($newVersion)
    {
        $this->new_version = $newVersion;

        return $this;
    }

    /**
     * Get new_version
     *
     * @return string
     */
    public function getNewVersion()
    {
        return $this->new_version;
    }

    /**
     * Set last_update_date
     *
     * @param string $lastUpdateDate
     * @return Plugin
     */
    public function setLastUpdateDate($lastUpdateDate)
    {
        $this->last_update_date = $lastUpdateDate;

        return $this;
    }

    /**
     * Get last_update_date
     *
     * @return string
     */
    public function getLastUpdateDate()
    {
        return $this->last_update_date;
    }

    /**
     * Set product_url
     *
     * @param string $productUrl
     * @return Plugin
     */
    public function setProductUrl($productUrl)
    {
        $this->product_url = $productUrl;

        return $this;
    }

    /**
     * Get product_url
     *
     * @return string
     */
    public function getProductUrl()
    {
        return $this->product_url;
    }

    /**
     * Set eccube_version
     *
     * @param array $eccube_version
     * @return Plugin
     */
    public function setEccubeVersion($eccube_version)
    {
        $this->eccube_version = $eccube_version;

        return $this;
    }

    /**
     * Get eccube_version
     *
     * @return array
     */
    public function getEccubeVersion()
    {
        return $this->eccube_version;
    }

    public function getEccubeVersionAsString()
    {
        return implode(', ', $this->getEccubeVersion());
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
